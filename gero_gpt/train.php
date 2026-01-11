<?php
// Ensure session is started at the very beginning to handle messages consistently
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'db/config.php'; // This provides $conn (mysqli) and $pdo (PDO)

$mediaDir = __DIR__ . "/media/";
// Define allowed extensions and their corresponding MIME types.
// This is a strict whitelist. Add more as needed, but always verify their safety.
$allowed = [
    'image' => [
        'extensions' => ['jpg', 'png', 'jpeg', 'gif', 'webp'],
        'mime_types' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
    ],
    'video' => [
        'extensions' => ['mp4', 'webm', 'ogg'],
        'mime_types' => ['video/mp4', 'video/webm', 'video/ogg']
    ],
    'audio' => [
        'extensions' => ['mp3', 'wav', 'ogg'],
        'mime_types' => ['audio/mpeg', 'audio/wav', 'audio/ogg'] // audio/mpeg for mp3
    ]
];

// Initialize arrays for feedback messages
$uploaded_success = [];
$uploaded_errors = [];

// Function to log errors for debugging (for your XAMPP server error log)
function log_upload_error($message)
{
    error_log("Train.php Upload Error: " . $message);
}

// Bulk upload handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media'])) {
    // Define the Deepfake faces directory
    $deepfakeFacesDir = __DIR__ . '/gero_gpt/deepfake_input/faces/';
    
    // Ensure the Deepfake faces directory exists
    if (!is_dir($deepfakeFacesDir)) {
        if (!mkdir($deepfakeFacesDir, 0755, true)) {
            // PATCH: Added error_get_last() to the displayed error message
            $mkdirError = error_get_last();
            $uploaded_errors[] = "Failed to create deepfake faces directory: " . htmlspecialchars($deepfakeFacesDir) . " (System Error: " . htmlspecialchars($mkdirError['message'] ?? 'Unknown') . ")";
            log_upload_error("Failed to create deepfake faces directory: " . $deepfakeFacesDir . " System Error: " . ($mkdirError['message'] ?? 'Unknown'));
            // Consider exiting or disabling deepfake copy if this is critical
        }
    }

    foreach ($_FILES['media']['tmp_name'] as $i => $tmpName) {
        $originalName = $_FILES['media']['name'][$i];
        $fileMimeType = $_FILES['media']['type'][$i]; // Get MIME type from $_FILES
        $php_error_code = $_FILES['media']['error'][$i];
        $file_error_message = ''; // To store specific error for this file

        // Check for initial PHP upload errors
        if ($php_error_code !== UPLOAD_ERR_OK) {
            switch ($php_error_code) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $file_error_message = "File exceeds max upload size (" . ini_get('upload_max_filesize') . ").";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $file_error_message = "File was only partially uploaded.";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $file_error_message = "No file was selected.";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $file_error_message = "Missing a temporary folder.";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $file_error_message = "Failed to write file to disk.";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $file_error_message = "A PHP extension stopped the file upload.";
                    break;
                default:
                    $file_error_message = "Unknown upload error (Code: {$php_error_code}).";
                    break;
            }
            $uploaded_errors[] = "$originalName: " . $file_error_message;
            log_upload_error("$originalName: PHP upload error - " . $file_error_message);
            continue; // Skip to next file
        }

        // Proceed if no initial PHP error
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $type = null;
        $isValidMime = false;

        foreach ($allowed as $t => $data) {
            if (in_array($ext, $data['extensions'])) {
                // Check if the uploaded MIME type is also allowed for this extension type
                if (in_array($fileMimeType, $data['mime_types'])) {
                    $type = $t;
                    $isValidMime = true;
                    break; // Found type and valid MIME, no need to check other extensions
                }
            }
        }

        if (!$type || !$isValidMime) {
            $uploaded_errors[] = "$originalName: Invalid file type, extension, or MIME type ({$fileMimeType}).";
            log_upload_error("$originalName: Invalid file type, extension, or MIME type ({$fileMimeType}).");
            continue; // Skip to next file
        }

        // Additional server-side MIME type verification using finfo (more reliable)
        // This requires the fileinfo extension to be enabled in php.ini
        if (class_exists('finfo')) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $realMimeType = $finfo->file($tmpName);
            $isRealMimeValid = false;
            foreach ($allowed[$type]['mime_types'] as $allowedMime) {
                if (strpos($realMimeType, $allowedMime) === 0) { // Check for starts with (e.g., image/jpeg can be image/jpeg; charset=binary)
                    $isRealMimeValid = true;
                    break;
                }
            }

            if (!$isRealMimeValid) {
                $uploaded_errors[] = "$originalName: Detected file type ({$realMimeType}) does not match allowed types.";
                log_upload_error("$originalName: Detected file type ({$realMimeType}) does not match allowed types.");
                continue;
            }
        } else {
            log_upload_error("Fileinfo extension not enabled. MIME type verification is less strict.");
        }


        $targetDir = $mediaDir . $type . "s/";

        // Create directory if it doesn't exist
        if (!is_dir($targetDir)) {
            // Using 0755 for production is generally safer than 0777
            if (!mkdir($targetDir, 0755, true)) {
                $uploaded_errors[] = "$originalName: Failed to create target directory.";
                log_upload_error("$originalName: Failed to create target directory: " . $targetDir);
                continue; // Skip to next file
            }
        }

        // --- SECURITY IMPROVEMENT: Generate a truly unique and random filename ---
        // This prevents filename collision and potential issues if original filename contained malicious content.
        $newFilename = uniqid('', true) . '.' . $ext; // Add a microtime unique ID
        $finalFilename = $newFilename;
        $targetPath = $targetDir . $finalFilename;

        if (move_uploaded_file($tmpName, $targetPath)) {
            // --- NEW LOGIC START: Copy image to deepfake_input/faces if it's an image ---
            if ($type === 'image') {
                $deepfakeTargetPath = $deepfakeFacesDir . $finalFilename;
                if (!copy($targetPath, $deepfakeTargetPath)) {
                    // PATCH: Added error_get_last() to the displayed error message
                    $copyError = error_get_last();
                    $uploaded_errors[] = "$originalName: Successfully uploaded to media/images, but FAILED to copy to deepfake_input/faces. (System Error: " . htmlspecialchars($copyError['message'] ?? 'Unknown') . "). Please check permissions of {$deepfakeFacesDir}.";
                    log_upload_error("$originalName: Failed to copy image to deepfake_input/faces from $targetPath to $deepfakeTargetPath. Error: " . ($copyError['message'] ?? 'Unknown'));
                    // Continue processing, as the primary upload was successful
                }
            }
            // --- NEW LOGIC END ---

            $filenameForDb = basename($targetPath); // Use the final unique filename for DB
            $size = filesize($targetPath); // Re-get size after move

            // Insert into media table
            // Changed 'created_at' to 'uploaded_at'
            $stmt = $conn->prepare("INSERT INTO media (filename, type, label, tags, uploaded_at) VALUES (?, ?, '', '', NOW())");
            if ($stmt === false) {
                $uploaded_errors[] = "$filenameForDb: Database error (media table prepare failed).";
                log_upload_error("$filenameForDb: Database error (media table prepare failed): " . $conn->error);
                unlink($targetPath); // Clean up uploaded file if DB insert fails
                // Also clean up deepfake copy if it was made
                if ($type === 'image' && file_exists($deepfakeTargetPath)) {
                    unlink($deepfakeTargetPath);
                }
                continue;
            }
            $stmt->bind_param("ss", $filenameForDb, $type);
            if (!$stmt->execute()) {
                $uploaded_errors[] = "$filenameForDb: Database error (media table execute failed).";
                log_upload_error("$filenameForDb: Database error (media table execute failed): " . $stmt->error);
                unlink($targetPath); // Clean up uploaded file if DB insert fails
                // Also clean up deepfake copy if it was made
                if ($type === 'image' && file_exists($deepfakeTargetPath)) {
                    unlink($deepfakeTargetPath);
                }
                continue;
            }
            $stmt->close();

            // Insert into generation_log table
            $prompt = pathinfo($filenameForDb, PATHINFO_FILENAME); // Prompt from the unique filename
            $path = "media/{$type}s/" . $filenameForDb; // Path as stored in generation_log

            $stmt2 = $conn->prepare("INSERT INTO generation_log (filename, type, prompt, generated_at) VALUES (?, ?, ?, NOW())");
            if ($stmt2 === false) {
                $uploaded_errors[] = "$filenameForDb: Database error (generation_log table prepare failed).";
                log_upload_error("$filenameForDb: Database error (generation_log table prepare failed): " . $conn->error);
                // The file is already in media directory, we might want to unlink it again if generation_log fails
                // It depends on whether generation_log is critical for keeping the file.
                // For simplicity, we'll continue, but a robust system might clean up.
                continue;
            }
            $stmt2->bind_param("sss", $path, $type, $prompt);
            if (!$stmt2->execute()) {
                $uploaded_errors[] = "$filenameForDb: Database error (generation_log table execute failed).";
                log_upload_error("$filenameForDb: Database error (generation_log table execute failed): " . $stmt2->error);
                continue;
            }
            $stmt2->close();

            $uploaded_success[] = $filenameForDb; // Add to success list
        } else {
            // Check why move_uploaded_file failed
            if (!is_writable($targetDir)) {
                $uploaded_errors[] = "$originalName: Target directory not writable: " . $targetDir;
                log_upload_error("$originalName: Target directory not writable: " . $targetDir);
            } else {
                $uploaded_errors[] = "$originalName: Failed to move uploaded file. Check permissions/disk space.";
                log_upload_error("$originalName: Failed to move uploaded file from $tmpName to $targetPath. Error: " . error_get_last()['message']);
            }
        }
    }
    // Redirect to self to clear POST data and show messages
    // Use URL parameters to pass messages if any
    $redirect_params = [];
    if (!empty($uploaded_success)) {
        $redirect_params['upload_success'] = count($uploaded_success);
    }
    if (!empty($uploaded_errors)) {
        $redirect_params['upload_errors'] = count($uploaded_errors);
        // Store detailed error messages in session to avoid long URL
        $_SESSION['upload_error_details'] = $uploaded_errors;
    }
    header("Location: train.php?" . http_build_query($redirect_params));
    exit;
}

