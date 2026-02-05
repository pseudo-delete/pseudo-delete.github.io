const { MongoClient } = require('mongodb');

const uri = 'mongodb://localhost:27017';
const client = new MongoClient(uri);

let db;

// connect once and reuse
async function connect() {
  if (!db) {
    await client.connect();
    db = client.db('mongodb_integration_test_db');
    console.log('Connected to MongoDB');
  }
  return db;
}

async function insertInformation(name, age) {
  const database = await connect(); 

  const res = await database
    .collection('mongodb_integration_test_collection')
    .insertOne({
      name: name,
      age: age,
      date: new Date()
    });

  if (res.acknowledged && res.insertedId) {
    console.log('Insert successful, id:', res.insertedId);
    return { success: true, insertedId: res.insertedId };
  } else {
    console.log('Failed to insert information', res);
    return { success: false, result: res };
  }
}

module.exports = { insertInformation };
