<?php
// File: upload_video.php
// die('PHP script is being executed!'); // Add this line
// Always set the content type to JSON for API responses
header('Content-Type: application/json');

// --- Configuration ---
// Define paths; adjust $ffmpegPath as needed for your setup
$ffmpegPath = 'C:/xampp/htdocs/tools/bin/ffmpeg.exe'; // Ensure this path is correct
$baseDir = __DIR__; // The directory where this deepfake_upload.php file is located
$videoInputDir = $baseDir . '/deepfake_input/videos/';
$frameBaseDir = $baseDir . '/deepfake_input/frames/';
$audioBaseDir = $baseDir . '/deepfake_input/audio/'; // This directory will be used by frame_extractor.php

// Add MySQL DB connection details
$db_host = 'localhost';
$db_name = 'gero_gpt'; // Your database name
$db_user = 'root';
$db_pass = '';
$db_charset = 'utf8mb4';

// --- Helper Function for Standardized JSON Responses ---
// This function ensures all responses (success or error) are in a consistent JSON format.
function sendJsonResponse($status, $message, $data = []) {
    echo json_encode(array_merge(['status' => $status, 'message' => $message], $data));
    exit; // Stop script execution after sending the response
}

// --- Database Connection ---
try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (\PDOException $e) {
    // Log the error for debugging, don't show sensitive info to user
    error_log("Database Connection Error in deepfake_upload.php: " . $e->getMessage());
    sendJsonResponse('error', 'Database connection failed. Please check server logs.');
}

// --- Directory Setup ---
// Create necessary directories if they don't exist.
// Permissions are set to 0777 for broad access during development.
// For production, consider tighter permissions like 0755 for directories.
$requiredDirs = [$videoInputDir, $frameBaseDir, $audioBaseDir]; // Include audio dir for early creation
foreach ($requiredDirs as $dir) {
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0777, true)) { // `true` enables recursive directory creation
            sendJsonResponse('error', 'Failed to create directory: ' . $dir . '. Please check server permissions.');
        }
    }
}

// --- Video Upload Handling ---
// Check if a file was actually uploaded and if there were any PHP upload errors.
if (!isset($_FILES['video']) || $_FILES['video']['error'] !== UPLOAD_ERR_OK) {
    // Provide a detailed error message based on common PHP upload error codes
    $uploadErrors = [
        UPLOAD_ERR_INI_SIZE     => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
        UPLOAD_ERR_FORM_SIZE    => 'The uploaded file exceeds the MAX_FILE_SIZE directive specified in the HTML form.',
        UPLOAD_ERR_PARTIAL      => 'The uploaded file was only partially uploaded.',
        UPLOAD_ERR_NO_FILE      => 'No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR   => 'Missing a temporary folder for uploads. Check PHP temp directory settings.',
        UPLOAD_ERR_CANT_WRITE   => 'Failed to write the uploaded file to disk. Check server write permissions.',
        UPLOAD_ERR_EXTENSION    => 'A PHP extension stopped the file upload. Check your PHP configuration.',
    ];
    $errorMessage = 'Video upload failed. Error code: ' . ($_FILES['video']['error'] ?? 'N/A') . '. ';
    $errorMessage .= $uploadErrors[$_FILES['video']['error']] ?? 'An unknown upload error occurred.';

    sendJsonResponse('error', $errorMessage);
}

// Get original file details and create a unique name for the uploaded video
$originalName = basename($_FILES['video']['name']);
$ext = pathinfo($originalName, PATHINFO_EXTENSION);
$uniqueVideoName = uniqid('video_', true) . '.' . $ext; // Generates a unique filename like 'video_6670a4b7a.mov'
$videoPath = $videoInputDir . $uniqueVideoName; // Full path where the video will be saved

// Move the uploaded temporary file to its permanent storage location
if (!move_uploaded_file($_FILES['video']['tmp_name'], $videoPath)) {
    sendJsonResponse('error', 'Failed to move the uploaded video. This is often due to incorrect write permissions on: ' . $videoInputDir);
}

// Extract the unique ID from the video filename (e.g., 'video_6670a4b7a' from 'video_6670a4b7a.mov')
$videoId = pathinfo($uniqueVideoName, PATHINFO_FILENAME);
// Define a unique output directory for frames specific to this video
$frameOutputDir = $frameBaseDir . $videoId . '/';

// --- Insert initial record into 'videos' table ---
try {
    $stmt = $pdo->prepare("INSERT INTO videos (unique_filename, original_filename, video_storage_path, frames_storage_dir, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$uniqueVideoName, $originalName, $videoPath, $frameOutputDir, 'uploaded']);
    $videoIdInDb = $pdo->lastInsertId(); // Get the ID of the new record
} catch (\PDOException $e) {
    error_log("DB Insert Error in deepfake_upload.php: " . $e->getMessage());
    // Attempt to clean up the uploaded video if DB insertion fails
    if (file_exists($videoPath)) {
        unlink($videoPath);
    }
    sendJsonResponse('error', 'Failed to record video in database after upload. Please try again.');
}

// --- Frame Extraction Setup (now handled by frame_extractor.php via AJAX call from deepfake.php) ---
// We no longer call FFmpeg here directly. deepfake.php will make a separate call to frame_extractor.php

// Send success response to client
sendJsonResponse('success', 'Video uploaded successfully! Initiating frame extraction...', [
    'video' => $uniqueVideoName, // This is the unique filename, used to retrieve info later
    'video_db_id' => $videoIdInDb // NEW: Pass the database ID for subsequent operations
]);

?>