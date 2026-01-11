<?php
// File: submit_feedback.php
// Handles submission of user feedback (e.g., whether to include audio, video feedback)

header('Content-Type: application/json');

// Error logging setup (recommended for development/debugging)
ini_set('display_errors', 0); // Hide errors from browser
ini_set('log_errors', 1);    // Log errors to file
ini_set('error_log', __DIR__ . '/php_error.log'); // Log to project root's error log

// *** IMPORTANT: Include config.php FIRST to define BASE_DIR, $pdo, and other paths ***
// config.php is located in the 'db' subdirectory relative to submit_feedback.php
include_once __DIR__ . '/db/config.php'; // Corrected include path

// Ensure $pdo is available after including config.php
if (!isset($pdo) || !$pdo instanceof PDO) {
    error_log("Fatal Error: PDO connection not established in submit_feedback.php.");
    echo json_encode(['status' => 'error', 'message' => 'Database connection not available.']);
    exit;
}

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $video_db_id = filter_input(INPUT_POST, 'video_db_id', FILTER_VALIDATE_INT);
    $frame_number = filter_input(INPUT_POST, 'frame_number', FILTER_VALIDATE_INT);
    $face_id = filter_input(INPUT_POST, 'face_id', FILTER_SANITIZE_FULL_SPECIAL_CHARS); // This might be filename or ID
    $include_audio = filter_input(INPUT_POST, 'include_audio', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    $action_type = filter_input(INPUT_POST, 'action_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS); // e.g., 'feedback_audio', 'feedback_video'
    $video_feedback = filter_input(INPUT_POST, 'video_feedback', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT); // Assuming a user ID exists or is derived

    // Basic validation
    if ($video_db_id === false || $video_db_id === null ||
        $frame_number === false || $frame_number === null) {
        $response['message'] = "Invalid or missing video_db_id or frame_number.";
        error_log("Submit Feedback Error: Invalid input for video_db_id or frame_number.");
        echo json_encode($response);
        exit;
    }

    // Determine the action and update the database
    try {
        if ($action_type === 'feedback_audio' && $include_audio !== null) {
            // Update audio preference for the video
            $stmt = $pdo->prepare("UPDATE videos SET include_audio = :include_audio WHERE id = :video_db_id");
            $stmt->bindParam(':include_audio', $include_audio, PDO::PARAM_BOOL);
            $stmt->bindParam(':video_db_id', $video_db_id, PDO::PARAM_INT);
            $stmt->execute();
            $response['status'] = 'success';
            $response['message'] = "Audio preference updated successfully.";
            error_log("Audio preference updated for video ID {$video_db_id}: " . ($include_audio ? 'Yes' : 'No'));

        } elseif ($action_type === 'feedback_video' && $video_feedback !== null) {
            // Store general video feedback (e.g., 'liked', 'disliked', 'needs_improvement')
            // This might go into a separate feedback table or a column on the videos table
            // For simplicity, let's assume a feedback column or a new feedback log table.
            // Example: updating a column 'feedback_text' in 'videos' table for this video.
            // A more robust system would involve a dedicated 'feedback' table.

            $stmt = $pdo->prepare("UPDATE videos SET video_feedback = :video_feedback WHERE id = :video_db_id");
            $stmt->bindParam(':video_feedback', $video_feedback, PDO::PARAM_STR);
            $stmt->bindParam(':video_db_id', $video_db_id, PDO::PARAM_INT);
            $stmt->execute();
            $response['status'] = 'success';
            $response['message'] = "Video feedback submitted successfully.";
            error_log("Video feedback submitted for video ID {$video_db_id}: {$video_feedback}");

        } else {
            $response['message'] = "Invalid action type or missing feedback data.";
            error_log("Submit Feedback Error: Invalid action type '{$action_type}' or missing data.");
        }

    } catch (PDOException $e) {
        $response['message'] = "Database error: " . $e->getMessage();
        error_log("Submit Feedback Database Error: " . $e->getMessage());
    }

} else {
    $response['message'] = "Invalid request method.";
    error_log("Submit Feedback Error: Invalid request method: " . $_SERVER['REQUEST_METHOD']);
}

echo json_encode($response);
?>