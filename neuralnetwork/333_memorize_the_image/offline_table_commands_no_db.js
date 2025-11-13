
// displaying data in the table
function appendNeuralDataRow(id, input, target, weight, bias) {
    const tbody = $("#data-table tbody");
    tbody.append(`
      <tr class="tr-neural-data" id="row-${id}">
        <td id="id-${id}">${id}</td>
        <td id="input1-${id}">${input[0]}</td>
        <td id="input2-${id}">${input[1]}</td>
        <td id="input3-${id}">${input[2]}</td>
        <td id="hidden1-${id}"></td>
        <td id="hidden2-${id}"></td>
        <td id="hidden3-${id}"></td>
        <td id="output1-${id}"></td>
        <td id="output2-${id}"></td>
        <td id="output3-${id}"></td>
        <td id="target1-${id}">${target[0]}</td>
        <td id="target2-${id}">${target[1]}</td>
        <td id="target3-${id}">${target[2]}</td>
        <td id="weight1-${id}">${weight[0]}</td>
        <td id="weight2-${id}">${weight[1]}</td>
        <td id="weight3-${id}">${weight[2]}</td>
        <td id="weight4-${id}">${weight[3]}</td>
        <td id="weight5-${id}">${weight[4]}</td>
        <td id="weight6-${id}">${weight[5]}</td>
        <td id="weight7-${id}">${weight[6]}</td>
        <td id="weight8-${id}">${weight[7]}</td>
        <td id="weight9-${id}">${weight[8]}</td>
        <td id="weight10-${id}">${weight[9]}</td>
        <td id="weight11-${id}">${weight[10]}</td>
        <td id="weight12-${id}">${weight[11]}</td>
        <td id="weight13-${id}">${weight[12]}</td>
        <td id="weight14-${id}">${weight[13]}</td>
        <td id="weight15-${id}">${weight[14]}</td>
        <td id="weight16-${id}">${weight[15]}</td>
        <td id="weight17-${id}">${weight[16]}</td>
        <td id="weight18-${id}">${weight[17]}</td>
        <td id="bias1-${id}">${bias[0]}</td>
        <td id="bias2-${id}">${bias[1]}</td>
        <td id="bias3-${id}">${bias[2]}</td>
        <td id="bias4-${id}">${bias[3]}</td>
        <td id="bias5-${id}">${bias[4]}</td>
        <td id="bias6-${id}">${bias[5]}</td>
      </tr>
    `);

    appendNeuralDataRowDb(id, input, target, weight, bias);// updating the database
}// end of function appendNeuralDataRow

function updateNeuralDataRow(id, input, hidden, output, target, weight, bias)
{
    $("#input1-"+id).text(parseFloat(input[0]));
    $("#input2-"+id).text(parseFloat(input[1]));
    $("#input3-"+id).text(parseFloat(input[2]));
    $("#hidden1-"+id).text(parseFloat(hidden[0]));
    $("#hidden2-"+id).text(parseFloat(hidden[1]));
    $("#hidden3-"+id).text(parseFloat(hidden[2]));
    $("#output1-"+id).text(parseFloat(output[0]));
    $("#output2-"+id).text(parseFloat(output[1]));
    $("#output3-"+id).text(parseFloat(output[2]));
    $("#weight1-"+id).text(parseFloat(weight[0]));
    $("#weight2-"+id).text(parseFloat(weight[1]));
    $("#weight3-"+id).text(parseFloat(weight[2]));
    $("#weight4-"+id).text(parseFloat(weight[3]));
    $("#weight5-"+id).text(parseFloat(weight[4]));
    $("#weight6-"+id).text(parseFloat(weight[5]));
    $("#weight7-"+id).text(parseFloat(weight[6]));
    $("#weight8-"+id).text(parseFloat(weight[7]));
    $("#weight9-"+id).text(parseFloat(weight[8]));
    $("#weight10-"+id).text(parseFloat(weight[9]));
    $("#weight11-"+id).text(parseFloat(weight[10]));
    $("#weight12-"+id).text(parseFloat(weight[11]));
    $("#weight13-"+id).text(parseFloat(weight[12]));
    $("#weight14-"+id).text(parseFloat(weight[13]));
    $("#weight15-"+id).text(parseFloat(weight[14]));
    $("#weight16-"+id).text(parseFloat(weight[15]));
    $("#weight17-"+id).text(parseFloat(weight[16]));
    $("#weight18-"+id).text(parseFloat(weight[17]));
    $("#bias1-"+id).text(parseFloat(bias[0]));
    $("#bias2-"+id).text(parseFloat(bias[1]));
    $("#bias3-"+id).text(parseFloat(bias[2]));
    $("#bias4-"+id).text(parseFloat(bias[3]));
    $("#bias5-"+id).text(parseFloat(bias[4]));
    $("#bias6-"+id).text(parseFloat(bias[5]));
    $("#target1-"+id).text(parseFloat(target[0]));
    $("#target2-"+id).text(parseFloat(target[1]));
    $("#target3-"+id).text(parseFloat(target[2]));

    updateNeuralDataRowDb(id, input, hidden, output, target, weight, bias);// updating the database
}

function clearNeuralDataTable()
{
    const tbody = $("#data-table tbody");
    tbody.empty(); // clear all rows

    // clearNeuralDataTableDb();// clearing the database
}