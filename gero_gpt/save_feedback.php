<?php
// File: save_feedback.php

$pdo = new PDO("mysql:host=localhost;dbname=gero_gpt", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$data = json_decode(file_get_contents('php://input'), true);

$frame_number = $data['frame_number'];
$face_path = $data['face_image'];
$adjustment_type = $data['adjustment_type'];
$generation = $data['generation'];
$action = $data['action'];

$face_filename = basename(parse_url($face_path, PHP_URL_PATH));

$stmt = $pdo->prepare("INSERT INTO deepfake_adjustments (frame_number, face_image, adjustment_type, value, generation, status) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([
  $frame_number,
  $face_filename,
  $adjustment_type,
  $action === 'exclude' ? 'skip' : 'apply',
  $generation,
  $action
]);

echo json_encode(['status' => 'ok']);
?>
