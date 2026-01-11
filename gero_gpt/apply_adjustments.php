<?php
// File: config.php

// Define the base directory of your application
// This will typically be the directory where config.php itself resides.
define('BASE_DIR', __DIR__);

// Alternatively, if your gero_gpt folder is a subdirectory of your web root,
// you might need to adjust, e.g.:
// define('BASE_DIR', realpath(__DIR__ . '/..')); // If gero_gpt is in a subfolder like 'htdocs/gero_gpt'

// Or if it's simpler and you know your exact path:
// define('BASE_DIR', '/path/to/your/gero_gpt');

header('Content-Type: image/jpeg'); // Set header for image output

include_once 'db_connect.php'; // Adjust path as necessary
include_once 'config.php';     // Assumes config.php defines BASE_DIR

$video_name = $_GET['video'] ?? null;
$face_image = $_GET['face_image'] ?? null;
$frame_number = (int)($_GET['frame_number'] ?? 0);
$generation = (int)($_GET['generation'] ?? 1);

// Get target face position on original frame
$target_x = (int)($_GET['target_x'] ?? 0);
$target_y = (int)($_GET['target_y'] ?? 0);
$target_w = (int)($_GET['target_w'] ?? 0);
$target_h = (int)($_GET['target_h'] ?? 0);

// NEW: Get source face crop coordinates
$source_face_crop_x = (int)($_GET['source_face_crop_x'] ?? 0);
$source_face_crop_y = (int)($_GET['source_face_crop_y'] ?? 0);
$source_face_crop_w = (int)($_GET['source_face_crop_w'] ?? 0);
$source_face_crop_h = (int)($_GET['source_face_crop_h'] ?? 0);


if (!$face_image) {
    // Fallback: Return a blank image or an error image
    $img = imagecreate(200, 100);
    $bg_color = imagecolorallocate($img, 200, 200, 200); // Light grey
    $text_color = imagecolorallocate($img, 100, 100, 100); // Darker grey
    imagefill($img, 0, 0, $bg_color);
    imagestring($img, 3, 10, 40, "No face selected", $text_color);
    imagejpeg($img);
    imagedestroy($img);
    exit;
}

$face_image_path = BASE_DIR . "/media/images/" . $face_image;

if (!file_exists($face_image_path)) {
    $img = imagecreate(200, 100);
    $bg_color = imagecolorallocate($img, 255, 200, 200); // Light red
    $text_color = imagecolorallocate($img, 150, 0, 0); // Red
    imagefill($img, 0, 0, $bg_color);
    imagestring($img, 3, 10, 20, "Face image not found:", $text_color);
    imagestring($img, 2, 10, 40, substr($face_image, 0, 25) . '...', $text_color); // Truncate filename
    imagejpeg($img);
    imagedestroy($img);
    exit;
}

// Define a temporary path for the adjusted preview image
// Use sys_get_temp_dir() for platform-independent temp directory
$temp_dir = sys_get_temp_dir();
$temp_filename = uniqid('adjusted_face_preview_') . '.jpg';
$temp_output_path = $temp_dir . '/' . $temp_filename;

// Construct the command to call your Python script for preview generation
// This script should essentially take the face image, crop it, resize it to target_w/h,
// and potentially apply minor adjustments for preview purposes (e.g., center it).
// IMPORTANT: You might need a *separate* Python script for this preview, or
// modify your existing deepfake_blender.py to have a "preview" mode.
// For simplicity here, I'm assuming a generic 'preview_face_adjuster.py' that
// takes the face path, crop, and target dimensions.

$command = escapeshellcmd("python3 " . BASE_DIR . "/scripts/preview_face_adjuster.py " .
             $face_image_path . " " .
             $temp_output_path . " " .
             $target_w . " " . // Target width on the original frame
             $target_h . " " . // Target height on the original frame
             $source_face_crop_x . " " .
             $source_face_crop_y . " " .
             $source_face_crop_w . " " .
             $source_face_crop_h);

$output = [];
$return_var = 0;
exec($command . ' 2>&1', $output, $return_var);

if ($return_var === 0 && file_exists($temp_output_path)) {
    readfile($temp_output_path); // Output the generated image
    unlink($temp_output_path); // Clean up the temporary file
} else {
    // Generate an error image if script failed or output not found
    $img = imagecreate(200, 100);
    $bg_color = imagecolorallocate($img, 255, 220, 220);
    $text_color = imagecolorallocate($img, 150, 0, 0);
    imagefill($img, 0, 0, $bg_color);
    imagestring($img, 3, 10, 20, "Preview failed!", $text_color);
    imagestring($img, 2, 10, 40, "Check logs.", $text_color);
    error_log("apply_adjustments.php error: " . implode("\n", $output)); // Log the Python script's output
    imagejpeg($img);
    imagedestroy($img);
}

?>