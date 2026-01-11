<?php
// File: load_faces.php
$pdo = new PDO("mysql:host=localhost;dbname=gero_gpt", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->query("SELECT filename, label FROM media WHERE type = 'image'");
$faces = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($faces);
?>