// MongoDB Playground
// Use Ctrl+Space inside a snippet or a string literal to trigger completions.

// The current database to use.
use("tic_tac_toe_db");

try
{
  db.board_winning_combination
    .find({}, { _id: 0 })
    .forEach(doc => {
      printjson(doc);
    });
}
catch (error)
{
  print("Error:", error);
}
