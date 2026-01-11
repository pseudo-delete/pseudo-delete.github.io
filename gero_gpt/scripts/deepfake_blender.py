# File: scripts/deepfake_blender.py
import sys
import os
import cv2

def blend_faces(original_frame_path, face_image_path, output_image_path,
                target_x, target_y, target_w, target_h,
                source_crop_x, source_crop_y, source_crop_w, source_crop_h):
    """
    Performs basic face blending by cropping the source face and overlaying it
    onto the target area of the original frame.

    Args:
        original_frame_path (str): Path to the original video frame.
        face_image_path (str): Path to the source face image.
        output_image_path (str): Path to save the blended output image.
        target_x, target_y, target_w, target_h (int): Coordinates on the original frame
                                                     where the face will be placed.
        source_crop_x, source_crop_y, source_crop_w, source_crop_h (int):
                                                     Coordinates to crop the source face image.
                                                     If width or height is 0, no specific crop is applied.
    """
    try:
        original_frame = cv2.imread(original_frame_path)
        if original_frame is None:
            print(f"Error: Could not load original frame at {original_frame_path}", file=sys.stderr)
            sys.exit(1)

        source_face = cv2.imread(face_image_path)
        if source_face is None:
            print(f"Error: Could not load source face at {face_image_path}", file=sys.stderr)
            sys.exit(1)

        # Apply source face cropping if coordinates are provided (width/height > 0)
        if source_crop_w > 0 and source_crop_h > 0:
            # Ensure crop coordinates are within bounds
            h_src, w_src, _ = source_face.shape
            source_crop_x = max(0, min(source_crop_x, w_src - 1))
            source_crop_y = max(0, min(source_crop_y, h_src - 1))
            source_crop_w = max(1, min(source_crop_w, w_src - source_crop_x))
            source_crop_h = max(1, min(source_crop_h, h_src - source_crop_y))
            
            source_face_cropped = source_face[source_crop_y : source_crop_y + source_crop_h,
                                              source_crop_x : source_crop_x + source_crop_w]
            print(f"Source face cropped: x={source_crop_x}, y={source_crop_y}, w={source_crop_w}, h={source_crop_h}", file=sys.stderr)
        else:
            source_face_cropped = source_face
            print("No source face crop applied.", file=sys.stderr)

        # Resize the (potentially cropped) source face to the target dimensions
        if target_w <= 0 or target_h <= 0:
            print(f"Error: Invalid target width ({target_w}) or height ({target_h}).", file=sys.stderr)
            sys.exit(1)
            
        resized_face = cv2.resize(source_face_cropped, (target_w, target_h), interpolation=cv2.INTER_AREA)

        # Ensure target coordinates are within the original frame boundaries
        h_orig, w_orig, _ = original_frame.shape
        x_end = min(target_x + target_w, w_orig)
        y_end = min(target_y + target_h, h_orig)
        
        target_x = max(0, target_x)
        target_y = max(0, target_y)

        # Calculate actual dimensions after clipping to frame boundaries
        actual_w = x_end - target_x
        actual_h = y_end - target_y

        if actual_w <= 0 or actual_h <= 0:
            print(f"Warning: Target area outside frame or too small. No blend performed for frame: {original_frame_path}", file=sys.stderr)
            cv2.imwrite(output_image_path, original_frame) # Save original if no valid target area
            return

        # Resize the `resized_face` again if it was clipped
        if actual_w != target_w or actual_h != target_h:
            resized_face = cv2.resize(resized_face, (actual_w, actual_h), interpolation=cv2.INTER_AREA)
            print(f"Resized face clipped to actual_w={actual_w}, actual_h={actual_h}", file=sys.stderr)
        
        # Simple overlay (replace the region)
        # For more realistic blending, you would use techniques like:
        # - Alpha blending
        # - Seamless cloning (cv2.seamlessClone)
        # - Dlib/OpenCV face landmark detection and affine transformations
        # - Deep learning models (e.g., FaceSwap, DeepFaceLive)
        
        original_frame[target_y:y_end, target_x:x_end] = resized_face

        cv2.imwrite(output_image_path, original_frame)
        print(f"Blended frame saved to {output_image_path}", file=sys.stderr)

    except Exception as e:
        print(f"An error occurred during blending: {e}", file=sys.stderr)
        sys.exit(1)

if __name__ == "__main__":
    if len(sys.argv) != 12: # Changed from 11 to 12 as we now have 8 args for coordinates
        print("Usage: python deepfake_blender.py <original_frame_path> <face_image_path> <output_image_path> <target_x> <target_y> <target_w> <target_h> <source_crop_x> <source_crop_y> <source_crop_w> <source_crop_h>", file=sys.stderr)
        sys.exit(1)

    original_frame_path = sys.argv[1]
    face_image_path = sys.argv[2]
    output_image_path = sys.argv[3]
    target_x = int(sys.argv[4])
    target_y = int(sys.argv[5])
    target_w = int(sys.argv[6])
    target_h = int(sys.argv[7])
    source_crop_x = int(sys.argv[8])
    source_crop_y = int(sys.argv[9])
    source_crop_w = int(sys.argv[10])
    source_crop_h = int(sys.argv[11])

    blend_faces(original_frame_path, face_image_path, output_image_path,
                target_x, target_y, target_w, target_h,
                source_crop_x, source_crop_y, source_crop_w, source_crop_h)