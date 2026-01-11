let frameNumber = 1;
let generation = 1;
let currentFaceIndex = 0;
let totalFaces = 0;
let processing = false;

async function loadNextBlend() {
  if (processing) return;
  processing = true;

  const padded = String(frameNumber).padStart(3, '0');
  const framePath = `deepfake_input/frames/video_1/frame_${padded}.jpg`;

  try {
    const res = await fetch(`blend_frame.php?frame=${encodeURIComponent(framePath)}&number=${frameNumber}&generation=${generation}&face_index=${currentFaceIndex}`);
    const data = await res.json();
    
    if (data.done) {
      document.getElementById('status').textContent = `✅ Done with frame ${frameNumber}`;
    } else {
      document.getElementById('preview').src = data.blended;
      document.getElementById('current-info').textContent = `Frame ${frameNumber}, Face ${currentFaceIndex + 1} of ${data.total_faces}`;
      currentFaceIndex = data.face_index;
      totalFaces = data.total_faces;
    }
  } catch (err) {
    console.error("Blend load failed:", err);
  } finally {
    processing = false;
  }
}

async function submitFeedback(action) {
  const feedback = document.getElementById('feedback').value;

  await fetch('save_feedback.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
      frame_number: frameNumber,
      face_image: document.getElementById('preview').src,
      adjustment_type: feedback,
      generation,
      action
    })
  });

  currentFaceIndex++;
  if (currentFaceIndex >= totalFaces) {
    currentFaceIndex = 0;
    generation++;
    frameNumber++;
  }

  await loadNextBlend();
}

document.addEventListener('DOMContentLoaded', async () => {
  // Button listeners
  document.getElementById('next-frame').onclick = () => submitFeedback('next');
  document.getElementById('dont-include').onclick = () => submitFeedback('exclude');

  // Video upload handler
  const videoDrop = document.getElementById('video-drop');
  videoDrop.addEventListener('dragover', e => e.preventDefault());
  videoDrop.addEventListener('drop', e => {
    e.preventDefault();
    const file = e.dataTransfer.files[0];
    const formData = new FormData();
    formData.append('video', file);

    fetch('deepfake_upload.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        alert(`✅ Video uploaded: ${data.filename}`);
      } else {
        alert(`❌ Upload failed: ${data.message}`);
      }
    });
  });

  // Load face preview
  try {
    const res = await fetch('load_faces.php');
    const faces = await res.json();
    const container = document.getElementById('face-preview');
    faces.forEach(face => {
      const img = document.createElement('img');
      img.src = `media/${face.filename}`;
      img.className = 'preview-face';
      img.title = face.label || face.filename;
      container.appendChild(img);
    });
  } catch (err) {
    console.error("Face loading failed:", err);
  }

  await loadNextBlend();
});
