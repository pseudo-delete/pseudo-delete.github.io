// js/deepfake.js (Part 1: Core Setup and Global State)

// --- Global App State Object ---
// All "global" variables are now properties of this object to avoid re-declaration errors
let appState = {
    currentVideo: '',         // Stores the unique filename of the currently uploaded video (e.g., video_xyz.mp4)
    currentVideoDbId: null,   // Stores the database ID of the currently uploaded video
    currentFaceImage: '',     // Stores the filename of the currently selected face image (e.g., face_abc.jpg)
    totalFrames: 0,           // Total number of frames extracted from the video
    currentFrame: 1,          // Current frame being displayed/processed
    currentGeneration: 1,     // Current blending generation (for iterating through multiple blended images if available)
    originalFrameCropper: null, // Cropper.js instance for the original frame
    sourceFaceCropper: null,  // Cropper.js instance for the source face image
    currentAudioPath: '',     // Path to the extracted audio file
    sourceFaceCropData: { x: 0, y: 0, width: 0, height: 0 } // Store source face crop data if user defines it
};

// --- Utility Functions ---

// Function to show UI messages to the user
function showUIMessage(message, type = 'info', duration = 3000) {
    const messageDiv = document.getElementById('message-area');
    if (!messageDiv) return;

    // Allow HTML in messages if needed (e.g., for download links)
    messageDiv.innerHTML = message;
    messageDiv.className = `alert alert-${type}`; // Uses Bootstrap alert styles (from your CSS comments)
    messageDiv.style.display = 'block';

    if (duration > 0) {
        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, duration);
    }
}

// Function to disable/enable controls during processing
function disableControls(disable) {
    // Select all elements with the 'control-element' class
    const controls = document.querySelectorAll('.control-element');
    controls.forEach(control => {
        control.disabled = disable;
    });
}

// Function to clear frame previews
function clearFramePreview() {
    document.getElementById('original-frame').src = ''; // Clear image source
    document.getElementById('blended-output').src = '';
    document.getElementById('output-preview').src = '';
    document.getElementById('feedback-label').textContent = '';
    document.getElementById('coord-feedback').textContent = '';
    
    if (appState.originalFrameCropper) {
        appState.originalFrameCropper.destroy();
        appState.originalFrameCropper = null;
    }
}

// Function to update frame number and progress bar in the UI
function updateFrameUI() {
    document.getElementById('current-frame-num').textContent = appState.currentFrame;
    document.getElementById('total-frames-num').textContent = appState.totalFrames;
    const progressBar = document.getElementById('frame-progress');
    if (appState.totalFrames > 0) {
        const progress = (appState.currentFrame / appState.totalFrames) * 100;
        progressBar.style.width = `${progress}%`;
        progressBar.setAttribute('aria-valuenow', progress);
    } else {
        progressBar.style.width = '0%';
        progressBar.setAttribute('aria-valuenow', 0);
    }

    // Disable next/prev buttons if at limits
    document.getElementById('prev-frame-btn').disabled = appState.currentFrame <= 1;
    document.getElementById('next-frame-btn').disabled = appState.currentFrame >= appState.totalFrames;
}

// Function to reset all
function resetAll() {
    // Clear global variables via appState
    appState.currentVideo = '';
    appState.currentVideoDbId = null;
    appState.currentFaceImage = '';
    appState.totalFrames = 0;
    appState.currentFrame = 1;
    appState.currentGeneration = 1;
    appState.currentAudioPath = '';
    appState.sourceFaceCropData = { x: 0, y: 0, width: 0, height: 0 };

    // Clear UI elements
    clearFramePreview();
    // Assuming you have a face-preview img tag in deepfake.php for selected face preview
    const facePreviewImg = document.getElementById('face-preview');
    if (facePreviewImg) {
        facePreviewImg.src = '';
    }
    document.getElementById('current-frame-num').textContent = '0';
    document.getElementById('total-frames-num').textContent = '0';
    document.getElementById('frame-progress').style.width = '0%';
    document.getElementById('frame-progress').setAttribute('aria-valuenow', 0);
    document.getElementById('target-x').value = '';
    document.getElementById('target-y').value = '';
    document.getElementById('target-width').value = '';
    document.getElementById('target-height').value = '';
    
    // Reset file inputs (these now have control-element class, so disableControls might handle them)
    // explicitly clearing the value ensures file selection is reset
    const videoFileInput = document.getElementById('video-file-input');
    if (videoFileInput) videoFileInput.value = ''; 
    const faceImageInput = document.getElementById('face-image-input');
    if (faceImageInput) faceImageInput.value = ''; 

    showUIMessage("System reset. Ready for new input.", 'info');
    disableControls(false); // Ensure controls are enabled after reset
    
    // Destroy cropper instances if they exist
    if (appState.originalFrameCropper) {
        appState.originalFrameCropper.destroy();
        appState.originalFrameCropper = null;
    }
    if (appState.sourceFaceCropper) {
        appState.sourceFaceCropper.destroy();
        appState.sourceFaceCropper = null;
    }
}

// Initial UI setup on page load - this ensures elements are ready for other scripts to attach listeners
document.addEventListener('DOMContentLoaded', () => {
    // Call disableControls initially here to affect all elements with 'control-element'
    disableControls(false); 
    showUIMessage("Welcome! Please upload a video and a face image.", 'info');
    updateFrameUI(); // Initialize frame UI
    // Ensure cropper inputs are readonly
    document.getElementById('target-x').readOnly = true;
    document.getElementById('target-y').readOnly = true;
    document.getElementById('target-width').readOnly = true;
    document.getElementById('target-height').readOnly = true;
});