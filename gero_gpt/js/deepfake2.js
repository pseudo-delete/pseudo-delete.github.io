// js/deepfake2.js (Part 2: Main Processing Functions)

// Ensure appState is defined globally by deepfake.js before this script runs
// (This is why the script load order in deepfake.php is important)

// Function to handle video file selection and upload
function handleVideoUpload(file) {
    disableControls(true);
    showUIMessage("Uploading video...", 'info', 0);
    appState.currentVideo = ''; // Clear previous video data
    appState.currentVideoDbId = null;
    appState.totalFrames = 0;
    appState.currentFrame = 1;
    appState.currentGeneration = 1;
    appState.currentAudioPath = '';
    clearFramePreview(); // Clear any existing frame previews

    const formData = new FormData();
    formData.append('video', file); 

    fetch('/gero_gpt/upload_video.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            console.error('Network response was not ok', response.status, response.statusText);
            return response.text().then(text => {
                throw new Error(`Video upload server error. Status: ${response.status}. Response: ${text.substring(0, 200)}...`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Response from upload_video.php:', data);
        
        console.log('DEBUG: Checking conditions for success block.');
        console.log('DEBUG: data.status === "success" is', data.status === 'success');
        console.log('DEBUG: data.video_name is truthy (exists and not empty) is', !!data.video_name);
        console.log('DEBUG: data.video_db_id is truthy (exists and not empty) is', !!data.video_db_id);
        console.log('DEBUG: Type of data.video_name:', typeof data.video_name);
        console.log('DEBUG: Type of data.video_db_id:', typeof data.video_db_id);

        if (data.status === 'success' && data.video_name && data.video_db_id) {
            console.log('DEBUG: All conditions met. Entering success block.');
            appState.currentVideo = data.video_name;
            appState.currentVideoDbId = data.video_db_id;
            console.log(`Video uploaded: ${appState.currentVideo} DB ID: ${appState.currentVideoDbId}`);
            showUIMessage(`Video "${appState.currentVideo}" uploaded successfully. Extracting frames...`, 'info', 0);

            const videoPreview = document.getElementById('video-preview');
            videoPreview.src = `/gero_gpt/deepfake_input/videos/${encodeURIComponent(appState.currentVideo)}`;
            videoPreview.load();

            console.log('About to call extractFrames...');
            extractFrames(appState.currentVideo, appState.currentVideoDbId);
        } else {
            console.log('DEBUG: Conditions NOT met. Entering failure block.');
            showUIMessage(data.message || 'Video upload failed.', 'error');
            disableControls(false);
        }
    })
    .catch(error => {
        console.error('Error during video upload fetch:', error);
        showUIMessage(`An unexpected error occurred during video upload: ${error.message}`, 'error');
        disableControls(false);
    });
}

// Function to handle face image file selection and upload
function handleFaceUpload(file) {
    disableControls(true);
    showUIMessage("Uploading face image...", 'info', 0);
    appState.currentFaceImage = ''; // Clear previous face data

    const formData = new FormData();
    formData.append('face_image', file);

    fetch('/gero_gpt/upload_face.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            console.error('Network response was not ok from upload_face.php', response.status, response.statusText);
            return response.text().then(text => {
                throw new Error(`Face upload server error. Status: ${response.status}. Response: ${text.substring(0, 200)}...`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success' && data.face_image_name) {
            appState.currentFaceImage = data.face_image_name;
            // Ensure the face-preview img tag exists in your deepfake.php
            const facePreviewImg = document.getElementById('face-preview');
            if (facePreviewImg) {
                facePreviewImg.src = `/gero_gpt/deepfake_input/faces/${encodeURIComponent(appState.currentFaceImage)}`;
                facePreviewImg.onload = () => {
                    // Initialize Cropper.js on the source face image
                    if (appState.sourceFaceCropper) {
                        appState.sourceFaceCropper.destroy();
                    }
                    appState.sourceFaceCropper = new Cropper(facePreviewImg, {
                        aspectRatio: 1, // Default to 1:1 for faces
                        viewMode: 1,
                        autoCropArea: 0.8,
                        ready() {
                            appState.sourceFaceCropper.disable(); // Disable by default, user enables with button
                            // Initialize appState.sourceFaceCropData with default crop values
                            appState.sourceFaceCropData = appState.sourceFaceCropper.getData(true);
                        }
                    });
                };
            }
            
            showUIMessage(`Face "${appState.currentFaceImage}" uploaded successfully.`, 'success');
            // If a video is already loaded, proceed to blend the current frame with the new face
            if (appState.currentVideo && appState.totalFrames > 0) {
                loadFrameAndBlend();
            } else {
                showUIMessage("Upload a video now to start blending.", 'info');
            }
        } else {
            showUIMessage(data.message || 'Face image upload failed.', 'error');
        }
        disableControls(false);
    })
    .catch(error => {
        console.error('Error during face upload fetch:', error);
        showUIMessage(`An unexpected error occurred during face upload: ${error.message}`, 'error');
        disableControls(false);
    });
}

// Function to extract frames (called after successful video upload)
function extractFrames(videoName, videoDbId) {
    console.log('extractFrames called with videoName:', videoName, 'videoDbId:', videoDbId);
    disableControls(true);
    showUIMessage("Starting frame and audio extraction...", 'info', 0);

    if (!videoName || videoDbId === null) {
        console.error('Validation failed in extractFrames: videoName or videoDbId missing.');
        showUIMessage("Error: No video selected or database ID missing for frame extraction.", 'error');
        disableControls(false);
        return;
    }

    appState.currentVideo = videoName; // Ensure global is set
    appState.currentVideoDbId = videoDbId; // Ensure global is set

    const formDataForExtractor = new FormData();
    formDataForExtractor.append('video_name', videoName);
    formDataForExtractor.append('video_db_id', videoDbId);

    console.log('Making fetch call to frame_extractor.php with data:', {videoName, videoDbId});
    fetch('/gero_gpt/frame_extractor.php', {
        method: 'POST',
        body: formDataForExtractor
    })
    .then(res => {
        if (!res.ok) {
            console.error('Network response was not ok from frame_extractor.php', res.status, res.statusText);
            return res.text().then(text => {
                throw new Error(`Frame extraction server error. Status: ${res.status}. Response: ${text.substring(0, 200)}...`);
            });
        }
        return res.json();
    })
    .then(data => {
        if (data.status === 'success') {
            appState.currentFrame = 1; // Reset to first frame
            appState.totalFrames = data.frame_count;
            clearFramePreview(); // Ensure previews are clear before loading new ones

            updateFrameUI(); // Update frame numbers and progress bar

            if (data.audio_extracted && data.audio_path) {
                appState.currentAudioPath = data.audio_path;
                console.log('Audio extracted successfully:', appState.currentAudioPath);
                showUIMessage("Frames and audio extracted successfully. Ready for blending.", 'success');
            } else {
                appState.currentAudioPath = '';
                console.warn('Audio extraction did not yield a valid audio file for this video. Audio will not be included in final export.');
                showUIMessage("Frames extracted, but audio extraction failed or no audio found. Proceeding without audio.", 'info');
            }

            if (appState.currentFaceImage) {
                loadFrameAndBlend(); // Load first frame and blend
            } else {
                showUIMessage("Please select a face image to begin blending.", 'info', 0);
                disableControls(false);
            }

        } else {
            showUIMessage(`Frame extraction failed: ${data.message || 'Unknown error.'}`, 'error');
            disableControls(false);
        }
    })
    .catch(error => {
        console.error('Error during frame extraction fetch:', error);
        showUIMessage(`An unexpected error occurred during frame extraction: ${error.message}`, 'error');
        disableControls(false);
    });
}


// Function to load a specific frame and initiate blending
function loadFrameAndBlend() {
    disableControls(true);
    showUIMessage("Loading frame and blending...", 'info', 0);
    document.getElementById('coord-feedback').textContent = "";

    if (!appState.currentVideo || appState.currentVideoDbId === null) {
        showUIMessage("Error: No video selected or database ID missing. Please upload a video first.", 'error');
        disableControls(false);
        return;
    }

    if (!appState.currentFaceImage) {
        showUIMessage("Please select a face image before blending.", 'info');
        disableControls(false);
        return;
    }

    // Fetch the original frame path
    fetch(`/gero_gpt/get_frame_path.php?video=${encodeURIComponent(appState.currentVideo)}&frame=${appState.currentFrame}`)
        .then(r => {
            if (!r.ok) {
                return r.text().then(text => { throw new Error(`HTTP error from get_frame_path.php! Status: ${r.status}, Response: ${text.substring(0, 200)}...`); });
            }
            return r.json();
        })
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('original-frame').src = data.frame_path;
                const originalFrameImg = document.getElementById('original-frame');

                originalFrameImg.onload = () => {
                    // Initialize Cropper.js on the original frame
                    if (appState.originalFrameCropper) {
                        appState.originalFrameCropper.destroy();
                    }
                    appState.originalFrameCropper = new Cropper(originalFrameImg, {
                        aspectRatio: 1, // Start with 1:1 aspect ratio, can be changed by user
                        viewMode: 1, // Restrict the crop box to not exceed the canvas
                        autoCropArea: 0.8, // 80% of the image
                        movable: true,
                        zoomable: true,
                        ready() {
                            // Set initial coordinates for the crop box (e.g., center 80% square)
                            const initialCropWidth = Math.min(originalFrameImg.naturalWidth, originalFrameImg.naturalHeight) * 0.8;
                            const initialCropHeight = initialCropWidth;
                            appState.originalFrameCropper.setData({
                                x: (originalFrameImg.naturalWidth - initialCropWidth) / 2,
                                y: (originalFrameImg.naturalHeight - initialCropHeight) / 2,
                                width: initialCropWidth,
                                height: initialCropHeight
                            });
                            const cropperData = appState.originalFrameCropper.getData(true); // Get current crop box data
                            document.getElementById('target-x').value = Math.round(cropperData.x);
                            document.getElementById('target-y').value = Math.round(cropperData.y);
                            document.getElementById('target-width').value = Math.round(cropperData.width);
                            document.getElementById('target-height').value = Math.round(cropperData.height);

                            // Trigger initial blend immediately after cropper is ready
                            applyManualCoords(); // Proceed to blend with initial crop
                        },
                        cropmove() { // Update inputs as user moves/resizes crop box
                            const cropperData = appState.originalFrameCropper.getData(true);
                            document.getElementById('target-x').value = Math.round(cropperData.x);
                            document.getElementById('target-y').value = Math.round(cropperData.y);
                            document.getElementById('target-width').value = Math.round(cropperData.width);
                            document.getElementById('target-height').value = Math.round(cropperData.height);
                        },
                        cropend() {
                            // The user will click 'Apply Coords & Reblend' after adjustments
                        }
                    });
                    appState.originalFrameCropper.disable(); // Disable cropper by default, user enables it with 'Edit Area' button
                };
                if (originalFrameImg.complete) originalFrameImg.onload();

            } else {
                throw new Error(data.message || "Failed to get frame path from server.");
            }
        })
        .catch(err => {
            console.error("Load frame error:", err);
            showUIMessage(`Error loading frame: ${err.message || 'An unknown error occurred.'}`, 'error');
            disableControls(false);
        });
}

