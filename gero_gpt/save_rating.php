<?php
$pdo = new PDO("mysql:host=localhost;dbname=gero_gpt", "root", "");

$filename = $_POST['filename'];
$type = $_POST['type'];
$rating = (int)$_POST['rating'];
$tags = $_POST['tags'] ?? '';

$stmt = $pdo->prepare("INSERT INTO media_ratings (filename, type, rating, tags) VALUES (?, ?, ?, ?)");
$stmt->execute([$filename, $type, $rating, $tags]);

// Move file to rated folder
$src = "media/generated_{$type}s/" . $filename;
$dest = "media/rated/{$type}s/" . $filename;
rename($src, $dest);

header("Location: rate.php");
exit;
