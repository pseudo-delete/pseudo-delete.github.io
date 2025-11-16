<?php
    // php/get_neural_data_row.php
    include 'connect.php';

    // Ensure connection is available
    if (!$conn) {
        // Output empty array or specific error message if connection failed
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Database connection failed.']);
        exit;
    }

    // Get ID from POST data
    $id = $_POST['id'];

    // 1. Define the SQL query with a placeholder (?)
    $query = "SELECT `id`, `input1`, `input2`, `input3`, `hidden1`, `hidden2`, `hidden3`, `output1`, `output2`, `output3`, `weight1`, `weight2`, `weight3`, `weight4`, `weight5`, `weight6`, `weight7`, `weight8`, `weight9`, `weight10`, `weight11`, `weight12`, `weight13`, `weight14`, `weight15`, `weight16`, `weight17`, `weight18`, `bias1`, `bias2`, `bias3`, `bias4`, `bias5`, `bias6`, `target1`, `target2`, `target3` FROM `333_memorize_the_image` WHERE id = ?";

    // 2. Prepare the statement
    $stmt = $conn->prepare($query);

    // 3. Check for preparation error
    if (!$stmt) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
        $conn->close();
        exit;
    }

    // 4. Bind the parameter 'i' for integer ID
    $stmt->bind_param('i', $id);

    // 5. Execute the statement
    if (!$stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Execution failed: ' . $stmt->error]);
        $stmt->close();
        $conn->close();
        exit;
    }

    // 6. Get the result object
    $result = $stmt->get_result();

    // 7. Fetch the single row
    $row = $result->fetch_assoc();
    
    // Set the content type header and output the result (null if not found, or the row data)
    header('Content-Type: application/json');
    echo json_encode($row);
    
    $stmt->close();
    $conn->close();
?>