// js/deepfake3.js (Part 3: Event Listeners and UI Interactions)

// Ensure appState, showUIMessage, disableControls, updateFrameUI, clearFramePreview, 
// handleVideoUpload, handleFaceUpload, loadFrameAndBlend, applyManualCoords, generateFinalVideo, resetAll
// are defined globally by deepfake.js and deepfake2.js before this script runs.

// ALL event listener setup code MUST be inside a DOMContentLoaded listener
// to ensure the HTML elements exist when the script tries to attach to them.
document.addEventListener('DOMContentLoaded', () => {

    // Handle video file input change
    document.getElementById('video-file-input').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            handleVideoUpload(file);
        }
    });

    // Handle video drop zone
    const videoDropZone = document.getElementById('video-drop');
    videoDropZone.addEventListener('click', () => {
        document.getElementById('video-file-input').click();
    });
    videoDropZone.addEventListener('dragover', (event) => {
        event.preventDefault();
        videoDropZone.classList.add('drag-over');
    });
    videoDropZone.addEventListener('dragleave', (event) => {
        videoDropZone.classList.remove('drag-over');
    });
    videoDropZone.addEventListener('drop', (event) => {
        event.preventDefault();
        videoDropZone.classList.remove('drag-over');
        const file = event.dataTransfer.files[0];
        if (file && file.type.startsWith('video/')) {
            handleVideoUpload(file);
        } else {
            showUIMessage('Please drop a video file.', 'error');
        }
    });


    // Handle face image file input change (currently unused, face selection from gallery instead)
    // This input is now hidden, so this listener might not be directly triggered by user action,
    // but the functionality to handle the file is here if needed programmatically.
    document.getElementById('face-image-input').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            handleFaceUpload(file);
        }
    });


    // Handle next frame button click
    document.getElementById('next-frame-btn').addEventListener('click', () => {
        if (appState.currentFrame < appState.totalFrames) {
            appState.currentFrame++;
            appState.currentGeneration = 1; // Reset generation when changing frame
            updateFrameUI();
            loadFrameAndBlend();
        }
    });

    // Handle previous frame button click
    document.getElementById('prev-frame-btn').addEventListener('click', () => {
        if (appState.currentFrame > 1) {
            appState.currentFrame--;
            appState.currentGeneration = 1; // Reset generation when changing frame
            updateFrameUI();
            loadFrameAndBlend();
        }
    });

    // Handle 'Apply Coords & Reblend' button click
    document.getElementById('apply-coords-btn').addEventListener('click', applyManualCoords);

    // Handle 'Edit Area' button click (toggles Cropper.js enable/disable)
    document.getElementById('edit-area-btn').addEventListener('click', () => {
        if (appState.originalFrameCropper) {
            if (appState.originalFrameCropper.cropped) {
                appState.originalFrameCropper.clear(); // Clear current crop if one exists
            }
            appState.originalFrameCropper.enable(); // Enable cropping mode
            showUIMessage("Cropping enabled. Adjust the area and click 'Apply Coords & Reblend'.", 'info', 5000);
        } else {
            showUIMessage("No frame loaded to edit.", 'error');
        }
    });

    // Handle source face edit button
    document.getElementById('edit-source-face-btn').addEventListener('click', () => {
        if (appState.sourceFaceCropper && appState.currentFaceImage) {
            // Check if face-preview element exists before trying to access its src
            const facePreviewElement = document.getElementById('face-preview');
            if (!facePreviewElement || !facePreviewElement.src) {
                showUIMessage("No source face image loaded in preview to edit. Please upload or select one first.", 'error');
                return;
            }
            
            document.getElementById('source-face-image-for-crop').src = facePreviewElement.src;
            document.getElementById('source-face-cropper-modal').style.display = 'flex'; // Show modal
            // Re-initialize or enable cropper on modal image
            document.getElementById('source-face-image-for-crop').onload = () => {
                if (appState.sourceFaceCropper) {
                    appState.sourceFaceCropper.destroy(); // Destroy previous instance if any
                }
                appState.sourceFaceCropper = new Cropper(document.getElementById('source-face-image-for-crop'), {
                    aspectRatio: 1, // Keep 1:1 for faces
                    viewMode: 1,
                    autoCropArea: 0.8,
                    data: appState.sourceFaceCropData, // Set previous crop data
                    ready() {
                        appState.sourceFaceCropper.enable(); // Enable cropping mode
                    },
                    cropmove() {
                        const cropperData = appState.sourceFaceCropper.getData(true);
                        document.getElementById('source-crop-x').value = Math.round(cropperData.x);
                        document.getElementById('source-crop-y').value = Math.round(cropperData.y);
                        document.getElementById('source-crop-width').value = Math.round(cropperData.width);
                        document.getElementById('source-crop-height').value = Math.round(cropperData.height);
                    }
                });
                showUIMessage("Cropping source face enabled. Adjust and click 'Save Crop'.", 'info', 5000);
            };
            // If image is already loaded (from cache), onload might not fire, call it manually
            if (document.getElementById('source-face-image-for-crop').complete) {
                document.getElementById('source-face-image-for-crop').onload();
            }
        } else {
            showUIMessage("No source face image loaded to edit. Please upload or select one first.", 'error');
        }
    });

    // Handle 'Save Crop' button inside the source face cropper modal
    document.getElementById('apply-source-crop-btn').addEventListener('click', () => {
        if (appState.sourceFaceCropper) {
            appState.sourceFaceCropData = appState.sourceFaceCropper.getData(true); // Get rounded data
            appState.sourceFaceCropper.disable(); // Disable cropper after setting data
            showUIMessage("Source face crop applied. Re-blending current frame with new source crop.", 'success');
            closeSourceFaceCropperModal(); // Close the modal
            applyManualCoords(); // Re-blend current frame with new source crop
        } else {
            showUIMessage("No source face cropper to apply.", 'error');
        }
    });

    // Close modal function (called by X button)
    // Note: This function is globally accessible because it's not wrapped in a DOMContentLoaded specific to deepfake3.js itself.
    // If it were, it couldn't be called from inline HTML like onclick="closeSourceFaceCropperModal()".
    // For now, let's keep it defined globally.
    window.closeSourceFaceCropperModal = function() {
        document.getElementById('source-face-cropper-modal').style.display = 'none';
        if (appState.sourceFaceCropper) {
            appState.sourceFaceCropper.destroy(); // Destroy cropper instance when closing modal
            appState.sourceFaceCropper = null; // Clear reference
        }
    }


    // Handle 'Next Generation' button click (if multiple blended images are supported)
    // Note: The 'next-generation-btn' is not in the provided HTML. I'm keeping the logic here in case you add it.
    // If you add this button, add class="control-element" to it.
    const nextGenerationBtn = document.getElementById('next-generation-btn');
    if (nextGenerationBtn) { // Check if the element exists
        nextGenerationBtn.addEventListener('click', () => {
            appState.currentGeneration++; // Increment generation
            loadFrameAndBlend(); // Reload and blend for the next generation
        });
    }


    // Handle 'Generate Video' button click
    document.getElementById('generate-video-btn').addEventListener('click', () => {
        generateFinalVideo();
    });

    // Handle 'Reset All' button click
    document.getElementById('reset-btn').addEventListener('click', () => {
        resetAll();
    });

    // --- Existing Face Selection from Gallery ---
    async function loadExistingFaces() {
        try {
            // Corrected: Using 'load_faces.php' and accessing 'filename' property
            const response = await fetch('/gero_gpt/load_faces.php'); 
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const faces = await response.json(); // Expecting an array of objects, e.g., [{id: X, filename: "Y.jpg"}]
            const faceSelectionDiv = document.getElementById('face-selection');
            faceSelectionDiv.innerHTML = ''; // Clear "Loading..."

            if (faces.length > 0) {
                faces.forEach(face => {
                    // Ensure 'face' object has a 'filename' property
                    const faceFilename = face.filename; 
                    if (!faceFilename) {
                        console.warn('Face object missing filename property:', face);
                        return; // Skip this face if filename is not found
                    }
                    
                    const img = document.createElement('img');
                    img.src = `/gero_gpt/deepfake_input/faces/${encodeURIComponent(faceFilename)}`;
                    img.alt = faceFilename;
                    img.className = 'face-img';
                    img.dataset.filename = faceFilename; // Store filename for easy access

                    img.addEventListener('click', () => {
                        // Remove 'selected' class from all images
                        document.querySelectorAll('.face-img').forEach(i => i.classList.remove('selected'));
                        // Add 'selected' class to the clicked image
                        img.classList.add('selected');
                        // Set the currentFaceImage in appState
                        appState.currentFaceImage = faceFilename; // Use the extracted filename
                        
                        // Ensure the face-preview img tag exists in your deepfake.php (e.g. <img id="face-preview" src="" style="display:none;">)
                        const facePreviewImg = document.getElementById('face-preview');
                        if (facePreviewImg) {
                             facePreviewImg.src = img.src; // Also set the main preview
                             facePreviewImg.onload = () => {
                                // Initialize Cropper.js on the main face preview after selection
                                if (appState.sourceFaceCropper) {
                                    appState.sourceFaceCropper.destroy();
                                }
                                appState.sourceFaceCropper = new Cropper(facePreviewImg, {
                                    aspectRatio: 1, // Default to 1:1 for faces
                                    viewMode: 1,
                                    autoCropArea: 0.8,
                                    ready() {
                                        appState.sourceFaceCropper.disable(); // Disable by default, user enables with button
                                        appState.sourceFaceCropData = appState.sourceFaceCropper.getData(true); // Set default crop
                                    }
                                });
                            };
                            if (facePreviewImg.complete) facePreviewImg.onload(); // If image is already loaded
                        }

                        showUIMessage(`Selected face: ${faceFilename}`, 'info');
                        
                        // If a video is already loaded, proceed to blend the current frame with the new face
                        if (appState.currentVideo && appState.totalFrames > 0) {
                            loadFrameAndBlend();
                        }
                    });
                    faceSelectionDiv.appendChild(img);
                });
            } else {
                faceSelectionDiv.textContent = 'No faces uploaded yet. Use train.php to upload.';
            }
        } catch (error) {
            console.error('Error loading existing faces:', error);
            document.getElementById('face-selection').textContent = 'Failed to load faces.';
            showUIMessage('Failed to load existing faces.', 'error');
        }
    }

    // Load faces when the page loads (after DOM is ready)
    loadExistingFaces();

}); // End of DOMContentLoaded for deepfake3.js