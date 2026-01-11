<?php
$sourceDir = __DIR__ . "/media/images/";
$targetDir = __DIR__ . "/media/generated_images/";
$logFile   = __DIR__ . "/logs/ffmpeg_error.log";
$ffmpeg    = "C:\\xampp\\htdocs\\tools\\bin\\ffmpeg.exe";

// Check if directories exist
if (!is_dir($sourceDir) || !is_dir($targetDir)) {
    echo json_encode(['error' => 'Media directories not found.']);
    exit;
}

// Get all image files
$images = array_values(array_diff(scandir($sourceDir), ['.', '..']));
if (count($images) < 2) {
    echo json_encode(['error' => 'Need at least 2 training images.']);
    exit;
}

// Pick 2 different images randomly
$img1 = $sourceDir . $images[array_rand($images)];
do {
    $img2 = $sourceDir . $images[array_rand($images)];
} while ($img1 === $img2);

// Output filename
$timestamp = time();
$outputImage = $targetDir . "image_{$timestamp}.jpg";

// FFmpeg command: scale both to 512x512 and blend
$cmd = "\"$ffmpeg\" -y " .
    "-i \"$img1\" -i \"$img2\" " .
    "-filter_complex \"[0:v]scale=512:512[bg];[1:v]scale=512:512[fg];[bg][fg]blend=all_mode=overlay:all_opacity=0.5\" " .
    "\"$outputImage\" 2> \"$logFile\"";

exec($cmd, $out, $code);

if ($code !== 0) {
    echo json_encode([
        'error' => 'ffmpeg failed to generate image.',
        'cmd' => $cmd,
        'debug' => file_exists($logFile) ? file_get_contents($logFile) : 'No log output.'
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'filename' => basename($outputImage)
]);
?>
