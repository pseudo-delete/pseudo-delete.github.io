import { initializeApp } from "https://www.gstatic.com/firebasejs/11.0.0/firebase-app.js";
import { getFirestore, collection, addDoc } from "https://www.gstatic.com/firebasejs/11.0.0/firebase-firestore.js";

const firebaseConfig = {
  apiKey: "AIzaSyCKVbKlfH83XULHnmoyZ_gip3UrxO_i-bc",
  projectId: "gero-ai"
};

const app = initializeApp(firebaseConfig);
const db = getFirestore(app);