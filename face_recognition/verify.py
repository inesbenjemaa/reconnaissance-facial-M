import sys
import joblib
import cv2
import numpy as np

model = joblib.load("face_classifier_model.pkl")

def extract_face(image_path):
    img = cv2.imread(image_path)
    face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml')
    gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
    faces = face_cascade.detectMultiScale(gray, 1.1, 4)

    if len(faces) == 0:
        return None

    x, y, w, h = faces[0]
    face = cv2.resize(gray[y:y+h, x:x+w], (100, 100))
    return face.flatten().reshape(1, -1)

if __name__ == "__main__":
    path = sys.argv[1]
    face = extract_face(path)
    if face is None:
        print("No face detected")
        sys.exit()

    prediction = model.predict(face)[0]
    print(prediction)
