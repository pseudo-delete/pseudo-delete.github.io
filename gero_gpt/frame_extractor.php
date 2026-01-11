<?php
// File: frame_extractor.php

// Always set the content type to JSON for API responses
header('Content-Type: application/json');

// --- Helper Function for Standardized JSON Responses ---
function sendJsonResponse($status, $message, $data = []) {
    echo json_encode(array_merge(['status' => $status, 'message' => $message], $data));
    exit;
}

// --- Configuration ---
// Adjust these paths if frame_extractor.php is not in the gero_gpt directory
$ffmpegPath = 'C:/xampp/htdocs/tools/bin/ffmpeg.exe'; // Verify this is correct
$baseDir = __DIR__; // This should resolve to C:/xampp/htdocs/gero_gpt/
$videoInputDir = $baseDir . '/deepfake_input/videos/';
$frameBaseDir = $baseDir . '/deepfake_input/frames/';
$audioBaseDir = $baseDir . '/deepfake_input/audio/'; // Directory for extracted audio

// IMPORTANT: Define the web base URL for your project
// This is the part that goes after http://localhost/ for accessing resources
$webBaseUrl = '/gero_gpt/'; // Adjust this if your project is not directly under /gero_gpt/

// Add MySQL DB connection details
$db_host = 'localhost';
$db_name = 'gero_gpt'; // Your database name
$db_user = 'root';
$db_pass = '';
$db_charset = 'utf8mb4';

// --- Database Connection ---
try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (\PDOException $e) {
    error_log("Database Connection Error in frame_extractor.php: " . $e->getMessage());
    sendJsonResponse('error', 'Database connection failed. Please check server logs.');
}

// Get the video unique filename AND its DB ID from the POST request
$videoUniqueFilename = $_POST['video_name'] ?? null; // Still using this for file paths
$videoDbId = $_POST['video_db_id'] ?? null; // NEW: Get the database ID

if (!$videoUniqueFilename || !$videoDbId) {
    sendJsonResponse('error', 'Video information (filename or DB ID) not received by the server for frame extraction.');
}

// Fetch video record from DB to ensure it exists and get full paths
$videoRecord = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM videos WHERE id = ? AND unique_filename = ?");
    $stmt->execute([$videoDbId, $videoUniqueFilename]);
    $videoRecord = $stmt->fetch();
    if (!$videoRecord) {
        sendJsonResponse('error', 'Video record not found in database for extraction. It might have been deleted or an invalid ID was provided.', ['video_db_id' => $videoDbId, 'video_unique_filename' => $videoUniqueFilename]);
    }
} catch (\PDOException $e) {
    error_log("DB Fetch Error in frame_extractor.php: " . $e->getMessage());
    sendJsonResponse('error', 'Failed to retrieve video record from database for extraction.');
}

$videoPath = $videoRecord['video_storage_path']; // Use path from DB for the original video
$videoId = pathinfo($videoUniqueFilename, PATHINFO_FILENAME); // ID for folder naming (e.g. video_abcdef123)
$frameOutputDir = $videoRecord['frames_storage_dir']; // Use path from DB for frames output directory
$audioOutputDir = $audioBaseDir . $videoId . '/'; // Specific audio output directory for this video (construct this, can be stored in DB later)


// --- Directory Setup ---
// Create necessary directories if they don't exist
$requiredDirs = [$frameOutputDir, $audioOutputDir]; // Only need to ensure these are created if not already by deepfake_upload
foreach ($requiredDirs as $dir) {
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0777, true)) {
            sendJsonResponse('error', 'Failed to create directory: ' . $dir . '. Please check server permissions.');
        }
    }
}


// Check if the video file actually exists
if (!file_exists($videoPath)) {
    // If the video file is missing, update DB status and send error
    try {
        $stmt = $pdo->prepare("UPDATE videos SET status = 'failed_extraction_source_missing' WHERE id = ?");
        $stmt->execute([$videoDbId]);
    } catch (\PDOException $e) {
        error_log("DB Update Error (source missing) in frame_extractor.php: " . $e->getMessage());
    }
    sendJsonResponse('error', 'Source video file not found on server at path: ' . $videoPath, ['debug_path' => $videoPath]);
}

// --- FFmpeg Command Execution (Frame Extraction) ---
$ffmpegFrameLogFile = $frameOutputDir . 'ffmpeg_frames_output.txt'; // Separate log for frames
$frameCmd = escapeshellarg($ffmpegPath) . ' -hide_banner -loglevel error -i ' . escapeshellarg($videoPath) .
            ' -qscale:v 2 "' . $frameOutputDir . 'frame_%03d.jpg" 2>&1';

$frameOutputLines = [];
$frameExitCode = 0;
exec($frameCmd, $frameOutputLines, $frameExitCode);

file_put_contents($ffmpegFrameLogFile, implode("\n", $frameOutputLines));

// --- Validate Frame Output ---
$frames = glob($frameOutputDir . 'frame_*.jpg'); // Find all generated JPG frames
$frameCount = count($frames);

