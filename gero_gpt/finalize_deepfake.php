<?php
// File: finalize_deepfake.php
header('Content-Type: application/json');

$generation = isset($_GET['generation']) ? intval($_GET['generation']) : 1;
if ($generation < 1) {
    echo json_encode(["status" => "error", "message" => "Invalid generation."]);
    exit;
}

$framePattern = sprintf("deepfake_input/blended/gen%d_frame%%03d_face.jpg", $generation);
$audioPath = "deepfake_input/audio/original_audio.aac";
$outputDir = "deepfake_output";
$outputFile = "$outputDir/final_output_gen{$generation}.mp4";

// Ensure output directory exists
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

// FFmpeg command
$cmd = "ffmpeg -y -framerate 25 -i $framePattern -i $audioPath -c:v libx264 -pix_fmt yuv420p -c:a aac $outputFile 2>&1";
exec($cmd, $output, $status);

if ($status === 0 && file_exists($outputFile)) {
    echo json_encode([
        "status" => "success",
        "message" => "Final video created successfully.",
        "output" => $outputFile
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "FFmpeg failed.",
        "debug" => implode("\n", $output)
    ]);
}
?>
