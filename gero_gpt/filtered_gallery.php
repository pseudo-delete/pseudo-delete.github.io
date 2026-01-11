<?php
$pdo = new PDO("mysql:host=localhost;dbname=gero_gpt", "root", "");

$minRating = $_GET['min_rating'] ?? 1;
$searchTags = $_GET['tags'] ?? '';
$typeFilter = $_GET['type'] ?? 'image';

// Build SQL
$sql = "SELECT * FROM media_ratings WHERE rating >= :minRating AND type = :type";
$params = ['minRating' => $minRating, 'type' => $typeFilter];

if (!empty($searchTags)) {
    $sql .= " AND tags LIKE :tags";
    $params['tags'] = '%' . $searchTags . '%';
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Filtered Gallery</title>
</head>
<body>
    <h1>Filtered Media Gallery</h1>

    <form method="get">
        <label>Type:</label>
        <select name="type">
            <option value="image" <?= $typeFilter === 'image' ? 'selected' : '' ?>>Image</option>
            <option value="video" <?= $typeFilter === 'video' ? 'selected' : '' ?>>Video</option>
            <option value="audio" <?= $typeFilter === 'audio' ? 'selected' : '' ?>>Audio</option>
        </select>

        <label>Minimum Rating:</label>
        <input type="number" name="min_rating" min="1" max="5" value="<?= htmlspecialchars($minRating) ?>">

        <label>Tags (comma separated):</label>
        <input type="text" name="tags" value="<?= htmlspecialchars($searchTags) ?>">

        <button type="submit">Filter</button>
    </form>

    <hr>

    <?php if (count($results) === 0): ?>
        <p>No results found.</p>
    <?php else: ?>
        <div style="display:flex; flex-wrap:wrap;">
        <?php foreach ($results as $row): ?>
            <div style="margin:10px;">
                <?php if ($typeFilter === 'image'): ?>
                    <img src="media/rated/images/<?= $row['filename'] ?>" width="200"><br>
                <?php elseif ($typeFilter === 'video'): ?>
                    <video width="320" controls><source src="media/rated/videos/<?= $row['filename'] ?>"></video><br>
                <?php else: ?>
                    <audio controls><source src="media/rated/audio/<?= $row['filename'] ?>"></audio><br>
                <?php endif; ?>

                <strong>Rating:</strong> <?= $row['rating'] ?><br>
                <strong>Tags:</strong> <?= htmlspecialchars($row['tags']) ?><br>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</body>
</html>