if ($frameExitCode !== 0 || !$frames || $frameCount === 0) {
    // If FFmpeg failed or no frames were found, prepare a detailed error response.
    $ffmpegErrorDetails = file_exists($ffmpegFrameLogFile) ? file_get_contents($ffmpegFrameLogFile) : 'FFmpeg log file not found or could not be read.';
    
    // Update video status to failed extraction in DB
    try {
        $stmt = $pdo->prepare("UPDATE videos SET status = 'failed_extraction', frame_count = ?, uploaded_at = uploaded_at WHERE id = ?"); // Preserve uploaded_at
        $stmt->execute([$frameCount, $videoDbId]);
    } catch (\PDOException $e) {
        error_log("DB Update Error (failed frames) in frame_extractor.php: " . $e->getMessage());
    }

    sendJsonResponse('error', 'FFmpeg failed to extract frames or produced no output. Please check the details for more information.', [
        'details' => $ffmpegErrorDetails, // The raw error messages from FFmpeg
        'exit_code' => $frameExitCode,    // The numeric exit code from FFmpeg
        'frames_found' => $frameCount,    // Number of frames actually found (should be 0 on failure)
        'attempted_path' => $videoPath
    ]);
}

// --- FFmpeg Command Execution (Audio Extraction) ---
$ffmpegAudioLogFile = $audioOutputDir . 'ffmpeg_audio_output.txt'; // Separate log for audio
$outputAudioPath = $audioOutputDir . "audio.mp3"; // Standard name for extracted audio

// Command: -vn (no video), -acodec libmp3lame (MP3 codec), -q:a 2 (audio quality)
$audioCmd = escapeshellarg($ffmpegPath) . ' -hide_banner -loglevel error -i ' . escapeshellarg($videoPath) .
            ' -vn -acodec libmp3lame -q:a 2 ' . escapeshellarg($outputAudioPath) . ' 2>&1';

$audioOutputLines = [];
$audioExitCode = 0;
exec($audioCmd, $audioOutputLines, $audioExitCode);

file_put_contents($ffmpegAudioLogFile, implode("\n", $audioOutputLines));

// --- Validate Audio Output and Clean Up if Empty/Failed ---
$audioExtracted = false;
$audioUrl = null;
$dbAudioPath = null; // Path to store in DB

if (file_exists($outputAudioPath) && filesize($outputAudioPath) > 0) {
    // Audio file exists and is not empty
    $audioExtracted = true;
    $audioUrl = $webBaseUrl . 'deepfake_input/audio/' . $videoId . '/' . basename($outputAudioPath);
    $dbAudioPath = $audioUrl; // Store the web path in the DB
} else {
    // Audio extraction failed or resulted in an empty file
    error_log("FFmpeg audio extraction warning for {$videoPath}. Exit Code: {$audioExitCode}. Output: " . implode("\n", $audioOutputLines));
    
    // Clean up the potentially empty/failed audio file
    if (file_exists($outputAudioPath)) {
        unlink($outputAudioPath);
        error_log("Cleaned up empty or failed audio file: {$outputAudioPath}");
    }
    // Optionally remove the audio directory if it's now empty after cleanup (excluding . and ..)
    if (is_dir($audioOutputDir) && count(array_diff(scandir($audioOutputDir), ['.', '..'])) == 0) {
        rmdir($audioOutputDir);
        error_log("Cleaned up empty audio directory: {$audioOutputDir}");
    }
}

// --- Update 'videos' table with frame_count and audio_path ---
try {
    $stmt = $pdo->prepare("UPDATE videos SET frame_count = ?, audio_path = ?, status = 'frames_extracted', uploaded_at = uploaded_at WHERE id = ?"); // Preserve uploaded_at
    $stmt->execute([$frameCount, $dbAudioPath, $videoDbId]);
} catch (\PDOException $e) {
    error_log("DB Update Error (frames/audio) in frame_extractor.php: " . $e->getMessage());
    // This is a non-fatal error for the user, but important for logs
}


// --- Success Response ---
// Construct the full web URL for the first frame
$firstFrameUrl = null;
if (isset($frames[0])) {
    // Reconstruct the web path: /gero_gpt/deepfake_input/frames/{videoId}/frame_001.jpg
    $firstFrameUrl = $webBaseUrl . 'deepfake_input/frames/' . $videoId . '/' . basename($frames[0]);
}

sendJsonResponse('success', 'Frames and audio extraction completed!', [
    'frame_count' => $frameCount,
    'first_frame' => isset($frames[0]) ? basename($frames[0]) : null,
    'frame_path' => $firstFrameUrl,
    'audio_extracted' => $audioExtracted, // Indicate if audio was successfully extracted
    'audio_path' => $audioUrl,            // Web URL to the extracted audio, or null
    'audio_log' => basename($ffmpegAudioLogFile) // Path to the audio extraction log for debugging
]);

?>