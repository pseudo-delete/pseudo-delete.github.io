<?php
include 'db/config.php';

$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $label = $_POST['label'];
  $tags = $_POST['tags'];

  $stmt = $conn->prepare("UPDATE media SET label = ?, tags = ? WHERE id = ?");
  $stmt->bind_param("ssi", $label, $tags, $id);
  $stmt->execute();

  header("Location: train.php");
  exit;
}

$res = $conn->query("SELECT * FROM media WHERE id = $id");
$row = $res->fetch_assoc();
$filename = $row['filename'] ?? '';
$filetype = pathinfo($filename, PATHINFO_EXTENSION);
$mediaUrl = "media/" . $row['type'] . "s/" . $filename;
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Media</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .form-group {
      margin-bottom: 20px;
    }
    input[type="text"], textarea {
      width: 100%;
      max-width: 600px;
      padding: 8px;
      font-size: 16px;
      box-sizing: border-box;
    }
    textarea {
      height: 80px;
      resize: vertical;
      white-space: pre-wrap;
      overflow-wrap: break-word;
    }
  </style>
</head>
<body>
  <h1>Edit Media</h1>

  <!-- Media Preview -->
  <div style="margin-bottom: 20px;">
    <?php if (in_array(strtolower($filetype), ['jpg', 'jpeg', 'png', 'gif'])): ?>
      <img src="<?= $mediaUrl ?>" alt="Preview" width="300">
    <?php elseif (in_array(strtolower($filetype), ['mp4', 'webm', 'mov'])): ?>
      <video width="320" controls>
        <source src="<?= $mediaUrl ?>" type="video/<?= $filetype ?>">
        Your browser does not support the video tag.
      </video>
    <?php elseif (in_array(strtolower($filetype), ['mp3', 'wav', 'ogg'])): ?>
      <audio controls>
        <source src="<?= $mediaUrl ?>" type="audio/<?= $filetype ?>">
        Your browser does not support the audio element.
      </audio>
    <?php else: ?>
      <p>No preview available for this file type.</p>
    <?php endif; ?>
  </div>

  <!-- Edit Form -->
  <form method="POST">
    <div class="form-group">
      <label>Label:</label><br>
      <input type="text" name="label" value="<?= htmlspecialchars($row['label']) ?>">
    </div>

    <div class="form-group">
      <label>Tags (comma-separated):</label><br>
      <textarea name="tags"><?= htmlspecialchars($row['tags']) ?></textarea>
    </div>

    <input type="submit" value="Save Changes">
  </form>
  <br>
  <a href="train.php">← Back</a>
</body>
</html>
