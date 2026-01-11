<?php
$prompt = strtolower($_GET['prompt'] ?? 'abstract image');

// Create a 512x512 image
$img = imagecreatetruecolor(512, 512);

// Default white background
$white = imagecolorallocate($img, 255, 255, 255);
imagefilledrectangle($img, 0, 0, 512, 512, $white);

// Simple keyword logic
if (str_contains($prompt, 'blue')) {
    $color = imagecolorallocate($img, 0, 102, 204);
} elseif (str_contains($prompt, 'red')) {
    $color = imagecolorallocate($img, 204, 0, 0);
} else {
    $color = imagecolorallocate($img, rand(0,255), rand(0,255), rand(0,255));
}

if (str_contains($prompt, 'square')) {
    imagefilledrectangle($img, 100, 100, 400, 400, $color);
}
if (str_contains($prompt, 'circle')) {
    imagefilledellipse($img, 256, 256, 300, 300, $color);
}
if (str_contains($prompt, 'line')) {
    for ($i = 0; $i < 10; $i++) {
        imageline($img, rand(0,512), rand(0,512), rand(0,512), rand(0,512), $color);
    }
}
if (str_contains($prompt, 'text')) {
    $black = imagecolorallocate($img, 0, 0, 0);
    imagestring($img, 5, 20, 20, $prompt, $black);
}

// Save file
$outputPath = 'assets/generated/images/' . time() . '.png';
imagepng($img, $outputPath);
imagedestroy($img);

echo json_encode(['path' => $outputPath]);
