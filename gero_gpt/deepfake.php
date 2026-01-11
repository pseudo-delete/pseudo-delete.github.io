<?php
// File: deepfake.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Deepfake Processor - Gero GPT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />
    <link rel="stylesheet" href="style.css">
    <style>
        /* Your existing CSS styles */
        body { font-family: sans-serif; margin: 20px; }
        .drop-zone {
            border: 2px dashed #aaa;
            padding: 20px;
            text-align: center;
            background-color: #f9f9f9;
            cursor: pointer;
            margin-bottom: 10px;
        }
        .face-img {
            width: 100px;
            margin: 5px;
            border: 2px solid transparent;
            cursor: pointer;
        }
        .face-img:hover {
            border: 2px solid #333;
        }
        .row { display: flex; gap: 15px; }
        .col { flex: 1; }
        img.preview { max-width: 100%; border: 1px solid #ccc; }
        video { width: 100%; }
        button { margin-right: 6px; padding: 8px 12px; cursor: pointer; }

        .face-img.selected {
            border: 2px solid blue;
            box-shadow: 0 0 5px blue;
        }
        .coord-input-section {
            margin-top: 10px;
            border: 1px solid #eee;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 5px;
        }
        .coord-input-section label {
            display: inline-block;
            width: 45px;
            text-align: right;
            margin-right: 5px;
        }
        .coord-input-section input[type="number"] {
            width: 60px;
            margin-right: 10px;
            background-color: #e9e9e9;
        }
        #ui-message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            display: none;
        }
        #ui-message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        #ui-message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        #ui-message.info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        #source-face-cropper-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.8);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: #fefefe;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 700px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            border-radius: 8px;
            position: relative;
        }
        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 20px;
            cursor: pointer;
        }
        .close-button:hover,
        .close-button:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        #source-face-image-for-crop {
            display: block;
            max-width: 100%;
        }
        .source-crop-coords label {
            display: inline-block;
            width: 45px;
            text-align: right;
            margin-right: 5px;
        }
        .source-crop-coords input[type="number"] {
            width: 60px;
            margin-right: 10px;
            background-color: #e9e9e9;
        }
    </style>
</head>
<body>

<h1>🎭 Deepfake Frame Blending</h1>

<nav>
    <a href="index.html">🧠 Chat & Generate</a>
    <a href="deepfake.php">🧠 Deepfake videos</a>
    <a href="train.php">📂 Train / Manage Media</a>
    <a href="generated_output.php">🎨 Generated Output</a>
    <a href="help.html">❓ Help</a>
</nav>

<div class="row">
    <div class="col">
        <h3>Face Images (from train.php) <button id="edit-source-face-btn" class="control-element" title="Edit selected face's crop area">✏️ Edit Crop</button></h3>
        <div id="face-selection" style="overflow-y: auto; height: 300px; border: 1px solid #ccc; padding: 5px;" class="drop-zone">
            Loading...
        </div>
        <input type="file" id="face-image-input" accept="image/*" style="display: none;" class="control-element">
    </div>

    <div class="col">
        <h3>Original Frame <button id="edit-area-btn" class="control-element" title="Edit target face area on original frame">🎯 Edit Area</button></h3>
        <img id="original-frame" class="preview" src="" alt="Original Frame">

        <div class="coord-input-section">
            <h4>Target Face Position (on Original Frame)</h4>
            <label for="target-x">X:</label> <input type="number" id="target-x" value="0" readonly>
            <label for="target-y">Y:</label> <input type="number" id="target-y" value="0" readonly>
            <label for="target-width">Width:</label> <input type="number" id="target-width" value="0" readonly>
            <label for="target-height">Height:</label> <input type="number" id="target-height" value="0" readonly>
            <button id="apply-coords-btn" class="control-element">Apply Coords & Reblend</button>
            <div id="coord-feedback" style="font-size: 0.8em; color: grey; margin-top: 5px;"></div>
        </div>
        <h3>Blended Output</h3>
        <img id="blended-output" class="preview" src="" alt="Blended Frame">
        <div id="feedback-label" style="margin: 5px 0;"></div>

        <h3>Adjusted Face Preview (Source Face within Target Area)</h3>
        <img id="output-preview" class="preview" src="" alt="Adjusted Face Preview">

        <label>Feedback:</label>
        <select id="feedback-type" class="control-element">
            <option value="">-- Select --</option>
            <option value="perfect">Perfect</option>
            <option value="face_too_big">Face too big</option>
            <option value="face_too_small">Face too small</option>
            <option value="rotate_left">Rotate left</option>
            <option value="rotate_right">Rotate right</option>
            <option value="adjust_lighting">Adjust lighting</option>
        </select>

        <div style="margin-top: 10px;">
            <button id="perfect-btn" class="control-element">✔️ Perfect</button>
            <button id="submit-feedback-btn" class="control-element">Submit Feedback</button>
            <button id="regenerate-btn" class="control-element">🔁 Reblend with Adjustments</button>
            <button id="skip-btn" class="control-element">🚫 Don’t Include (Skip)</button>
            <button id="prev-frame-btn" class="control-element">⬅️ Previous Frame</button>
            <button id="next-frame-btn" class="control-element">➡️ Next Frame</button>
        </div>
        <div id="message-area" class="info"></div>
    </div>

    <div class="col">
        <h3>Upload Video</h3>
        <div id="video-drop" class="drop-zone">
            Drop video here or click to select...
            <input type="file" id="video-file-input" accept="video/*" style="display: none;" class="control-element">
        </div>
        <video id="video-preview" controls></video>
        <div style="margin-top: 15px;">
            <button id="generate-video-btn" class="control-element">🎬 Export Final Video</button>
            <button id="reset-btn" class="control-element">🔄 Reset All</button>
        </div>

        <div id="frame-info" style="margin: 10px 0;">
            Frame: <span id="current-frame-num">0</span> / <span id="total-frames-num">0</span>
        </div>
        <progress id="frame-progress" value="0" max="100" style="width: 100%;"></progress>
    </div>
</div>

<div id="source-face-cropper-modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeSourceFaceCropperModal()">&times;</span>
        <h2>Crop Source Face Image</h2>
        <div style="max-width: 500px; margin: auto;">
            <img id="source-face-image-for-crop" src="" alt="Source Face for Cropping">
        </div>
        <div class="source-crop-coords" style="margin-top: 15px; text-align: center;">
            <label for="source-crop-x">X:</label> <input type="number" id="source-crop-x" value="0" readonly>
            <label for="source-crop-y">Y:</label> <input type="number" id="source-crop-y" value="0" readonly>
            <label for="source-crop-width">W:</label> <input type="number" id="source-crop-width" value="0" readonly>
            <label for="source-crop-height">H:</label> <input type="number" id="source-crop-height" value="0" readonly>
        </div>
        <div style="margin-top: 20px; text-align: center;">
            <button id="apply-source-crop-btn" class="control-element">Apply Source Crop</button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<script src="js/deepfake.js"></script>
<script src="js/deepfake2.js"></script>
<script src="js/deepfake3.js"></script>
</body>
</html>