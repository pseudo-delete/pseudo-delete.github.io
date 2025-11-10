import { initializeApp } from "https://www.gstatic.com/firebasejs/11.0.0/firebase-app.js";
import { getFirestore, collection, addDoc } from "https://www.gstatic.com/firebasejs/11.0.0/firebase-firestore.js";

const firebaseConfig = {
  apiKey: "YOUR_API_KEY",
  projectId: "YOUR_PROJECT_ID"
};

const app = initializeApp(firebaseConfig);
const db = getFirestore(app);

async function addData() {
  await addDoc(collection(db, "333_image_integration"), {
    id: 1,
    name: "Gero",
    age: 25
  });
  console.log("Document added!");
}

window.addData = addData; // so you can call it from button onclick