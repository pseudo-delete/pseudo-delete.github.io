<?php
    include 'connect.php';

    $id = $_POST['id'];

    $query = "SELECT `id`, `input1`, `input2`, `input3`, `hidden1`, `hidden2`, `hidden3`, `output1`, `output2`, `output3`, `weight1`, `weight2`, `weight3`, `weight4`, `weight5`, `weight6`, `weight7`, `weight8`, `weight9`, `weight10`, `weight11`, `weight12`, `weight13`, `weight14`, `weight15`, `weight16`, `weight17`, `weight18`, `bias1`, `bias2`, `bias3`, `bias4`, `bias5`, `bias6`, `target1`, `target2`, `target3` FROM `333_memorize_the_image` WHERE id=$id";;

    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    echo json_encode($row);
    
    $conn -> close();

?>