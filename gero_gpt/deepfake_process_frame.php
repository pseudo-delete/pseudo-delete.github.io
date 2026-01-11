<?php
// File: deepfake_process_frame.php

// Requires ffmpeg installed and accessible via CLI

$video_dir = 'deepfake_input/videos/';
$frame_dir = 'deepfake_frames/';
$output_dir = 'deepfake_output/';
$media_dir = 'media/';

if (!is_dir($frame_dir)) mkdir($frame_dir, 0777, true);
if (!is_dir($output_dir)) mkdir($output_dir, 0777, true);

$pdo = new PDO('sqlite:gero_gpt.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create deepfake_adjustments table if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS deepfake_adjustments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    frame_number INTEGER,
    face_image TEXT,
    adjustment_type TEXT,
    value TEXT,
    generation INTEGER,
    status TEXT CHECK(status IN ('pending', 'applied', 'skipped')) DEFAULT 'pending'
)");

function extract_frames($video_file, $output_path) {
    $cmd = "ffmpeg -i " . escapeshellarg($video_file) . " -qscale:v 2 " . escapeshellarg($output_path . 'frame_%04d.png');
    exec($cmd);
}

function get_face_images($pdo) {
    $stmt = $pdo->query("SELECT filename FROM media WHERE type = 'image'");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function blend_face_to_frame($frame_path, $face_path, $output_path) {
    // Simple copy for now — replace with actual blend logic later
    copy($frame_path, $output_path); 
}

// --- EXECUTION ENTRY ---

$video_file = isset($_GET['video']) ? basename($_GET['video']) : '';
if (!$video_file || !file_exists($video_dir . $video_file)) {
    die("Invalid video file");
}

$frame_subdir = $frame_dir . pathinfo($video_file, PATHINFO_FILENAME) . '/';
if (!is_dir($frame_subdir)) {
    mkdir($frame_subdir, 0777, true);
    extract_frames($video_dir . $video_file, $frame_subdir);
}

$frames = glob($frame_subdir . 'frame_*.png');
$face_images = get_face_images($pdo);

$generation = 1; // Start from generation 1, can be made dynamic
$current_frame_index = isset($_GET['frame']) ? intval($_GET['frame']) : 0;

if ($current_frame_index >= count($frames)) {
    die("All frames processed.");
}

$current_frame_path = $frames[$current_frame_index];
$frame_filename = basename($current_frame_path);

foreach ($face_images as $face_filename) {
    $output_file = $output_dir . "g" . $generation . "_" . pathinfo($face_filename, PATHINFO_FILENAME) . "_" . $frame_filename;
    blend_face_to_frame($current_frame_path, $media_dir . $face_filename, $output_file);
    
    // Log the attempt in adjustments table
    $stmt = $pdo->prepare("INSERT INTO deepfake_adjustments (frame_number, face_image, adjustment_type, value, generation, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$current_frame_index, $face_filename, 'blend', 'default', $generation, 'pending']);
}

echo "Frame {$current_frame_index} processed with " . count($face_images) . " face(s). Next: frame?frame=" . ($current_frame_index + 1);
exit;
?>