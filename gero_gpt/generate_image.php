<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "gero_gpt");

if ($conn->connect_error) {
  echo json_encode(['success' => false, 'error' => 'Database connection failed.']);
  exit;
}

$prompt = trim($_POST['prompt'] ?? '');
$generation = intval($_POST['generation'] ?? 1);

if (empty($prompt)) {
  echo json_encode(['success' => false, 'error' => 'Prompt is required.']);
  exit;
}

// Split prompt into words and search for any match in filename/label/tags
$words = preg_split('/\s+/', $conn->real_escape_string($prompt));
$likeClauses = array_map(fn($word) => "(filename LIKE '%$word%' OR label LIKE '%$word%' OR tags LIKE '%$word%')", $words);
$where = implode(" OR ", $likeClauses);

// Look for training media to use for generation
$sql = "SELECT * FROM media WHERE type = 'image' AND ($where) ORDER BY RAND() LIMIT 1";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
  echo json_encode(['success' => false, 'error' => 'No matching training media found.']);
  exit;
}

$row = $result->fetch_assoc();
$filepath = $row['filepath'] ?? '';
if (!file_exists($filepath)) {
  echo json_encode(['success' => false, 'error' => "File not found: $filepath"]);
  exit;
}

$ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
switch ($ext) {
  case 'jpg':
  case 'jpeg':
    $src = @imagecreatefromjpeg($filepath); break;
  case 'png':
    $src = @imagecreatefrompng($filepath); break;
  case 'gif':
    $src = @imagecreatefromgif($filepath); break;
  default:
    echo json_encode(['success' => false, 'error' => 'Unsupported image format.']);
    exit;
}

if (!$src) {
  echo json_encode(['success' => false, 'error' => 'Failed to create image from file.']);
  exit;
}

// Simulate image generation: add red border to simulate output
$w = imagesx($src);
$h = imagesy($src);
$red = imagecolorallocate($src, 255, 0, 0);
imagerectangle($src, 0, 0, $w-1, $h-1, $red);

// Save generated image
$outDir = 'media/generated_images/';
if (!file_exists($outDir)) mkdir($outDir, 0777, true);

$outName = 'gen_' . time() . '_' . rand(1000, 9999) . '.png';
$outPath = $outDir . $outName;

if (!imagepng($src, $outPath)) {
  echo json_encode(['success' => false, 'error' => 'Failed to save generated image.']);
  exit;
}
imagedestroy($src);

// Log to database
$stmt = $conn->prepare("INSERT INTO generation_log (filename, prompt, type, generation) VALUES (?, ?, 'image', ?)");
$stmt->bind_param("ssi", $outName, $prompt, $generation);
$stmt->execute();
$stmt->close();
$conn->close();

echo json_encode(['success' => true, 'filename' => $outName]);
