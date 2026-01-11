<?php
include 'db/config.php';

$mediaTypes = [
  'image' => 'media/generated_images',
  'video' => 'media/generated_videos',
  'audio' => 'media/generated_audios'
];

$itemsPerPage = 5;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$typeFilter = $_GET['type'] ?? null;

// Optional sorting for future upgrades
$sortBy = $_GET['sort'] ?? 'generated_at'; // default: time
$order = 'DESC';

// Fetch media logs per type
function getMediaLogEntries($conn, $type, $sortBy, $order) {
  $validSorts = ['generated_at', 'filename']; // extend as needed
  $sortBy = in_array($sortBy, $validSorts) ? $sortBy : 'generated_at';

  $stmt = $conn->prepare("SELECT filename, prompt, generated_at FROM generation_log WHERE type = ? ORDER BY $sortBy $order");
  $stmt->bind_param("s", $type);
  $stmt->execute();
  return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Handle Deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
  $fileToDelete = $_POST['delete'];
  if (file_exists($fileToDelete)) {
    unlink($fileToDelete);
    header("Location: " . $_SERVER['PHP_SELF'] . '?' . http_build_query($_GET));
    exit;
  } else {
    $deletionError = "Error: File not found.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Generated Output</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .media-preview { margin: 20px 0; }
    .meta { font-size: 0.9em; color: #555; }
    .tooltip:hover::after {
      content: attr(data-prompt);
      position: absolute;
      background: #333;
      color: #fff;
      padding: 6px;
      border-radius: 5px;
      font-size: 0.8em;
      white-space: pre-wrap;
      max-width: 300px;
      top: 100%;
      left: 0;
    }
    .filter-bar a {
      margin-right: 10px;
      text-decoration: none;
    }
    form.delete-form { display: inline; }
    .pagination a { margin-right: 5px; }
  </style>
</head>
<body>
  <h1>🎨 Generated Output</h1>
  <nav>
    <a href="index.html">🧠 Chat & Generate</a>
    <a href="train.php">📂 Train / Manage Media</a>
    <a href="generated_output.php">🎨 Generated Output</a>
    <a href="help.html">❓ Help</a>
    <a href="rate.php">Rate Generated Media</a>
    <a href="filtered_gallery.php">🔍 View Filtered Gallery</a>
  </nav>

  <?php if (isset($deletionError)) echo "<p style='color:red'>$deletionError</p>"; ?>

  <!-- Filter Bar -->
  <div class="filter-bar">
    <strong>Filter by Type:</strong>
    <?php foreach ($mediaTypes as $key => $_): ?>
      <a href="?type=<?= $key ?>" <?= ($typeFilter === $key) ? "style='font-weight:bold'" : "" ?>><?= ucfirst($key) ?></a>
    <?php endforeach; ?>
    <a href="generated_output.php" <?= $typeFilter === null ? "style='font-weight:bold'" : "" ?>>All</a>
  </div>
  <hr>

  <?php
  foreach ($mediaTypes as $type => $dir):
    if ($typeFilter && $type !== $typeFilter) continue;

    echo "<h2>🗂️ " . ucfirst($type) . "s</h2>";

    $entries = getMediaLogEntries($conn, $type, $sortBy, $order);
    $totalPages = ceil(count($entries) / $itemsPerPage);
    $offset = ($page - 1) * $itemsPerPage;
    $display = array_slice($entries, $offset, $itemsPerPage);

    if (count($display) === 0) {
      echo "<p>No generated $type files found.</p>";
      continue;
    }

    foreach ($display as $entry):
      $file = htmlspecialchars($entry['filename']);
      $prompt = htmlspecialchars($entry['prompt']);
      $mtime = date("Y-m-d H:i:s", strtotime($entry['generated_at']));
      $fullPath = "$dir/$file";
      if (!file_exists($fullPath)) continue;
  ?>
      <div class="media-preview tooltip" data-prompt="Prompt: <?= $prompt ?>">
        <?php if ($type === 'image'): ?>
          <img src="<?= $fullPath ?>" width="300" loading="lazy"><br>
        <?php elseif ($type === 'video'): ?>
          <video src="<?= $fullPath ?>" width="300" controls></video><br>
        <?php elseif ($type === 'audio'): ?>
          <audio src="<?= $fullPath ?>" controls></audio><br>
        <?php endif; ?>

        <div class="meta">
          🕒 <?= $mtime ?><br>
          🔖 Prompt: <?= $prompt ?><br>
          🔗 <a href="<?= $fullPath ?>" download>Download</a>
        </div>

        <form method="post" class="delete-form" onsubmit="return confirm('Delete <?= $file ?>?')">
          <input type="hidden" name="delete" value="<?= htmlspecialchars($fullPath) ?>">
          <button type="submit">🗑️ Delete</button>
        </form>
      </div>
      <hr>
  <?php
    endforeach;

    // Pagination
    if ($totalPages > 1):
      echo "<div class='pagination'><strong>Pages:</strong> ";
      for ($i = 1; $i <= $totalPages; $i++):
        $link = "?type=$type&page=$i";
        echo "<a href='$link'" . ($i === $page ? " style='font-weight:bold'" : "") . ">$i</a> ";
      endfor;
      echo "</div>";
    endif;

  endforeach;
  ?>
</body>
</html>
