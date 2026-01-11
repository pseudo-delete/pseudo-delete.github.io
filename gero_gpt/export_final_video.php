<?php
// File: export_final_video.php

$videoId = $_GET['video'] ?? '';
if (!$videoId) {
  exit(json_encode(['status' => 'error', 'message' => 'Missing video ID']));
}

$frameDir = "deepfake_output/frames/$videoId/";
$outputVideo = "deepfake_output/videos/{$videoId}_final.mp4";
$inputVideo = "deepfake_input/videos/$videoId";

if (!is_dir($frameDir) || !file_exists($inputVideo)) {
  exit(json_encode(['status' => 'error', 'message' => 'Missing frames or source video']));
}

// Step 1: Create video from frames
$cmd1 = "ffmpeg -y -framerate 30 -i {$frameDir}frame_%03d.jpg -c:v libx264 -pix_fmt yuv420p temp_video.mp4";

// Step 2: Extract and add audio
$cmd2 = "ffmpeg -y -i temp_video.mp4 -i \"$inputVideo\" -c:v copy -map 0:v:0 -map 1:a:0 -shortest \"$outputVideo\"";

// Execute both
exec($cmd1, $o1, $r1);
exec($cmd2, $o2, $r2);

// Cleanup temp
unlink("temp_video.mp4");

if ($r2 === 0) {
  // Optional: Insert into generation_log
  $db = new PDO("sqlite:your_database.db");
  $stmt = $db->prepare("INSERT INTO generation_log (type, filename, prompt, timestamp, engine, generation) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->execute([
    'video',
    "{$videoId}_final.mp4",
    "Deepfake final video assembled",
    date('Y-m-d H:i:s'),
    'deepfake',
    1
  ]);

  echo json_encode([
    'status' => 'success',
    'video_path' => $outputVideo
  ]);
} else {
  echo json_encode([
    'status' => 'error',
    'message' => 'FFmpeg failed to generate video'
  ]);
}
?>