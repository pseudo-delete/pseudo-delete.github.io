<?php
    include 'connect.php';

    if ($conn->query("TRUNCATE TABLE `333_memorize_the_image`;") === TRUE) {
        echo $id;
    } else {
        echo "Error at net" .$id.": " . $query . "<br>" . $conn->error;
    }

    return "Clearing of database data table successful.";
    
    $conn -> close();
?>