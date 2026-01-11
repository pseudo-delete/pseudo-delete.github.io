<?php
$media = [];

function listFiles($dir, $type) {
    $files = array_diff(scandir($dir), array('.', '..'));
    $result = [];

    foreach ($files as $file) {
        $result[] = [
            'type' => $type,
            'filename' => $file,
            'label' => pathinfo($file, PATHINFO_FILENAME)
        ];
    }

    return $result;
}

$media = array_merge(
    listFiles("assets/uploads/images", "image"),
    listFiles("assets/uploads/videos", "video"),
    listFiles("assets/uploads/audios", "audio")
);

header('Content-Type: application/json');
echo json_encode($media);
