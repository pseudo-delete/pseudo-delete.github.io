import { initializeApp } from "https://www.gstatic.com/firebasejs/11.0.0/firebase-app.js";
import { getFirestore, collection, doc, addDoc, setDoc, getDocs, query, orderBy, limit, writeBatch, deleteDoc, updateDoc } from "https://www.gstatic.com/firebasejs/11.0.0/firebase-firestore.js";

const firebaseConfig = {
  apiKey: "AIzaSyCKVbKlfH83XULHnmoyZ_gip3UrxO_i-bc",
  authDomain: "gero-ai.firebaseapp.com",
  projectId: "gero-ai"
};

const app = initializeApp(firebaseConfig);
const db = getFirestore(app);

// adding data, with custom id
async function addData(data_id) {
  await addDoc(collection(db, "image_integration_collection"), {
    id: data_id,
    input_1: $("#input1-val").text(),
    input_2: $("#input2-val").text(),
    input_3: $("#input3-val").text(),
    hidden_1: 0,
    hidden_2: 0,
    hidden_3: 0,
    output_1: 0,
    output_2: 0,
    output_3: 0,
    target_1: $("#target1-val").text(),
    target_2: $("#target2-val").text(),
    target_3: $("#target3-val").text(),
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

// 🔹 Load data
async function loadTable() {
  const snapshot = await getDocs(query(collection(db, "image_integration_collection"), orderBy("id", "asc")));
  const tbody = $("#data-table tbody");
  tbody.empty(); // clear old rows

  snapshot.forEach((doc) => {
    const d = doc.data();
    tbody.append(`
      <tr class="tr-neural-data" id="row-${d.id}">
        <td id="doc-id-${d.id}">${doc.id}</td>
        <td id="id-${d.id}">${d.id || ''}</td>
        <td id="input1-${d.id}">${d.input_1 || ''}</td>
        <td id="input2-${d.id}">${d.input_2 || ''}</td>
        <td id="input3-${d.id}">${d.input_3 || ''}</td>
        <td id="hidden1-${d.id}">${d.hidden_1 || ''}</td>
        <td id="hidden2-${d.id}">${d.hidden_2 || ''}</td>
        <td id="hidden3-${d.id}">${d.hidden_3 || ''}</td>
        <td id="output1-${d.id}">${d.output_1 || ''}</td>
        <td id="output2-${d.id}">${d.output_2 || ''}</td>
        <td id="output3-${d.id}">${d.output_3 || ''}</td>
        <td id="target1-${d.id}">${d.target_1 || ''}</td>
        <td id="target2-${d.id}">${d.target_2 || ''}</td>
        <td id="target3-${d.id}">${d.target_3 || ''}</td>
        <td id="weight1-${d.id}">${d.weight_1 || ''}</td>
        <td id="weight2-${d.id}">${d.weight_2 || ''}</td>
        <td id="weight3-${d.id}">${d.weight_3 || ''}</td>
        <td id="weight4-${d.id}">${d.weight_4 || ''}</td>
        <td id="weight5-${d.id}">${d.weight_5 || ''}</td>
        <td id="weight6-${d.id}">${d.weight_6 || ''}</td>
        <td id="weight7-${d.id}">${d.weight_7 || ''}</td>
        <td id="weight8-${d.id}">${d.weight_8 || ''}</td>
        <td id="weight9-${d.id}">${d.weight_9 || ''}</td>
        <td id="weight10-${d.id}">${d.weight_10 || ''}</td>
        <td id="weight11-${d.id}">${d.weight_11 || ''}</td>
        <td id="weight12-${d.id}">${d.weight_12 || ''}</td>
        <td id="weight13-${d.id}">${d.weight_13 || ''}</td>
        <td id="weight14-${d.id}">${d.weight_14 || ''}</td>
        <td id="weight15-${d.id}">${d.weight_15 || ''}</td>
        <td id="weight16-${d.id}">${d.weight_16 || ''}</td>
        <td id="weight17-${d.id}">${d.weight_17 || ''}</td>
        <td id="weight18-${d.id}">${d.weight_18 || ''}</td>
        <td id="bias1-${d.id}">${d.bias_1 || ''}</td>
        <td id="bias2-${d.id}">${d.bias_2 || ''}</td>
        <td id="bias3-${d.id}">${d.bias_3 || ''}</td>
        <td id="bias4-${d.id}">${d.bias_4 || ''}</td>
        <td id="bias5-${d.id}">${d.bias_5 || ''}</td>
        <td id="bias6-${d.id}">${d.bias_6 || ''}</td>
      </tr>
    `);
  });
}
// end of loading data

/* updating data */
// for a specific field
async function updateField(collectionName, docId, fieldName, newValue) {
  const docRef = doc(db, collectionName, docId);

  try {
    await updateDoc(docRef, {
      [fieldName]: newValue  // dynamic field key
    });
    console.log(`Field '${fieldName}' updated to '${newValue}'`);
  } catch (error) {
    console.error("Error updating field:", error);
  }
}

// batch update
// async function batchUpdateFields(collectionName, docId, fieldsToUpdate) {
//   await updateDoc(doc(db, collectionName, docId), {
//     name: "Alice",
//     age: 30,
//     active: true
//   });
// }

/* end of updating data */

// deleting all the documents in the collection
async function deleteCollection(collectionPath, batchSize = 500) {
  const colRef = collection(db, collectionPath);
  const q = query(colRef, limit(batchSize));

  return new Promise((resolve, reject) => {
    deleteQueryBatch(db, q, resolve).catch(reject);
  });
}

// 🔹 Equivalent of deleteQueryBatch()
async function deleteQueryBatch(db, q, resolve) {
  const snapshot = await getDocs(q);

  if (snapshot.empty) {
    resolve();
    return;
  }

  const batch = writeBatch(db);
  snapshot.docs.forEach((docSnap) => {
    batch.delete(docSnap.ref);
  });

  await batch.commit();

  // Recurse on next batch (browser version of process.nextTick)
  setTimeout(() => {
    deleteQueryBatch(db, q, resolve);
  }, 0);
}
// end of deleting all the documents in the collection

// creating collection
// Create a new collection and document, collectionName will be the container for the name of collection
async function createCollection(collectionName) {
  await setDoc(doc(db, collectionName, "docu1"), {
    fld: "initialization"
  });

  console.log("Collection " + collectionName + " and document 'docu1' created!");
}
// end of create collection

window.db = db; // so you can access it from console for debugging
window.collection = collection; // so you can access it from console for debugging
window.doc = doc; // so you can access it from console for debugging
window.addDoc = addDoc; // so you can access it from console for debugging
window.updateDoc = updateDoc; // so you can access it from console for debugging
window.getDocs = getDocs; // so you can access it from console for debugging
window.addData = addData; // so you can call it from button onclick
window.createCollection = createCollection; // so you can call it from button onclick
window.deleteCollection = deleteCollection; // so you can call it from button onclick
window.loadTable = loadTable; // so you can call it from button onclick


await deleteCollection("image_integration_collection")// Initial delete of all documents in the collection for cleaning up the data-table for use
  .then(() => console.log("All documents deleted"))
  .catch(console.error);

await createCollection("image_integration_collection"); // initial birth of deleted collection

loadTable(); // initial load