// Function to apply manual coordinates and trigger blending
function applyManualCoords() {
    disableControls(true);
    showUIMessage("Applying adjustments and blending...", 'info', 0);

    if (!appState.currentVideo || !appState.currentFaceImage) {
        showUIMessage("Error: Video or face not selected.", 'error');
        disableControls(false);
        return;
    }

    // Get current crop data from the original frame cropper
    let target_x, target_y, target_w, target_h;
    if (appState.originalFrameCropper) {
        const cropperData = appState.originalFrameCropper.getData(true); // Get rounded data
        target_x = Math.round(cropperData.x);
        target_y = Math.round(cropperData.y);
        target_w = Math.round(cropperData.width);
        target_h = Math.round(cropperData.height);
    } else {
        // Fallback or initial values if cropper isn't initialized yet (shouldn't happen with current flow)
        target_x = 0; target_y = 0; target_w = 0; target_h = 0;
        console.warn("Original Frame Cropper not initialized. Using default target coordinates.");
    }

    // Update the readonly inputs with the values from the cropper (already done in cropmove, but good to ensure)
    document.getElementById('target-x').value = target_x;
    document.getElementById('target-y').value = target_y;
    document.getElementById('target-width').value = target_w;
    document.getElementById('target-height').value = target_h;

    const blendUrl = new URL('/gero_gpt/blend_frame.php', window.location.origin);
    blendUrl.searchParams.append('video', appState.currentVideo);
    blendUrl.searchParams.append('frame', appState.currentFrame);
    blendUrl.searchParams.append('generation', appState.currentGeneration);
    blendUrl.searchParams.append('face', appState.currentFaceImage);
    blendUrl.searchParams.append('target_x', target_x);
    blendUrl.searchParams.append('target_y', target_y);
    blendUrl.searchParams.append('target_w', target_w);
    blendUrl.searchParams.append('target_h', target_h);

    // Add source face crop data if available (width/height > 0 indicates a crop was set)
    if (appState.sourceFaceCropData.width > 0 && appState.sourceFaceCropData.height > 0) {
        blendUrl.searchParams.append('source_face_crop_x', Math.round(appState.sourceFaceCropData.x));
        blendUrl.searchParams.append('source_face_crop_y', Math.round(appState.sourceFaceCropData.y));
        blendUrl.searchParams.append('source_face_crop_w', Math.round(appState.sourceFaceCropData.width));
        blendUrl.searchParams.append('source_face_crop_h', Math.round(appState.sourceFaceCropData.height));
    }


    fetch(blendUrl.toString())
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`HTTP error! status: ${response.status}. Response: ${text.substring(0, 200)}...`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success' && data.blended_images.length > 0) {
                document.getElementById('blended-output').src = data.blended_images[0].path + '?t=' + new Date().getTime();
                document.getElementById('feedback-label').textContent = `Blended frame ${appState.currentFrame} (Generation ${appState.currentGeneration}) loaded.`;
                document.getElementById('coord-feedback').textContent = `Target Coords: X:${target_x}, Y:${target_y}, W:${target_w}, H:${target_h}`;
                if (appState.sourceFaceCropData.width > 0) {
                    document.getElementById('coord-feedback').textContent += ` | Source Crop: X:${Math.round(appState.sourceFaceCropData.x)}, Y:${Math.round(appState.sourceFaceCropData.y)}, W:${Math.round(appState.sourceFaceCropData.width)}, H:${Math.round(appState.sourceFaceCropData.height)}`;
                }

                // Also update the Adjusted Face preview (output-preview)
                const adjustedSrc = new URL('/gero_gpt/apply_adjustments.php', window.location.origin);
                adjustedSrc.searchParams.append('video', appState.currentVideo);
                adjustedSrc.searchParams.append('face_image', appState.currentFaceImage);
                adjustedSrc.searchParams.append('frame_number', appState.currentFrame);
                adjustedSrc.searchParams.append('generation', appState.currentGeneration);
                adjustedSrc.searchParams.append('target_x', target_x);
                adjustedSrc.searchParams.append('target_y', target_y);
                adjustedSrc.searchParams.append('target_w', target_w);
                adjustedSrc.searchParams.append('target_h', target_h);
                if (appState.sourceFaceCropData.width > 0 && appState.sourceFaceCropData.height > 0) {
                    adjustedSrc.searchParams.append('source_face_crop_x', Math.round(appState.sourceFaceCropData.x));
                    adjustedSrc.searchParams.append('source_face_crop_y', Math.round(appState.sourceFaceCropData.y));
                    adjustedSrc.searchParams.append('source_face_crop_w', Math.round(appState.sourceFaceCropData.width));
                    adjustedSrc.searchParams.append('source_face_crop_h', Math.round(appState.sourceFaceCropData.height));
                }
                document.getElementById('output-preview').src = adjustedSrc.toString() + '?t=' + new Date().getTime();

                showUIMessage("Blending complete!", 'success');

            } else {
                throw new Error(data.message || 'Blending failed: No blended images returned.');
            }
            disableControls(false);
        })
        .catch(error => {
            console.error('Error during blend_frame.php fetch:', error);
            showUIMessage(`Error blending frame: ${error.message}`, 'error');
            disableControls(false);
        });
}

