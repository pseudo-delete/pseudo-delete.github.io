$(function()
{
    // establishing AJAX connection to PHP file(database connection))
    $.ajax({
        url: "php/connect.php",
        type: "POST",
        success: function(response)
        {
            console.log("Connection successful: " + response);
        },
        error: function()
        {
            console.log("AJAX connection failed.");
        }
    });

    // append a new row to the neural data table in the database
    function appendNeuralDataRowDb(id, input, target, weight, bias)
    {
        $.ajax(
        {
            url: "php/append_neural_data_row.php",
            type: "POST",
            data:
            {
                id: id, 
                input: input,
                target: target,
                weight: weight,
                bias: bias
            },
            success: function(response)
            {
                console.log(`Neural Net ${id} Added: ` + response);
            },
            error: function()
            {
                console.log(`AJAX adding error ${id}: failure in the process of adding neural rows to the database.`);
            }
        });
    }

    // update a row to the neural data table in the database
    function updateNeuralDataRowDb(id, input, hidden, output, target, weight, bias)
    {
        $.ajax(
        {
            url: "php/update_neural_data_row.php",
            type: "POST",
            data:
            {
                id: id, 
                input: input,
                hidden: hidden,
                output: output,
                target: target,
                weight: weight,
                bias: bias
            },
            success: function(response)
            {
                console.clear();
                console.log(`Neural Net ${id} Updated: ` + response);
            },
            error: function()
            {
                console.clear();
                console.log(`AJAX update error Net ${id}: failure in the process of updating neural rows to the database.`);
            }
        });
    }

    // clear all the rows from the neural data table in the database
    function clearNeuralDataTableDb()
    {
        $.ajax(
        {
            url: "php/clear_neural_data_table.php",
            type: "POST",
            success: function(response)
            {
                console.clear();
                console.log(response);
            },
            error: function()
            {console.clear();
                console.log(`AJAX db clearing error: failure in the process of clearing all rows of neural table of the database.`);
            }
        });
    }

    window.appendNeuralDataRowDb = appendNeuralDataRowDb;
    window.updateNeuralDataRowDb = updateNeuralDataRowDb;
    window.clearNeuralDataTableDb = clearNeuralDataTableDb;

});