# File: scripts/preview_face_adjuster.py
import sys
import os
import cv2
import numpy as np

def generate_face_preview(face_image_path, output_path,
                          target_w, target_h,
                          source_crop_x, source_crop_y, source_crop_w, source_crop_h):
    """
    Generates a preview of the source face after applying the specified crop
    and resizing it to the target dimensions.
    """
    try:
        source_face = cv2.imread(face_image_path)
        if source_face is None:
            print(f"Error: Could not load source face at {face_image_path}", file=sys.stderr)
            sys.exit(1)

        # Apply source face cropping if coordinates are provided (width/height > 0)
        if source_crop_w > 0 and source_crop_h > 0:
            h_src, w_src, _ = source_face.shape
            source_crop_x = max(0, min(source_crop_x, w_src - 1))
            source_crop_y = max(0, min(source_crop_y, h_src - 1))
            source_crop_w = max(1, min(source_crop_w, w_src - source_crop_x))
            source_crop_h = max(1, min(source_crop_h, h_src - source_crop_y))

            face_to_preview = source_face[source_crop_y : source_crop_y + source_crop_h,
                                          source_crop_x : source_crop_x + source_crop_w]
            print(f"Preview: Source face cropped: x={source_crop_x}, y={source_crop_y}, w={source_crop_w}, h={source_crop_h}", file=sys.stderr)
        else:
            face_to_preview = source_face
            print("Preview: No source face crop applied.", file=sys.stderr)

        # Resize the (potentially cropped) face to the target dimensions
        if target_w <= 0 or target_h <= 0:
            # If target dimensions are invalid, create a default preview
            print(f"Warning: Invalid target width ({target_w}) or height ({target_h}) for preview. Using default size.", file=sys.stderr)
            target_w = 200 # Default preview size
            target_h = 200 # Default preview size
            
        resized_preview = cv2.resize(face_to_preview, (target_w, target_h), interpolation=cv2.INTER_AREA)

        cv2.imwrite(output_path, resized_preview)
        print(f"Preview image saved to {output_path}", file=sys.stderr)

    except Exception as e:
        print(f"An error occurred during preview generation: {e}", file=sys.stderr)
        sys.exit(1)

if __name__ == "__main__":
    if len(sys.argv) != 9: # Changed from 8 to 9 because of the new source crop parameters
        print("Usage: python preview_face_adjuster.py <face_image_path> <output_path> <target_w> <target_h> <source_crop_x> <source_crop_y> <source_crop_w> <source_crop_h>", file=sys.stderr)
        sys.exit(1)

    face_image_path = sys.argv[1]
    output_path = sys.argv[2]
    target_w = int(sys.argv[3])
    target_h = int(sys.argv[4])
    source_crop_x = int(sys.argv[5])
    source_crop_y = int(sys.argv[6])
    source_crop_w = int(sys.argv[7])
    source_crop_h = int(sys.argv[8])

    generate_face_preview(face_image_path, output_path,
                          target_w, target_h,
                          source_crop_x, source_crop_y, source_crop_w, source_crop_h)