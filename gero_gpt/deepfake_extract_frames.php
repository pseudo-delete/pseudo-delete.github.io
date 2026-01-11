<?php
// File: deepfake_extract_frames.php

$video_dir = 'deepfake_input/videos/';
$frames_dir = 'deepfake_frames/';

// Ensure frame directory exists
if (!is_dir($frames_dir)) mkdir($frames_dir, 0777, true);

// Get video filename from request
$video_filename = $_GET['video'] ?? '';
if (!$video_filename || !file_exists($video_dir . $video_filename)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or missing video file.']);
    exit;
}

$video_path = $video_dir . $video_filename;
$video_basename = pathinfo($video_filename, PATHINFO_FILENAME);
$output_path = $frames_dir . $video_basename . '/';

if (!is_dir($output_path)) mkdir($output_path, 0777, true);

// Use FFmpeg to extract frames
$cmd = "ffmpeg -i " . escapeshellarg($video_path) . " -qscale:v 2 " . escapeshellarg($output_path . 'frame_%04d.jpg');
exec($cmd, $output, $return_var);

if ($return_var === 0) {
    echo json_encode(['status' => 'success', 'frames_folder' => $output_path]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to extract frames.']);
}
exit;
?>