// Function to generate the final video
function generateFinalVideo() {
    disableControls(true);
    showUIMessage("Generating final video... This may take a while.", 'info', 0);

    if (!appState.currentVideo || appState.totalFrames === 0) {
        showUIMessage("Error: No video processed yet. Please upload and extract frames first.", 'error');
        disableControls(false);
        return;
    }

    const generateUrl = new URL('/gero_gpt/generate_video.php', window.location.origin);
    generateUrl.searchParams.append('video_db_id', appState.currentVideoDbId);
    generateUrl.searchParams.append('video_name', appState.currentVideo);
    generateUrl.searchParams.append('audio_path', appState.currentAudioPath);

    fetch(generateUrl.toString())
        .then(response => {
            if (!response.ok) {
                console.error('Network response was not ok from generate_video.php', response.status, response.statusText);
                return response.text().then(text => {
                    throw new Error(`Video generation server error. Status: ${response.status}. Response: ${text.substring(0, 200)}...`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success' && data.final_video_path) {
                showUIMessage(`Video generation complete! Download: <a href="${data.final_video_path}" target="_blank" class="alert-link">Click here to download</a>`, 'success', 0);
            } else {
                showUIMessage(`Video generation failed: ${data.message || 'Unknown error.'}`, 'error');
            }
            disableControls(false);
        })
        .catch(error => {
            console.error('Error during generate_video.php fetch:', error);
            showUIMessage(`An unexpected error occurred during video generation: ${error.message}`, 'error');
            disableControls(false);
        });
}