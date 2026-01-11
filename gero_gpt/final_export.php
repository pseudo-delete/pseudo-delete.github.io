<?php
// File: final_export.php (PATCHED)

header('Content-Type: application/json'); // Ensure JSON header is sent

// --- Configuration (adjust as needed) ---
$ffmpegPath = 'C:/xampp/htdocs/tools/bin/ffmpeg.exe'; // Make sure this path is correct
$baseDir = __DIR__; // This should resolve to C:/xampp/htdocs/gero_gpt/

// Get parameters from GET request
$video = $_GET['video'] ?? ''; // Full video filename (e.g., "my_video.mp4")
$audioUrl = $_GET['audio'] ?? ''; // Web-accessible URL to the extracted audio (e.g., /gero_gpt/deepfake_input/audio/my_video/audio.mp3)

// Input and Output Directories
$inputVideoDir = $baseDir . '/deepfake_input/videos/';
$inputFramesDir = $baseDir . '/deepfake_input/frames/';
$inputAudioBaseDir = $baseDir . '/deepfake_input/audio/'; // Base for extracted audio files

$outputBaseDir = $baseDir . '/deepfake_output/';

// Derive unique ID from video filename for folder structuring
$videoId = pathinfo($video, PATHINFO_FILENAME); // e.g., "my_video"

// Specific input paths
$originalVideoPath = $inputVideoDir . $video; // Path to original uploaded video
$blendedFramesPattern = $inputFramesDir . $videoId . '/frame_*.jpg'; // Pattern for generated frames

// Output video name and path
$outputVideoName = "deepfaked_" . $videoId . ".mp4";
$finalOutputVideoPath = $outputBaseDir . $outputVideoName;

// --- Validate Inputs ---
if (empty($video)) {
    echo json_encode(['status' => 'error', 'message' => 'Video filename is missing.']);
    exit();
}
if (!file_exists($originalVideoPath)) {
    echo json_encode(['status' => 'error', 'message' => 'Original video file not found: ' . $originalVideoPath]);
    exit();
}

// Get all blended frames for this video
$blendedFrames = glob($inputFramesDir . $videoId . '/frame_*.jpg');
if (empty($blendedFrames)) {
    echo json_encode(['status' => 'error', 'message' => 'No blended frames found for video: ' . $videoId]);
    exit();
}

// --- Create temporary directory for FFmpeg input ---
$tmpDirName = "tmp_export_" . uniqid() . '_' . $videoId; // Use unique ID to prevent conflicts
$tmpDirPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $tmpDirName; // Use system temp dir for better practice

// Fallback to project's tmp if system temp not writable (less ideal but works)
if (!is_writable(sys_get_temp_dir())) {
    $tmpDirPath = $baseDir . '/tmp_exports/' . $tmpDirName;
}

if (!is_dir($tmpDirPath) && !mkdir($tmpDirPath, 0777, true)) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to create temporary export directory. Check permissions for ' . $tmpDirPath]);
    exit();
}

// --- Copy blended frames to the temporary directory ---
// FFmpeg expects frame sequence to be tightly packed (e.g., frame_001.jpg, frame_002.jpg)
// The existing `glob` for `deepfake_input/frames/$video/frame_*.jpg` and `natsort` is good for ordering.
// However, the `frame_extractor.php` already produces `frame_%03d.jpg`, so direct copying should be fine.
$frameFilesToCopy = glob($inputFramesDir . $videoId . '/frame_*.jpg');
natsort($frameFilesToCopy); // Ensure correct numerical order

$copiedFrameCount = 0;
foreach ($frameFilesToCopy as $index => $framePath) {
    // FFmpeg needs frames to start from 1, so use $index + 1
    $destFrameName = 'frame_' . str_pad($index + 1, 3, '0', STR_PAD_LEFT) . '.jpg';
    if (!copy($framePath, $tmpDirPath . DIRECTORY_SEPARATOR . $destFrameName)) {
        error_log("FFmpeg export: Failed to copy frame {$framePath} to {$tmpDirPath}/{$destFrameName}");
        // Decide if this is a fatal error or just a warning
    } else {
        $copiedFrameCount++;
    }
}

if ($copiedFrameCount === 0) {
    // Clean up temp dir before exiting
    array_map('unlink', glob($tmpDirPath . '/*'));
    @rmdir($tmpDirPath);
    echo json_encode(['status' => 'error', 'message' => 'No frames were successfully copied to the temporary directory for export.']);
    exit();
}


// --- Determine audio input ---
$audioInput = ''; // Default to no audio input
$audioLogMessage = 'No specific audio file for export.';

