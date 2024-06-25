<?php
    require('./connection.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $vehicleID = $_POST['vehicleID'];
        $userID = $_POST['userID'];

        // Prepare the SQL statement to prevent SQL injection
        $stmt = $conn->prepare('INSERT INTO orders (vehicleID, userID) VALUES (?, ?)');
        $stmt->bind_param('ss', $vehicleID, $userID);

        if ($stmt->execute()) {
            echo "Order confirmed successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement and the connection
        $stmt->close();
        $conn->close();
    } else {
        echo "Invalid request.";
    }
?>
