print("🔥🔥🔥 THIS IS THE CORRECT face_service.py 🔥🔥🔥")
print("FILE PATH =", __file__)

from flask import Flask, request, jsonify, render_template
import cv2
import numpy as np
import os
from ultralytics import YOLO
from insightface.app import FaceAnalysis

app = Flask(__name__)

# =========================
# CONFIG
# =========================
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
EMBEDDING_DIR = os.path.join(BASE_DIR, "embeddings")
MODEL_PATH = os.path.join(BASE_DIR, "yolov8n.pt")

# Ensure embeddings directory exists
os.makedirs(EMBEDDING_DIR, exist_ok=True)

SIMILARITY_THRESHOLD = 0.5

# =========================
# LOAD MODELS
# =========================
print("[INFO] Loading YOLOv8 model...")
yolo = YOLO(MODEL_PATH)

print("[INFO] Loading InsightFace model...")
face_app = FaceAnalysis(name="buffalo_l")
face_app.prepare(ctx_id=0)

# =========================
# LOAD EMBEDDINGS
# =========================
print("[INFO] Loading embeddings...")
embeddings = {}

for file in os.listdir(EMBEDDING_DIR):
    if file.endswith(".npy"):
        name = os.path.splitext(file)[0]
        path = os.path.join(EMBEDDING_DIR, file)
        emb = np.load(path)

        # ⚠️ NORMALISASI: kalau (N,512) → jadi (512,)
        if emb.ndim == 2:
            print(f"🛠️ Fix embedding shape for {name}: {emb.shape} → mean")
            emb = emb.mean(axis=0)

        embeddings[name] = emb

print("[INFO] Embeddings loaded:", list(embeddings.keys()))

# =========================
# HELPER: COSINE SIMILARITY
# =========================
def cosine_similarity(a, b):
    a = np.array(a)
    b = np.array(b)

    if a.shape != b.shape:
        print("⚠️ Shape mismatch:", a.shape, b.shape)
        return -1.0

    return float(np.dot(a, b) / (np.linalg.norm(a) * np.linalg.norm(b)))

# =========================
# ROUTE: ROOT → TEST CAMERA PAGE
# =========================
@app.route("/")
def index():
    print("📄 Serving test_camera.html via render_template")
    return render_template("test_camera.html")

# =========================
# ROUTE: RECOGNIZE FRAME
# =========================
@app.route("/recognize_frame", methods=["POST"])
def recognize_frame():
    print("📥 /recognize_frame called")

    if "frame" not in request.files:
        return jsonify({"error": "no frame"}), 400

    file = request.files["frame"]
    img_bytes = np.frombuffer(file.read(), np.uint8)
    frame = cv2.imdecode(img_bytes, cv2.IMREAD_COLOR)

    if frame is None:
        return jsonify({"error": "decode failed"}), 400

    print("🖼️ Frame shape:", frame.shape)

    result_name = "unknown"
    best_score = 0.0
    best_bbox = None

    faces = face_app.get(frame)
    print("🟣 InsightFace faces:", len(faces))

    for face in faces:
        emb = np.array(face.embedding)

        for name, db_emb in embeddings.items():
            db_emb = np.array(db_emb)

            if db_emb.ndim == 2:
                db_emb = db_emb.mean(axis=0)

            if emb.shape != db_emb.shape:
                continue

            score = cosine_similarity(emb, db_emb)

            if score > best_score:
                best_score = score
                result_name = name
                best_bbox = face.bbox.astype(int).tolist()

    # =========================
    # AUTO ABSENSI LOGIC
    # =========================
    status = "rejected"
    if best_score >= SIMILARITY_THRESHOLD:
        status = "accepted"

    print("🏁 FINAL RESULT:", result_name, best_score, status)

    return jsonify({
        "name": result_name,
        "score": float(best_score),
        "status": status,
        "bbox": best_bbox
    })


# =========================
# ROUTE: REGISTER FACE
# =========================
@app.route("/register_face", methods=["POST"])
def register_face():
    print("📥 /register_face called")

    # Validate inputs
    if "file" not in request.files:
        return jsonify({"error": "No file uploaded"}), 400

    name = request.form.get("name")
    if not name:
        return jsonify({"error": "No name provided"}), 400

    # Decode the image
    file = request.files["file"]
    img_bytes = np.frombuffer(file.read(), np.uint8)
    img = cv2.imdecode(img_bytes, cv2.IMREAD_COLOR)

    if img is None:
        return jsonify({"error": "Failed to decode image"}), 400

    print(f"🖼️ Image shape: {img.shape}")

    # Detect faces
    faces = face_app.get(img)
    print(f"🟣 Faces detected: {len(faces)}")

    if len(faces) == 0:
        return jsonify({"error": "No face detected"}), 400

    if len(faces) > 1:
        return jsonify({"error": "Multiple faces detected. One face only"}), 400

    # Extract and save embedding
    embedding = np.array(faces[0].embedding)
    save_path = os.path.join(EMBEDDING_DIR, f"{name}.npy")
    np.save(save_path, embedding)
    print(f"💾 Embedding saved to {save_path} | shape: {embedding.shape}")

    # Hot reload: update in-memory embeddings dict
    embeddings[name] = embedding
    print(f"✅ Embeddings updated in memory. Total: {len(embeddings)} → {list(embeddings.keys())}")

    return jsonify({
        "status": "success",
        "message": f"Face registered for {name}"
    })


# =========================
# DEBUG: SHOW ROUTES
# =========================
print("📌 REGISTERED ROUTES:")
print(app.url_map)

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=False)
