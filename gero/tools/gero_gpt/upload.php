<?php
include 'db.php';

$type = $_POST['type'];
$label = $_POST['label'];
$file = $_FILES['file'];

$uploadDir = "assets/uploads/{$type}s/";
$target = $uploadDir . basename($file["name"]);

if (move_uploaded_file($file["tmp_name"], $target)) {
    $stmt = $conn->prepare("INSERT INTO media (filename, label, type) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $file["name"], $label, $type);
    $stmt->execute();
    echo "Uploaded successfully.\n";
    echo "<a href='train.html'>Train More Data</a>";
} else {
    echo "Upload failed.";
}

$conn->close();
?>
