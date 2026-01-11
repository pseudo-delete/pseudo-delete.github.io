<?php
include 'db/config.php';

$id = (int)($_POST['id'] ?? 0);
$rating = (int)($_POST['rating'] ?? 0);

if ($id > 0 && $rating >= 1 && $rating <= 5) {
    $stmt = $conn->prepare("UPDATE media SET rating = ? WHERE id = ?");
    $stmt->bind_param("ii", $rating, $id);
    $stmt->execute();
}
?>
