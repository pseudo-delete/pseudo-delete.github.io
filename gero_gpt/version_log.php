<?php
include 'db.php';

$stmt = $pdo->query("SELECT * FROM generated_log ORDER BY created_at DESC");
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Version Control Log</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>🕒 Generated Media History</h2>
  <a href="index.html">← Back</a>

  <?php if (count($logs) === 0): ?>
    <p>No generated media yet.</p>
  <?php else: ?>
    <table border="1" cellpadding="6">
      <tr>
        <th>ID</th>
        <th>Filename</th>
        <th>Type</th>
        <th>Method</th>
        <th>Date</th>
        <th>Preview</th>
      </tr>
      <?php foreach ($logs as $row): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['filename']) ?></td>
          <td><?= $row['type'] ?></td>
          <td><?= $row['method'] ?></td>
          <td><?= $row['created_at'] ?></td>
          <td>
            <?php
              $folder = 'media/generated_' . $row['type'] . 's/' . $row['filename'];
              if ($row['type'] === 'image') {
                echo "<img src='$folder' style='max-height: 80px'>";
              } elseif ($row['type'] === 'video') {
                echo "<video src='$folder' controls style='max-height: 80px'></video>";
              } elseif ($row['type'] === 'audio') {
                echo "<audio src='$folder' controls></audio>";
              }
            ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
</body>
</html>
