<?php
$types = ['images', 'videos', 'audios'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rate Generated Media</title>
</head>
<body>
    <nav>
        <a href="index.html">🧠 Chat & Generate</a>
        <a href="train.php">📂 Train / Manage Media</a>
        <a href="generated_output.php">🎨 Generated Output</a>
        <a href="help.html">❓ Help</a>
        <a href="rate.php">Rate Generated Media</a>
        <a href="filtered_gallery.php">🔍 View Filtered Gallery</a>
    </nav>
    <h1>Rate Generated Media</h1>
    <?php foreach ($types as $type): ?>
        <h2><?= ucfirst($type) ?></h2>
        <?php
        $dir = __DIR__ . "/media/generated_$type/";
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file):
        ?>
            <div style="margin-bottom: 20px;">
                <?php if ($type === 'images'): ?>
                    <img src="media/generated_<?= $type ?>/<?= $file ?>" width="200"><br>
                <?php elseif ($type === 'videos'): ?>
                    <video width="320" controls><source src="media/generated_<?= $type ?>/<?= $file ?>"></video><br>
                <?php else: ?>
                    <audio controls><source src="media/generated_<?= $type ?>/<?= $file ?>"></audio><br>
                <?php endif; ?>

                <form action="save_rating.php" method="post">
                    <input type="hidden" name="filename" value="<?= $file ?>">
                    <input type="hidden" name="type" value="<?= rtrim($type, 's') ?>">
                    Rating (1-5): <input type="number" name="rating" min="1" max="5" required>
                    Tags: <input type="text" name="tags" placeholder="e.g., forest, anime, soft-light">
                    <button type="submit">Submit</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
</body>
</html>
