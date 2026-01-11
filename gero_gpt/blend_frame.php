<?php
// File: blend_frame.php
// This script takes a video frame, a selected face, and coordinates,
// then calls a Python script to perform the blending.

header('Content-Type: application/json');

// Error logging setup (recommended for development/debugging)
// In a production environment, display_errors should be 0.
ini_set('display_errors', 0); // Hide errors from browser
ini_set('log_errors', 1);    // Log errors to file
// This error_log path will be relative to blend_frame.php, which is in BASE_DIR.
// It will write to C:\xampp\htdocs\gero_gpt\php_error.log
ini_set('error_log', __DIR__ . '/php_error.log');

// *** IMPORTANT: Include config.php FIRST to define BASE_DIR and other paths ***
// config.php is located in the 'db' subdirectory relative to blend_frame.php
include_once __DIR__ . '/db/config.php';

// Check if BASE_DIR is defined (it should be after including config.php)
if (!defined('BASE_DIR')) {
    error_log("Fatal Error: BASE_DIR is not defined after including config.php in blend_frame.php");
    echo json_encode(['status' => 'error', 'message' => 'Server configuration error: BASE_DIR not defined.']);
    exit;
}

// The following line is commented out because 'db_connect.php' was confirmed not to exist,
// and 'blend_frame.php' does not directly use database connections.
// If you have a 'db.php' file that sets up $pdo or $conn and other scripts need it,
// you might include it where necessary in those specific scripts.
// include_once BASE_DIR . '/db_connect.php';

$response = ['status' => 'error', 'message' => 'An unknown error occurred.', 'blended_images' => []];

// Get GET parameters
$video_name = $_GET['video'] ?? null; // e.g., "video_123.mp4"
$frame_number = (int)($_GET['frame'] ?? 0);
$face_image = $_GET['face'] ?? null; // e.g., "face_abc.jpg"
$generation = (int)($_GET['generation'] ?? 1); // Default to generation 1

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


if (!$video_name || !$frame_number || !$face_image) {
    $response['message'] = "Missing required parameters: video, frame, or face.";
    echo json_encode($response);
    exit;
}

// Extract the video base name (folder name) from the full video filename
// e.g., "video_123.mp4" -> "video_123"
$video_base_name = pathinfo($video_name, PATHINFO_FILENAME);

// Construct full paths using the defined constants from config.php
// CRITICAL CHANGE: Match frame naming convention (frame_001.jpg)
$input_frame_path = FRAME_DIR . "/" . $video_base_name . "/frame" . sprintf('_%03d', $frame_number) . ".jpg";
$face_image_path = IMAGE_DIR . "/" . $face_image;


// Ensure input files exist before attempting to blend
if (!file_exists($input_frame_path)) {
    $response['message'] = "Input frame not found: " . $input_frame_path;
    error_log("Blend Error: Input frame not found: " . $input_frame_path); // Log the error
    echo json_encode($response);
    exit;
}
if (!file_exists($face_image_path)) {
    $response['message'] = "Face image not found: " . $face_image_path;
    error_log("Blend Error: Face image not found: " . $face_image_path); // Log the error
    echo json_encode($response);
    exit;
}

// Define output path for the blended image
// Blended images will be stored in a subfolder for each video, and then by frame and generation
$output_dir = BLENDED_FRAME_DIR . "/{$video_base_name}/"; // Use video_base_name for output subdirectory
if (!is_dir($output_dir)) {
    if (!mkdir($output_dir, 0777, true)) { // Create recursively if parent directories don't exist
        $response['message'] = "Failed to create output directory: " . $output_dir;
        error_log("Blend Error: Failed to create output directory: " . $output_dir); // Log the error
        echo json_encode($response);
        exit;
    }
}
// CRITICAL CHANGE: Match frame naming convention for output (frame_001.jpg)
$output_filename = "frame" . sprintf('_%03d', $frame_number) . "_gen" . sprintf('%02d', $generation) . ".jpg";
$output_image_path = $output_dir . $output_filename;
// Web path for the blended image (relative to document root)
$output_image_web_path = "/gero_gpt/deepfake_output/blended_frames/{$video_base_name}/{$output_filename}";

// Construct the command to call your Python blending script
// ******************************************************************************
// PATCHED: Changed 'python3' to 'python' as it's more common for Windows installations
$command = escapeshellcmd("python " . BASE_DIR . "/scripts/deepfake_blender.py " .
// ******************************************************************************
             $input_frame_path . " " .
             $face_image_path . " " .
             $output_image_path . " " .
             $target_x . " " .
             $target_y . " " .
             $target_w . " " .
             $target_h . " " .
             $source_face_crop_x . " " .
             $source_face_crop_y . " " .
             $source_face_crop_w . " " .
             $source_face_crop_h);

// Execute the command
$output = []; // To capture stdout and stderr from the Python script
$return_var = 0; // To capture the exit code of the Python script
exec($command . ' 2>&1', $output, $return_var); // '2>&1' redirects stderr to stdout

if ($return_var === 0) {
    if (file_exists($output_image_path)) {
        $response['status'] = 'success';
        $response['message'] = "Frame blended successfully.";
        $response['blended_images'][] = ['path' => $output_image_web_path];
    } else {
        // Python script returned 0 but output file not found.
        $response['message'] = "Blending script ran, but output image not found. Python Output: " . implode("\n", $output);
        error_log("Blend Error: Python script ran but output not found for frame {$frame_number}. Command: {$command}. Output: " . implode("\n", $output));
    }
} else {
    // Python script returned a non-zero exit code (error)
    $response['message'] = "Blending script failed. Return code: {$return_var}. Python Output: " . implode("\n", $output);
    error_log("Blend Error: Python script failed for frame {$frame_number}. Command: {$command}. Return: {$return_var}. Output: " . implode("\n", $output));
}

echo json_encode($response);
?>