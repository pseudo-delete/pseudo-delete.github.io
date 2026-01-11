<?php
include 'db/config.php';

$type = $_POST['type'] ?? '';
$label = $_POST['label'] ?? '';
$tags = ''; // Optional, can be extended
$uploadDir = __DIR__ . "/media/{$type}s/";

// Check for valid type
if (!in_array($type, ['image', 'video', 'audio'])) {
    die("Invalid media type.");
}

// Check if a file was uploaded
if (!isset($_FILES['media']) || $_FILES['media']['error'] !== UPLOAD_ERR_OK) {
    die("File upload failed.");
}

// Sanitize and rename file
$originalName = $_FILES['media']['name'];
$filename = str_replace(' ', '_', basename($originalName)); // Replaces spaces with underscores
$tmpPath = $_FILES['media']['tmp_name'];
$targetPath = $uploadDir . $filename;

// Move uploaded file
if (!move_uploaded_file($tmpPath, $targetPath)) {
    die("Failed to save uploaded file.");
}

// Insert metadata into database
$stmt = $conn->prepare("INSERT INTO media (filename, label, type, tags) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $filename, $label, $type, $tags);
$stmt->execute();

header("Location: train.php");
exit;
?>
