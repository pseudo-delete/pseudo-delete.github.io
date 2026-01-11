<?php
include 'db/config.php';

// --- Pagination ---
$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// --- Filters ---
$filterType = $_GET['type'] ?? '';
$filterLabel = $_GET['label'] ?? '';
$where = [];

if ($filterType) $where[] = "type = '" . $conn->real_escape_string($filterType) . "'";
if ($filterLabel) $where[] = "label LIKE '%" . $conn->real_escape_string($filterLabel) . "%'";
$whereSQL = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

// --- Total Count ---
$totalRes = $conn->query("SELECT COUNT(*) as total FROM media $whereSQL");
$total = $totalRes->fetch_assoc()['total'];
$totalPages = ceil($total / $limit);

// --- Fetch Records ---
$query = "SELECT * FROM media $whereSQL ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($query);

// --- Filter UI ---
echo '<form method="GET" style="margin-bottom: 20px;">
  <label>Type:
    <select name="type">
      <option value="">All</option>
      <option value="image"' . ($filterType == 'image' ? ' selected' : '') . '>Image</option>
      <option value="video"' . ($filterType == 'video' ? ' selected' : '') . '>Video</option>
      <option value="audio"' . ($filterType == 'audio' ? ' selected' : '') . '>Audio</option>
    </select>
  </label>
  <label>Label:
    <input type="text" name="label" value="' . htmlspecialchars($filterLabel) . '">
  </label>
  <button type="submit">Filter</button>
</form>';

// --- Media Table ---
echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Preview</th><th>Type</th><th>Label</th><th>Tags</th><th>Rating</th><th>Filename</th><th>Actions</th></tr>";

while ($row = $result->fetch_assoc()) {
  $type = $row['type'];
  $label = htmlspecialchars($row['label']);
  $tags = htmlspecialchars($row['tags'] ?? '');
  $rating = (int)($row['rating'] ?? 0);
  $filename = $row['filename'];
  $id = $row['id'];
  $filePath = "media/{$type}s/{$filename}";

  echo "<tr>";

  // --- Media Preview ---
  echo "<td>";
  if ($type === 'image') {
    echo "<img src='$filePath' style='max-width:100px; max-height:100px;'>";
  } elseif ($type === 'video') {
    echo "<video src='$filePath' style='max-width:100px; max-height:100px;' controls muted></video>";
  } elseif ($type === 'audio') {
    echo "<audio src='$filePath' controls></audio>";
  } else {
    echo "N/A";
  }
  echo "</td>";

  // --- Info Columns ---
  echo "<td>$type</td><td>$label</td><td>$tags</td><td>";
  for ($i = 1; $i <= 5; $i++) {
    $filled = $i <= $rating ? '★' : '☆';
    echo "<span class='star' data-id='$id' data-rating='$i'>$filled</span>";
  }
  echo "</td><td>$filename</td>";

  // --- Actions ---
  echo "<td>
    <a href='$filePath' target='_blank'>View</a> |
    <a href='edit_media.php?id=$id'>Edit</a> |
    <a href='delete_media.php?id=$id' onclick=\"return confirm('Delete this file?');\">Delete</a>
  </td>";

  echo "</tr>";
}
echo "</table>";

// --- Pagination Links ---
echo "<div style='margin-top:20px;'>";
for ($i = 1; $i <= $totalPages; $i++) {
  if ($i == $page) echo "<strong>$i</strong> ";
  else echo "<a href='?page=$i&type=$filterType&label=$filterLabel'>$i</a> ";
}
echo "</div>";
?>

<!-- Star Rating AJAX -->
<script>
document.querySelectorAll('.star').forEach(star => {
  star.addEventListener('click', () => {
    const id = star.dataset.id;
    const rating = star.dataset.rating;
    fetch('rate_media.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: 'id=' + id + '&rating=' + rating
    }).then(() => location.reload());
  });
});
</script>
