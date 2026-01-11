<?php
// db/config.php

// Define BASE_DIR as the absolute path to your project root (gero_gpt folder)
// __DIR__ is C:\xampp\htdocs\gero_gpt\db
// dirname(__DIR__) goes up one level to C:\xampp\htdocs\gero_gpt
define('BASE_DIR', dirname(__DIR__));

$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = "";     // Default XAMPP password
$dbname = "gero_gpt"; // CORRECTED: This is now your actual database name

// --- MySQLi Connection (for train.php, etc.) ---
$conn = new mysqli($servername, $username, $password, $dbname);

// Check MySQLi connection
if ($conn->connect_error) {
    // Log the error and terminate. Do NOT expose detailed error messages in production.
    error_log("MySQLi Connection failed: " . $conn->connect_error);
    die("Database connection failed. Please try again later.");
}
$conn->set_charset("utf8mb4");

// --- PDO Connection (for submit_feedback.php, and generally recommended for new code) ---
$dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch results as associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Disable emulation for real prepared statements
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
    // Log the error and terminate. Do NOT expose detailed error messages in production.
    error_log("PDO Connection failed: " . $e->getMessage());
    die("Database connection failed. Please try again later.");
}

// --- Define Other Project Paths Relative to BASE_DIR ---
// These directories will be created if they don't exist
define('MEDIA_DIR', BASE_DIR . '/media');
define('UPLOAD_DIR', MEDIA_DIR . '/uploads'); // General upload directory, though some things go directly into subfolders
define('IMAGE_DIR', MEDIA_DIR . '/images'); // For face images uploaded via train.php
define('VIDEO_INPUT_DIR', BASE_DIR . '/deepfake_input/videos'); // Where original videos are stored
define('FRAME_DIR', BASE_DIR . '/deepfake_input/frames'); // Where extracted frames are stored
define('BLENDED_FRAME_DIR', BASE_DIR . '/deepfake_output/blended_frames'); // Where blended frames are stored
define('FINAL_VIDEO_DIR', BASE_DIR . '/deepfake_output/final_videos'); // Where final videos are exported
define('AUDIO_DIR', BASE_DIR . '/deepfake_output/audio'); // Where extracted audio is stored

// Ensure these directories exist and are writable
// 0777 grants full permissions (read, write, execute) to owner, group, and others.
// The 'true' parameter allows for recursive directory creation.
// In a production environment, you might use more restrictive permissions like 0755.
if (!is_dir(MEDIA_DIR)) mkdir(MEDIA_DIR, 0777, true);
if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0777, true);
if (!is_dir(IMAGE_DIR)) mkdir(IMAGE_DIR, 0777, true);
if (!is_dir(VIDEO_INPUT_DIR)) mkdir(VIDEO_INPUT_DIR, 0777, true);
if (!is_dir(FRAME_DIR)) mkdir(FRAME_DIR, 0777, true);
if (!is_dir(BLENDED_FRAME_DIR)) mkdir(BLENDED_FRAME_DIR, 0777, true);
if (!is_dir(FINAL_VIDEO_DIR)) mkdir(FINAL_VIDEO_DIR, 0777, true);
if (!is_dir(AUDIO_DIR)) mkdir(AUDIO_DIR, 0777, true);

// Configure PHP error logging to the project root
ini_set('display_errors', 0); // Important: Never show errors directly on production
ini_set('log_errors', 1);
ini_set('error_log', BASE_DIR . '/php_error.log');

?>