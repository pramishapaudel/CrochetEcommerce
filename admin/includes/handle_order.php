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
        $vehicleID = $_POST['vehicleID'];

        if ($action == 'accept') {
            // Update the order status to accepted
            $stmt = $conn->prepare("UPDATE orders 
                                    JOIN product ON orders.productId = product.productId
                                    SET orders.status = 'complete', product.productQuantity = product.productQuantity - 1
                                    WHERE orders.orderId = ?");
        } elseif ($action == 'reject') {
            // Update the order status to rejected
            $stmt = $conn->prepare("DELETE FROM orders WHERE orderId = ?");
            // $stmt2 = $conn->prepare("UPDATE product SET orders.status='rejected' WHERE productId= ?");
            // $stmt2->bind_param('i', $vehicleID);
            // $stmt2->execute();
        }

        if (isset($stmt)) {
            $stmt->bind_param('i', $orderID);
            if ($stmt->execute()) {
                echo 'success';
            } else {
                echo "Error: " . $stmt->error;;
            }
            $stmt->close();
        } else {
            echo 'error';
        }
    }

    $conn->close();
?>