// Inline label/tag update
if (isset($_POST['update_field'])) {
    $field = $_POST['field'];
    $value = $_POST['value'];
    // Validate filename to prevent path traversal issues.
    // Ensure it's just a filename, not a path.
    $file = basename($_POST['file']);

    if (in_array($field, ['label', 'tags'])) {
        $stmt = $conn->prepare("UPDATE media SET $field = ? WHERE filename = ?");
        if ($stmt) {
            $stmt->bind_param("ss", $value, $file);
            $stmt->execute();
            $stmt->close();
        } else {
            // Log error if prepare failed
            log_upload_error("Update field prepare failed: " . $conn->error);
        }
    }
    exit; // Important to exit for AJAX requests
}

// Bulk delete
if (isset($_POST['bulk_delete']) && isset($_POST['files']) && is_array($_POST['files'])) {
    foreach ($_POST['files'] as $file) {
        $safeFile = basename($file); // Always sanitize input to prevent path traversal
        // First, retrieve the type from the database to construct the correct path
        $stmt_get_type = $conn->prepare("SELECT type FROM media WHERE filename = ?");
        if ($stmt_get_type) {
            $stmt_get_type->bind_param("s", $safeFile);
            $stmt_get_type->execute();
            $result_type = $stmt_get_type->get_result();
            if ($row_type = $result_type->fetch_assoc()) {
                $fileType = $row_type['type'];
                $pathToDelete = $mediaDir . $fileType . "s/" . $safeFile;

                if (file_exists($pathToDelete) && is_file($pathToDelete)) { // Check if it's a file before unlinking
                    if (!unlink($pathToDelete)) {
                        log_upload_error("Failed to delete file from disk: " . $pathToDelete);
                    }
                }

                // --- NEW LOGIC START: Delete from deepfake_input/faces if it's an image ---
                if ($fileType === 'image') {
                    $deepfakeFacePathToDelete = __DIR__ . '/gero_gpt/deepfake_input/faces/' . $safeFile;
                    if (file_exists($deepfakeFacePathToDelete) && is_file($deepfakeFacePathToDelete)) {
                        if (!unlink($deepfakeFacePathToDelete)) {
                            log_upload_error("Failed to delete image from deepfake_input/faces: " . $deepfakeFacePathToDelete);
                        }
                    }
                }
                // --- NEW LOGIC END ---
            }
            $stmt_get_type->close();
        } else {
            log_upload_error("Prepare statement failed to get file type for deletion: " . $conn->error);
        }

        // Delete from media table
        $stmt_media = $conn->prepare("DELETE FROM media WHERE filename = ?");
        if ($stmt_media) {
            $stmt_media->bind_param("s", $safeFile);
            $stmt_media->execute();
            $stmt_media->close();
        } else {
            log_upload_error("Delete from media prepare failed: " . $conn->error);
        }

        // Delete from generation_log table
        // Use the exact filename from media, but for generation_log, the filename might be stored as a full path 'media/type/filename.ext'
        // So, we use LIKE for robustness, but still make sure $safeFile is just the filename.
        $stmt_log = $conn->prepare("DELETE FROM generation_log WHERE filename LIKE ?");
        if ($stmt_log) {
            $logFilenamePattern = "%/" . $safeFile; // Matches 'media/images/myimage.jpg' or 'media/videos/myvideo.mp4'
            $stmt_log->bind_param("s", $logFilenamePattern);
            $stmt_log->execute();
            $stmt_log->close();
        } else {
            log_upload_error("Delete from generation_log prepare failed: " . $conn->error);
        }
    }
    header("Location: train.php?bulk_delete_status=success"); // Redirect after bulk action
    exit;
}

