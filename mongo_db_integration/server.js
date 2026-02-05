const express = require('express');
const { MongoClient } = require('mongodb');

const app = express();
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

const client = new MongoClient('mongodb://localhost:27017');
let db;

async function connectDB() {
  if (!db) {
    await client.connect();
    db = client.db('mongodb_integration_test_db');
    console.log('MongoDB connected');
  }
}

// route called from the browser
app.post('/insert-information', async (req, res) => {
  try {
    await connectDB();

    const { name, age } = req.body;

    const result = await db
      .collection('mongodb_integration_test_collection')
      .insertOne({
        name,
        age,
        date: new Date()
      });

    res.json({
      success: true,
      insertedId: result.insertedId
    });
  } catch (err) {
    console.error(err);
    res.status(500).json({ success: false });
  }
});

app.listen(3000, () => {
  console.log('Server running at http://localhost:3000');
});
