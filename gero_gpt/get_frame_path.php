<?php
// File: get_frame_path.php
header('Content-Type: application/json');

// It's good practice to hide PHP errors from direct browser output in production for APIs.
// ini_set('display_errors', 0);
// ini_set('log_errors', 1);
// ini_set('error_log', '/path/to/your/php-error.log'); // Make sure this path is writable by the web server

$video = $_GET['video'] ?? ''; // Expects full video filename, e.g., "video_6851e1140f1009.49878699.mp4"
$frame = $_GET['frame'] ?? ''; // e.g., "1"

if (!$video || !$frame) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Missing video filename or frame number.']);
    exit;
}

// Extract the base name (folder name) from the full video filename
// This will turn "video_6851e1140f1009.49878699.mp4" into "video_6851e1140f1009.49878699"
$videoId = pathinfo($video, PATHINFO_FILENAME);

// Basic sanitization for the videoId to prevent directory traversal issues.
// Only allow alphanumeric characters, underscores, hyphens, and periods (for unusual IDs).
$videoId = preg_replace('/[^a-zA-Z0-9_.-]/', '', $videoId);
if (empty($videoId)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid video ID extracted.']);
    exit;
}

$framePadded = str_pad($frame, 3, '0', STR_PAD_LEFT); // e.g., "001"

// --- Server-side Path Construction for file_exists() ---
// Use __DIR__ to get the absolute path to the current script's directory (e.g., C:/xampp/htdocs/gero_gpt/).
// Then, construct the path to the frames folder relative to gero_gpt.
$baseFramesDir = __DIR__ . '/deepfake_input/frames/';
// Example resolved path: C:/xampp/htdocs/gero_gpt/deepfake_input/frames/

// Construct the full server-side path to the specific frame file
$serverFrameFilePath = $baseFramesDir . $videoId . '/frame_' . $framePadded . '.jpg';

// --- Web-friendly Path Construction for Browser Display ---
// This is the URL the browser needs to load the image.
// It must be root-relative, including your project's subdirectory.
// DOUBLE-CHECK THIS: Ensure '/gero_gpt/' matches your actual web server alias/subdirectory.
// E.g., if your project is at http://localhost/myproject/, this should be '/myproject/'
$webRootBaseUrl = '/gero_gpt/';

$webFramePath = $webRootBaseUrl . 'deepfake_input/frames/' . $videoId . '/frame_' . $framePadded . '.jpg';
// Example web path: /gero_gpt/deepfake_input/frames/video_6851e1140f1009.49878699/frame_001.jpg


if (!file_exists($serverFrameFilePath)) {
    // Log the error to PHP's error log (e.g., in XAMPP's apache/logs/error.log)
    error_log("Frame not found: Attempted path: " . $serverFrameFilePath . " (Video ID: " . $videoId . ", Frame: " . $frame . ")");
    
    // Return specific server-side path for easier debugging in the browser console
    http_response_code(404); // Not Found
    echo json_encode(['status' => 'error', 'message' => 'Original frame image not found on server.', 'debug_path' => $serverFrameFilePath]);
    exit;
}

// If file exists, return success with the web-friendly path
echo json_encode([
    'status' => 'success',
    'frame_path' => $webFramePath
]);

?>