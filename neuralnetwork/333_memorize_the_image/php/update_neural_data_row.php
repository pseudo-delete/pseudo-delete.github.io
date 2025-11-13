<?php
    include 'connect.php';

    $id = $_POST['id'];
    $input = $_POST['input'];
    $hidden = $_POST['hidden'];
    $output = $_POST['output'];
    $target = $_POST['target'];
    $weight = $_POST['weight'];
    $bias = $_POST['bias'];

    $query = "UPDATE `333_memorize_the_image` SET 
        input1=".$input[0].",
        input2=".$input[1].",
        input3=".$input[2].",
        hidden1=".$hidden[0].",
        hidden2=".$hidden[1].",
        hidden3=".$hidden[2].",
        output1=".$output[0].",
        output2=".$output[1].",
        output3=".$output[2].",
        target1=".$target[0].",
        target2=".$target[1].",
        target3=".$target[2].",
        weight1=".$weight[0].",
        weight2=".$weight[1].",
        weight3=".$weight[2].",
        weight4=".$weight[3].",
        weight5=".$weight[4].",
        weight6=".$weight[5].",
        weight7=".$weight[6].",
        weight8=".$weight[7].",
        weight9=".$weight[8].",
        weight10=".$weight[9].",
        weight11=".$weight[10].",
        weight12=".$weight[11].",
        weight13=".$weight[12].",
        weight14=".$weight[13].",
        weight15=".$weight[14].",
        weight16=".$weight[15].",
        weight17=".$weight[16].",
        weight18=".$weight[17].",
        bias1=".$bias[0].",
        bias2=".$bias[1].",
        bias3=".$bias[2].",
        bias4=".$bias[3].",
        bias5=".$bias[4].",
        bias6=".$bias[5]."
    WHERE id=".$id.";
    ";

    // Execute the query using your database connection
    if ($conn->query($query) === TRUE) {
        echo $id;
    } else {
        echo "Error at net" .$id.": " . $query . "<br>" . $conn->error;
    }
    
    $conn -> close();
?>