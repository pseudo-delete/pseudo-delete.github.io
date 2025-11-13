<?php
    include 'connect.php';

    $id = $_POST['id'];
    $input = $_POST['input'];
    $target = $_POST['target'];
    $weight = $_POST['weight'];
    $bias = $_POST['bias'];

    $query = `INSERT INTO 333_memorize_the_image(id, input1, input2, input3, target1, target2, target3, weight1, weight2, weight3, weight4, weight5, weight6, weight7, weight8, weight9, weight10, weight11, weight12, weight13, weight14, weight15, weight16, weight17, weight18, bias1, bias2, bias3, bias4, bias5, bias6) VALUES (`.$id.`, `.$input[0].`, `.$input[1].`, `.$input[2].`, `.$target[0].`, `.$target[1].`, `.$target[2].`, `.$weight[0].`, `.$weight[1].`, `.$weight[2].`, `.$weight[3].`, `.$weight[4].`, `.$weight[5].`, `.$weight[6].`, `.$weight[7].`, `.$weight[8].`, `.$weight[9].`, `.$weight[10].`, `.$weight[11].`, `.$weight[12].`, `.$weight[13].`, `.$weight[14].`, `.$weight[15].`, `.$weight[16].`, `.$weight[17].`, `.$bias[0].`, `.$bias[1].`, `.$bias[2].`, `.$bias[3].`, `.$bias[4].`, `.$bias[5].`)`;

    // Execute the query using your database connection
    if ($conn->query($query) === TRUE) {
        echo $id;
    } else {
        echo "Error at net" .$id.": " . $query . "<br>" . $conn->error;
    }
    
    $conn -> close();
?>