// Check if a web-accessible audio URL was passed from the frontend
if (!empty($audioUrl)) {
    // Convert web URL to server path
    // Remove the webBaseUrl part to get the relative path from gero_gpt/
    $relativePath = str_replace('/gero_gpt/', '', $audioUrl);
    $serverAudioPath = $baseDir . '/' . $relativePath;

    if (file_exists($serverAudioPath) && filesize($serverAudioPath) > 0) {
        $audioInput = '-i ' . escapeshellarg($serverAudioPath);
        $audioLogMessage = 'Using extracted audio: ' . $serverAudioPath;
    } else {
        error_log("Final Export Warning: Explicit audio file specified but not found or empty: " . $serverAudioPath);
        $audioLogMessage = 'Explicit audio file specified but not found/empty. Will attempt to use original video audio.';
        // Fallback to original video audio if the explicit one isn't valid
        $audioInput = '-i ' . escapeshellarg($originalVideoPath) . ' -map 1:a:0? ';
    }
} else {
    // If no explicit audio URL was passed from frontend, attempt to use original video's audio
    $audioInput = '-i ' . escapeshellarg($originalVideoPath) . ' -map 1:a:0? '; // map 1:a:0? attempts to map stream 0 from second input (original video)
    $audioLogMessage = 'No explicit audio. Attempting to use audio from original video: ' . $originalVideoPath;
}


// --- FFmpeg Command for Video Assembly ---
// Input frames from temporary directory, then audio input
// -framerate: This should match your desired video FPS (e.g., 25 or 30).
// -i $tmpDirPath/frame_%03d.jpg: Input pattern for frames from the temp directory.
// -map 0:v:0: Map video stream from the first input (frames).
// -c:v libx264: Video codec (H.264).
// -preset medium: Compression preset (medium is a good balance).
// -crf 23: Constant Rate Factor for video quality (lower is better quality, larger file).
// -pix_fmt yuv420p: Essential for broad compatibility (especially web playback).
// -c:a aac -b:a 128k: Audio codec and bitrate (if audio input is provided).
// -y: Overwrite output file without asking.
$ffmpegCmd = escapeshellarg($ffmpegPath) . " -y -framerate 25 -i " . escapeshellarg($tmpDirPath . DIRECTORY_SEPARATOR . 'frame_%03d.jpg') . " ";

// Add audio input only if it's determined
if (!empty($audioInput)) {
    $ffmpegCmd .= $audioInput;
    $ffmpegCmd .= " -map 0:v:0 "; // Explicitly map video from the frame sequence (input 0)
    // If audio came from the original video (input 1 in that case), map its audio
    if (strpos($audioInput, '-map 1:a:0?') !== false) {
        $ffmpegCmd .= " -map 1:a:0? "; // Map audio from the original video as the second input
    } else {
        // If audio came from the separate extracted audio file (which would be input 1)
        $ffmpegCmd .= " -map 1:a:0 "; // Map audio from the audio file as the second input
    }
    $ffmpegCmd .= " -c:v libx264 -preset medium -crf 23 -c:a aac -b:a 192k -pix_fmt yuv420p " . escapeshellarg($finalOutputVideoPath) . " 2>&1";
} else {
    // No audio input, export video only
    $ffmpegCmd .= " -map 0:v:0 -c:v libx264 -preset medium -crf 23 -pix_fmt yuv420p -an " . escapeshellarg($finalOutputVideoPath) . " 2>&1"; // -an for no audio
}


$ffmpegLogFile = $outputBaseDir . $videoId . '_export_ffmpeg_log.txt'; // Log for this specific export
$outputLines = [];
$exitCode = 0;

error_log("Final Export Command: " . $ffmpegCmd); // Log the full command for debugging
error_log("Audio Source: " . $audioLogMessage);

exec($ffmpegCmd, $outputLines, $exitCode);
file_put_contents($ffmpegLogFile, implode("\n", $outputLines));

// --- Clean up temporary directory ---
$cleanupAttempts = 0;
do {
    $cleanupSuccess = true;
    try {
        array_map('unlink', glob($tmpDirPath . '/*'));
        @rmdir($tmpDirPath); // @ suppresses error if dir not empty (e.g., if unlink failed)
    } catch (Exception $e) {
        $cleanupSuccess = false;
        error_log("Error during temp dir cleanup (attempt " . ($cleanupAttempts + 1) . "): " . $e->getMessage());
        sleep(1); // Wait a bit and retry
    }
    $cleanupAttempts++;
} while (!$cleanupSuccess && $cleanupAttempts < 3); // Retry a few times


// --- Validate Output and Respond ---
if ($exitCode === 0 && file_exists($finalOutputVideoPath) && filesize($finalOutputVideoPath) > 0) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Video exported successfully!',
        'output' => '/gero_gpt/deepfake_output/' . $outputVideoName, // Web-accessible path
        'ffmpeg_log' => basename($ffmpegLogFile)
    ]);
} else {
    $errorMessage = 'FFmpeg video export failed. Check FFmpeg log for details.';
    if ($exitCode !== 0) {
        $errorMessage .= " (Exit Code: $exitCode)";
    }
    if (!file_exists($finalOutputVideoPath)) {
        $errorMessage .= " (Output file not found)";
    } elseif (filesize($finalOutputVideoPath) === 0) {
        $errorMessage .= " (Output file is empty)";
    }

    echo json_encode([
        'status' => 'error',
        'message' => $errorMessage,
        'ffmpeg_log' => basename($ffmpegLogFile),
        'details' => implode("\n", $outputLines) // Full FFmpeg output for debugging
    ]);
}
?>