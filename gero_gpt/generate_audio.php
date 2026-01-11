<?php
// generate_audio.php
$sourceDir = "media/audios/";
$targetDir = "media/generated_audios/";

$audios = array_diff(scandir($sourceDir), ['.', '..']);
if (count($audios) < 1) {
    echo json_encode(['error' => 'No training audios available.']);
    exit;
}

$selected = array_rand($audios);
$srcAudio = $sourceDir . $audios[$selected];
$outputAudio = $targetDir . "gen_" . time() . ".mp3";

// Simulate audio generation (change pitch)
$cmd = "ffmpeg -y -i \"$srcAudio\" -af \"asetrate=44100*1.05,aresample=44100\" \"$outputAudio\"";
exec($cmd);

echo json_encode(['success' => true, 'filename' => basename($outputAudio)]);
?>
