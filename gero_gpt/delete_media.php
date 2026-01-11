<?php
include 'db/config.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
  $res = $conn->query("SELECT * FROM media WHERE id = $id");
  if ($row = $res->fetch_assoc()) {
    $path = "media/{$row['type']}s/{$row['filename']}";
    if (file_exists($path)) {
      unlink($path);
    }
    $conn->query("DELETE FROM media WHERE id = $id");
  }
}

header("Location: train.php");
exit;
?>
