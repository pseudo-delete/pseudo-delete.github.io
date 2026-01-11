<?php
$zip = new ZipArchive();
$filename = "gero_gpt_export_" . time() . ".zip";

if ($zip->open($filename, ZipArchive::CREATE) === TRUE) {
  $paths = ['media/images', 'media/videos', 'media/audios', 'media/generated_images', 'media/generated_videos', 'media/generated_audios', 'logs/history.log'];
  foreach ($paths as $path) {
    if (is_dir($path)) {
      $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
      foreach ($files as $file) {
        if (!$file->isDir()) {
          $zip->addFile($file, $file);
        }
      }
    } elseif (is_file($path)) {
      $zip->addFile($path);
    }
  }
  $zip->close();
  header("Content-Type: application/zip");
  header("Content-Disposition: attachment; filename=$filename");
  header("Content-Length: " . filesize($filename));
  readfile($filename);
  unlink($filename);
} else {
  echo "Failed to create ZIP";
}
