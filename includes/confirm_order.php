<?php
    // Include config file
    require_once "./connection.php";

    // Check if the request method is POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $vehicleID = $_POST['vehicleID'];
        $userID = $_POST['userID']; // Use session userID for security
        $fordate = $_POST['rentDate'];
        $status = "pending";
        
        // Validate the vehicleID and userID exist in their respective tables
        $stmt = $conn->prepare('SELECT COUNT(*) FROM products WHERE vehicleID = ?');
        $stmt->bind_param('i', $vehicleID);
        $stmt->execute();
        $stmt->bind_result($vehicleCount);
        $stmt->fetch();
        $stmt->close();

        $stmt = $conn->prepare('SELECT COUNT(*) FROM users WHERE userID = ?');
        $stmt->bind_param('i', $userID);
        $stmt->execute();
        $stmt->bind_result($userCount);
        $stmt->fetch();
        $stmt->close();

        if ($vehicleCount > 0 && $userCount > 0) {
            // Prepare the SQL statement to prevent SQL injection
            $stmt = $conn->prepare('INSERT INTO orders (vehicleID, userID, fordate, status) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('iiss', $vehicleID, $userID, $fordate, $status);
            
            $stmt2 = $conn->prepare('UPDATE products SET vehiclePending = vehiclePending + 1 WHERE vehicleID = ?');
            $stmt2->bind_param('i',$vehicleID);

            if ($stmt->execute()) {
                $stmt2->execute();
                echo "success";
            } else {
                echo "Error: " . $stmt->error;
            }

            // Close the statement and the connection
            $stmt->close();
            $conn->close();
        } else {
            echo "Invalid vehicle ID or user ID.";
        }
    } else {
        echo "Invalid request.";
    }
?>
