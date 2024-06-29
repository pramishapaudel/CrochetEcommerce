<?php
    require('../../includes/connection.php');
    session_start();

    if (!isset($_SESSION['adminID'])) {
        echo 'Unauthorized';
        exit();
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $action = $_POST['action'];
        $orderID = $_POST['orderID'];

        if ($action == 'accept') {
            // Update the order status to accepted
            $stmt = $conn->prepare("UPDATE orders 
                                    JOIN products ON orders.vehicleID = products.vehicleID
                                    SET orders.status = 'complete', products.vehicleLeft = products.vehicleLeft - 1
                                    WHERE orders.orderID = ?");
        } elseif ($action == 'reject') {
            // Update the order status to rejected
            $stmt = $conn->prepare("UPDATE orders SET status = 'rejected' WHERE orderID = ?");
        }

        if (isset($stmt)) {
            $stmt->bind_param('i', $orderID);
            if ($stmt->execute()) {
                echo 'success';
            } else {
                echo 'error';
            }
            $stmt->close();
        } else {
            echo 'error';
        }
    }

    $conn->close();
?>
