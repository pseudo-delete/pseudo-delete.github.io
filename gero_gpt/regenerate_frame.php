<?php
// File: regenerate_frame.php

$frame_number = intval($_GET['frame'] ?? 1);
$generation = intval($_GET['generation'] ?? 1);
$video = $_GET['video'] ?? '';

$frame_path = "deepfake_input/frames/$video/frame_" . str_pad($frame_number, 3, "0", STR_PAD_LEFT) . ".jpg";
if (!file_exists($frame_path)) {
    echo json_encode(['status' => 'error', 'message' => 'Frame not found']);
    exit;
}

$adjusted_dir = "deepfake_input/adjusted_faces/gen{$generation}";
$adjusted_faces = glob("$adjusted_dir/{$frame_number}_*");

$results = [];

foreach ($adjusted_faces as $face_path) {
    // Fake blend logic here; for now just return the image path.
    $results[] = [
        'path' => $face_path,
        'face' => basename($face_path)
    ];
}

echo json_encode([
    'status' => 'success',
    'regenerated_images' => $results
]);

?>