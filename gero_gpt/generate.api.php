<?php
// Connect to database
include 'db/config.php';

$prompt = $_POST['prompt'] ?? '';
if (!$prompt) {
    echo json_encode(['error' => 'Missing prompt']);
    exit;
}

// Paths
$sourceDir  = __DIR__ . "/media/videos/";
$targetDir  = __DIR__ . "/media/generated_videos/";
$ffmpegPath = "C:\\xampp\\htdocs\\tools\\bin\\ffmpeg.exe"; // Adjust as needed

if (!is_dir($sourceDir) || !is_dir($targetDir)) {
    echo json_encode(['error' => 'Source or target directory not found.']);
    exit;
}

// 1. Search matching media entries by label or tags
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

// 2. If no match, fallback to available video
if (count($matchedFiles) === 0) {
    $allVideos = array_values(array_filter(scandir($sourceDir), function ($f) use ($sourceDir) {
        return is_file($sourceDir . $f) && preg_match('/\.(mp4|webm|mov)$/i', $f);
    }));

    if (count($allVideos) > 0) {
        // Pick 1–3 fallback videos
        $fallback = array_slice($allVideos, 0, 3);
        $matchedFiles = array_map(fn($f) => $sourceDir . $f, $fallback);
    } else {
        echo json_encode([
            'error' => 'No matching videos found and no fallback available.',
            'debug' => [
                'prompt' => $prompt,
                'search' => $promptLike
            ]
        ]);
        exit;
    }
}

// 3. Limit number of clips used (max 3)
$matchedFiles = array_slice($matchedFiles, 0, 3);

// 4. Generate individual trimmed clips
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

// 5. Create a concat.txt file for joining
$concatListFile = $targetDir . "concat_list.txt";
$concatList = "";
foreach ($tempClips as $clip) {
    $concatList .= "file '" . $clip . "'\n";
}
file_put_contents($concatListFile, $concatList);

// 6. Join the clips
$outputFile = $targetDir . "gen_" . time() . ".mp4";
$joinCmd = "\"$ffmpegPath\" -y -f concat -safe 0 -i \"$concatListFile\" -c copy \"$outputFile\"";
exec($joinCmd, $out2, $joinCode);

if ($joinCode !== 0 || !file_exists($outputFile)) {
    echo json_encode(['error' => "Failed to join video clips", 'cmd' => $joinCmd]);
    exit;
}

// 7. Clean up temp files
foreach ($tempClips as $clip) unlink($clip);
unlink($concatListFile);

// 8. Return success
echo json_encode([
    'success' => true,
    'filename' => basename($outputFile),
    'source_used' => array_map('basename', $matchedFiles),
    'prompt' => $prompt
]);
?>
