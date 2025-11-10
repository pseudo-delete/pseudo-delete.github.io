import { initializeApp } from "https://www.gstatic.com/firebasejs/11.0.0/firebase-app.js";
import { getFirestore, collection, addDoc } from "https://www.gstatic.com/firebasejs/11.0.0/firebase-firestore.js";

const firebaseConfig = {
  apiKey: "AIzaSyCKVbKlfH83XULHnmoyZ_gip3UrxO_i-bc",
  projectId: "gero-ai"
};

const app = initializeApp(firebaseConfig);
const db = getFirestore(app);

    /*
    input_1
    input_2
    input_3
    hidden_1
    hidden_2
    hidden_3
    output_1
    output_2
    output_3
    target_1
    target_2
    target_3
    weight_1
    weight_2
    weight_3
    weight_4
    weight_5
    weight_6
    weight_7
    weight_8
    weight_9
    weight_10
    weight_11
    weight_12
    weight_13
    weight_14
    weight_15
    weight_16
    weight_17
    weight_18
    bias_1
    bias_2
    bias_3
    bias_4
    bias_5
    bias_6
     */
async function addData() {
  await addDoc(collection(db, "image_integration_collection"), {
    id: 1,
    input_1: $("#input1-val").text(),
    input_2: $("#input2-val").text(),
    input_3: $("#input3-val").text(),
    hidden_1: 0,
    hidden_2: 0,
    hidden_3: 0,
    output_1: 0,
    output_2: 0,
    output_3: 0,
    target_1: 0,
    target_2: 0,
    target_3: 0,
    weight_1: $("#weight1-val").text(),
    weight_2: $("#weight2-val").text(),
    weight_3: $("#weight3-val").text(),
    weight_4: $("#weight4-val").text(),
    weight_5: $("#weight5-val").text(),
    weight_6: $("#weight6-val").text(),
    weight_7: $("#weight7-val").text(),
    weight_8: $("#weight8-val").text(),
    weight_9: $("#weight9-val").text(),
    weight_10: $("#weight10-val").text(),
    weight_11: $("#weight11-val").text(),
    weight_12: $("#weight12-val").text(),
    weight_13: $("#weight13-val").text(),
    weight_14: $("#weight14-val").text(),
    weight_15: $("#weight15-val").text(),
    weight_16: $("#weight16-val").text(),
    weight_17: $("#weight17-val").text(),
    weight_18: $("#weight18-val").text(),
    bias_1: $("#bias1-val").text(),
    bias_2: $("#bias2-val").text(),
    bias_3: $("#bias3-val").text(),
    bias_4: $("#bias4-val").text(),
    bias_5: $("#bias5-val").text(),
    bias_6: $("#bias6-val").text()
  });
  console.log("Document added!");
}

window.addData = addData; // so you can call it from button onclick