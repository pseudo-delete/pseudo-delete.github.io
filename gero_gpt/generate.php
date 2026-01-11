<?php
include 'db/config.php';

$prompt = $_POST['prompt'] ?? '';
if (!$prompt) {
    echo json_encode(['error' => 'Missing prompt']);
    exit;
}

$sourceDir = __DIR__ . "/media/videos/";
$targetDir = __DIR__ . "/media/generated_videos/";
$ffmpegPath = "C:\\xampp\\htdocs\\tools\\bin\\ffmpeg.exe";

if (!is_dir($sourceDir) || !is_dir($targetDir)) {
    echo json_encode(['error' => 'Source or target directory not found.']);
    exit;
}

$promptLike = '%' . $conn->real_escape_string($prompt) . '%';
$query = $conn->prepare("SELECT filename FROM media WHERE type = 'video' AND (label LIKE ? OR tags LIKE ?)");
$query->bind_param("ss", $promptLike, $promptLike);
$query->execute();
$result = $query->get_result();

$matchedFiles = [];
while ($row = $result->fetch_assoc()) {
    $filepath = $sourceDir . $row['filename'];
    if (is_file($filepath)) {
        $matchedFiles[] = $filepath;
    }
}

if (count($matchedFiles) === 0) {
    echo json_encode(['error' => 'No matching videos found for prompt.']);
    exit;
}

$matchedFiles = array_slice($matchedFiles, 0, 3);
$tempClips = [];

foreach ($matchedFiles as $index => $file) {
    $clipName = $targetDir . "temp_clip_$index.mp4";
    $cmd = "\"$ffmpegPath\" -y -i \"$file\" -t 5 -vf \"hue=s=0,drawtext=text='Gero GPT':fontsize=24:fontcolor=white:x=10:y=10\" \"$clipName\"";
    exec($cmd, $out, $code);
    if ($code !== 0 || !file_exists($clipName)) {
        echo json_encode(['error' => "Failed to process clip: " . basename($file), 'cmd' => $cmd]);
        exit;
    }
    $tempClips[] = $clipName;
}

$concatListFile = $targetDir . "concat_list.txt";
file_put_contents($concatListFile, implode("\n", array_map(fn($c) => "file '" . $c . "'", $tempClips)));

$outputFile = $targetDir . "gen_" . time() . ".mp4";
$joinCmd = "\"$ffmpegPath\" -y -f concat -safe 0 -i \"$concatListFile\" -c copy \"$outputFile\"";
exec($joinCmd, $out2, $joinCode);

foreach ($tempClips as $clip) unlink($clip);
unlink($concatListFile);

if ($joinCode !== 0 || !file_exists($outputFile)) {
    echo json_encode(['error' => "Failed to join video clips", 'cmd' => $joinCmd]);
    exit;
}

// ✅ Log generation
$genFilename = basename($outputFile);
$stmt = $conn->prepare("INSERT INTO generation_log (filename, prompt, type) VALUES (?, ?, 'video')");
$stmt->bind_param("ss", $genFilename, $prompt);
$stmt->execute();

echo json_encode([
    'success' => true,
    'filename' => $genFilename,
    'source_used' => array_map('basename', $matchedFiles),
    'prompt' => $prompt
]);
?>