// Bulk tagging
if (isset($_POST['bulk_tag']) && isset($_POST['files']) && is_array($_POST['files']) && isset($_POST['new_tag'])) {
    $newTag = trim($_POST['new_tag']);
    if (!empty($newTag)) {
        // Use prepared statement for bulk tag
        // Use COALESCE to handle cases where 'tags' might be NULL
        $stmt_tag = $conn->prepare("UPDATE media SET tags = TRIM(BOTH ',' FROM CONCAT_WS(',', COALESCE(tags, ''), ?)) WHERE filename = ?");
        if ($stmt_tag) {
            foreach ($_POST['files'] as $file) {
                $safeFile = basename($file); // Sanitize filename
                $stmt_tag->bind_param("ss", $newTag, $safeFile);
                $stmt_tag->execute();
            }
            $stmt_tag->close();
        } else {
            log_upload_error("Bulk tag prepare failed: " . $conn->error);
        }
    }
    header("Location: train.php?bulk_tag_status=success"); // Redirect after bulk action
    exit;
}

// Export media metadata as CSV
if (isset($_GET['export'])) {
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=media_export.csv");
    // Ensure no other output before headers
    ob_clean(); // Clean output buffer

    // Changed 'created_at' to 'uploaded_at'
    $res = $conn->query("SELECT filename, type, label, tags, uploaded_at FROM media");

    // Output UTF-8 Byte Order Mark (BOM) for Excel compatibility with non-ASCII characters
    echo "\xEF\xBB\xBF";

    // Output CSV headers - Changed 'created_at' to 'uploaded_at'
    echo "filename,type,label,tags,uploaded_at\n";

    while ($row = $res->fetch_assoc()) {
        // Enclose fields in double quotes to handle commas within data
        // And escape existing double quotes
        // Changed 'created_at' to 'uploaded_at'
        $line = [
            str_replace('"', '""', $row['filename']),
            str_replace('"', '""', $row['type']),
            str_replace('"', '""', $row['label']),
            str_replace('"', '""', $row['tags']),
            str_replace('"', '""', $row['uploaded_at'])
        ];
        echo '"' . implode('","', $line) . "\"\n";
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Train Media</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .preview { margin: 8px 0; }
        .preview:hover { outline: 2px solid #ccc; }
        td[contenteditable] { background: #fefbd8; cursor: pointer; }
        #dropArea {
            border: 2px dashed #999;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            background-color: #fafafa;
        }
        .message-container { margin-bottom: 20px; }
        .message { padding: 10px; margin-bottom: 10px; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    </style>
    <script>
        function handleDragOver(e) {
            e.preventDefault();
            document.getElementById('dropArea').style.background = '#eef';
        }

        function handleDrop(e) {
            e.preventDefault();
            const input = document.getElementById('mediaInput');
            const dt = new DataTransfer();
            for (const file of e.dataTransfer.files) {
                dt.items.add(file);
            }
            input.files = dt.files;
            showPreview(input);
            document.getElementById('dropArea').style.background = '#fafafa';
        }

        function showPreview(input) {
            const container = document.getElementById('previewArea');
            container.innerHTML = '';
            ([...input.files]).forEach(file => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    let el;
                    if (file.type.includes('image')) {
                        el = document.createElement('img');
                    } else if (file.type.includes('video')) {
                        el = document.createElement('video');
                        el.controls = true;
                    } else if (file.type.includes('audio')) {
                        el = document.createElement('audio');
                        el.controls = true;
                    }
                    if (el) {
                        el.src = e.target.result;
                        el.width = 150;
                        el.className = 'preview';
                        container.appendChild(el);
                    }
                };
                reader.readAsDataURL(file);
            });
        }

        // Function to handle inline field updates (label/tags)
        function updateField(td, field, filename) {
            fetch('', { // Post to the same page
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `update_field=1&field=${field}&value=${encodeURIComponent(td.innerText)}&file=${filename}`
            })
            .then(response => {
                if (!response.ok) {
                    console.error('Network response was not ok ' + response.statusText);
                    // Optionally show a client-side error message
                }
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
                // Optionally show a client-side error message
            });
        }

        // Clear messages after a few seconds
        document.addEventListener('DOMContentLoaded', () => {
            const messages = document.querySelectorAll('.message');
            messages.forEach(msg => {
                setTimeout(() => {
                    msg.remove();
                }, 5000); // Remove message after 5 seconds
            });
        });

    </script>
</head>
<body ondragover="handleDragOver(event)" ondrop="handleDrop(event)">
    <h1>📂 Train / Upload Media</h1>
    <nav>
        <a href="index.html">🧠 Chat & Generate</a>
        <a href="deepfake.php">🧠 Deepfake videos</a> <a href="train.php">📂 Train / Manage Media</a>
        <a href="generated_output.php">🎨 Generated Output</a>
        <a href="help.html">❓ Help</a>
    </nav>

    <div class="message-container">
        <?php
        // Display upload messages
        if (isset($_GET['upload_success'])) {
            echo '<div class="message success">' . htmlspecialchars($_GET['upload_success']) . ' file(s) uploaded successfully!</div>';
        }
        if (isset($_GET['upload_errors']) && isset($_SESSION['upload_error_details'])) {
            echo '<div class="message error"><strong>Upload Errors:</strong><br>';
            foreach ($_SESSION['upload_error_details'] as $errorMsg) {
                echo ' - ' . htmlspecialchars($errorMsg) . '<br>';
            }
            echo '</div>';
            unset($_SESSION['upload_error_details']); // Clear session variable after display
        }

        // Display bulk action messages
        if (isset($_GET['bulk_delete_status']) && $_GET['bulk_delete_status'] == 'success') {
            echo '<div class="message info">Selected files deleted successfully.</div>';
        }
        if (isset($_GET['bulk_tag_status']) && $_GET['bulk_tag_status'] == 'success') {
            echo '<div class="message info">Selected files tagged successfully.</div>';
        }
        ?>
    </div>

    <h2>📤 Bulk Upload Media</h2>
    <form method="post" enctype="multipart/form-data" id="dropzone">
        <input type="file" name="media[]" id="mediaInput" multiple style="display:none" onchange="showPreview(this)">
        <div id="dropArea" onclick="document.getElementById('mediaInput').click()">
            Drag & Drop files here or click to select
        </div>
        <br>
        <button type="submit">Upload All</button>
        <div id="previewArea"></div>
    </form>

    <h2>📑 Manage Uploaded Media</h2>
    <form method="get">
        <input type="text" name="search" placeholder="Search label or tags..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <select name="type">
            <option value="">All Types</option>
            <option value="image" <?= (($_GET['type'] ?? '') == 'image') ? 'selected' : '' ?>>Images</option>
            <option value="video" <?= (($_GET['type'] ?? '') == 'video') ? 'selected' : '' ?>>Videos</option>
            <option value="audio" <?= (($_GET['type'] ?? '') == 'audio') ? 'selected' : '' ?>>Audios</option>
        </select>
        <button type="submit">Filter</button>
        <a href="?export=1">📥 Export Metadata</a>
    </form>

    <form method="post">
        <button name="bulk_delete" onclick="return confirm('Are you sure you want to delete the selected items? This action cannot be undone.')">🗑️ Delete Selected</button>
        <input type="text" name="new_tag" placeholder="Add tag">
        <button name="bulk_tag">🏷️ Tag Selected</button>
        <br><br>

        <table border="1" cellpadding="6">
            <thead>
                <tr>
                    <th>Select</th>
                    <th>Preview</th>
                    <th>Filename</th>
                    <th>Type</th>
                    <th>Label</th>
                    <th>Tags</th>
                    <th>Size</th>
                    <th>⭐</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $filter = $_GET['search'] ?? '';
                $type_filter = $_GET['type'] ?? ''; // Renamed to avoid conflict with $type variable in upload loop

                $sql = "SELECT * FROM media WHERE 1";
                $params = [];
                $param_types = "";

                if ($filter) {
                    $sql .= " AND (label LIKE ? OR tags LIKE ?)";
                    $like_filter = '%' . $filter . '%';
                    $params[] = $like_filter;
                    $params[] = $like_filter;
                    $param_types .= "ss";
                }
                if ($type_filter) { // Use type_filter here
                    $sql .= " AND type = ?";
                    $params[] = $type_filter;
                    $param_types .= "s";
                }
                // Changed 'created_at' to 'uploaded_at'
                $sql .= " ORDER BY uploaded_at DESC";

                $stmt_select = $conn->prepare($sql);
                if ($stmt_select === false) {
                    error_log("Failed to prepare media selection query: " . $conn->error);
                    echo "<tr><td colspan='8'>Error retrieving media.</td></tr>";
                } else {
                    if (!empty($params)) {
                        $stmt_select->bind_param($param_types, ...$params);
                    }
                    $stmt_select->execute();
                    $res = $stmt_select->get_result();

                    if ($res->num_rows > 0) {
                        while ($row = $res->fetch_assoc()) {
                            // Construct file path based on database 'type' and 'filename'
                            $filePath = "media/{$row['type']}s/{$row['filename']}";
                            // Only display size if file exists
                            $fileSize = file_exists($filePath) ? round(filesize($filePath) / 1024, 2) . " KB" : "N/A";
                            echo "<tr>
                                <td><input type='checkbox' name='files[]' value='" . htmlspecialchars($row['filename']) . "'></td>
                                <td>";
                            if ($row['type'] === 'image') echo "<img src='" . htmlspecialchars($filePath) . "' width='100' alt='" . htmlspecialchars($row['filename']) . "'>";
                            elseif ($row['type'] === 'video') echo "<video src='" . htmlspecialchars($filePath) . "' width='100' muted controlslist='nodownload' preload='metadata'></video>";
                            elseif ($row['type'] === 'audio') echo "<audio src='" . htmlspecialchars($filePath) . "' controls controlslist='nodownload'></audio>";
                            echo "</td>
                                <td>" . htmlspecialchars($row['filename']) . "</td>
                                <td>" . htmlspecialchars($row['type']) . "</td>
                                <td contenteditable onblur=\"updateField(this, 'label', '" . htmlspecialchars($row['filename']) . "')\">" . htmlspecialchars($row['label']) . "</td>
                                <td contenteditable onblur=\"updateField(this, 'tags', '" . htmlspecialchars($row['filename']) . "')\">" . htmlspecialchars($row['tags']) . "</td>
                                <td>{$fileSize}</td>
                                <td><a href='rate.php?file=" . urlencode($filePath) . "'>⭐ Rate</a></td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No media found matching your criteria.</td></tr>";
                    }
                    $stmt_select->close();
                }
                ?>
            </tbody>
        </table>
    </form>

    <h3>📊 Generation Log Stats</h3>
    <div>
        <?php
        $res = $conn->query("SELECT type, COUNT(*) as total FROM generation_log GROUP BY type");
        if ($res && $res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                echo "<div>🔹 " . htmlspecialchars($row['type']) . "s generated: " . htmlspecialchars($row['total']) . "</div>";
            }
        } else {
            echo "<div>No generation log data available.</div>";
        }
        ?>
    </div>
    <?php $conn->close(); // Close DB connection at the end of script ?>
</body>
</html>