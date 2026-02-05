/* global use, db */
// MongoDB Playground
// To disable this template go to Settings | MongoDB | Use Default Template For Playground.
// Make sure you are connected to enable completions and to be able to run a playground.
// Use Ctrl+Space inside a snippet or a string literal to trigger completions.
// The result of the last command run in a playground is shown on the results panel.
// By default the first 20 documents will be returned with a cursor.
// Use 'console.log()' to print to the debug output.
// For more documentation on playgrounds please refer to
// https://www.mongodb.com/docs/mongodb-vscode/playgrounds/

// Select the database to use.
use('mongodb_integration_test_db');

function insertInformation(name, age) {
    // Insert a single document and capture the result
    const res = db.getCollection('mongodb_integration_test_collection').insertOne(
        { name: name, age: age, date: new Date() }
    );

    // Check the driver return object for success
    if (res && res.acknowledged && res.insertedId) {
        console.log('Insert successful, id:', res.insertedId);
        return { success: true, insertedId: res.insertedId };
    } else {
        console.log('Failed to insert information', res);
        return { success: false, result: res };
    }
}

module.exports = { insertInformation };

// $(function()
// {
//     $("#button-insert").click(function()
//     {
//         let name = $("#input-name").val();
//         let age = $("#input-age").val();
//         insertInformation(name, age);
//     });
// });