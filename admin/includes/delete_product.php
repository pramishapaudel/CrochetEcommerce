<?php
    require('../../connection.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $vehicleID = $_POST['vehicleID'];
        
        // Prepare and execute the delete statement
        $stmt = $conn->prepare("DELETE FROM products WHERE vehicleID = ?");
        $stmt->bind_param("i", $vehicleID);
        
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }
        
        $stmt->close();
        $conn->close();
    }
?>
