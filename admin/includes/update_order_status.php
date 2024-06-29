<?php
require('../../includes/connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['orderID']) && isset($_POST['action'])) {
        $orderID = $_POST['orderID'];
        $action = $_POST['action'];

        // Determine the new status based on the action
        $newStatus = '';
        if ($action === 'pick') {
            $newStatus = 'picked';
        } elseif ($action === 'return') {
            $newStatus = 'returned';
        } else {
            echo 'Invalid action.';
            exit();
        }

        // Begin a transaction
        $conn->begin_transaction();

        try {
            // Update the order status
            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE orderID = ?");
            $stmt->bind_param('si', $newStatus, $orderID);

            if ($stmt->execute()) {
                // If the action is 'return', increase the vehicle count
                if ($action === 'return') {
                    // Get the vehicleID from the order
                    $stmt = $conn->prepare("SELECT vehicleID FROM orders WHERE orderID = ?");
                    $stmt->bind_param('i', $orderID);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $order = $result->fetch_assoc();
                    $vehicleID = $order['vehicleID'];
                    $stmt->close();

                    // Update the vehicle count in the products table
                    $stmt = $conn->prepare("UPDATE products SET vehicleLeft = vehicleLeft + 1 WHERE vehicleID = ?");
                    $stmt->bind_param('i', $vehicleID);
                    $stmt->execute();
                    $stmt->close();
                }

                // Commit the transaction
                $conn->commit();
                echo 'success';
            } else {
                throw new Exception('Error updating order status: ' . $stmt->error);
            }
        } catch (Exception $e) {
            // Rollback the transaction on error
            $conn->rollback();
            echo 'Error: ' . $e->getMessage();
        }

        $conn->close();
    } else {
        echo 'Invalid order ID or action.';
    }
} else {
    echo 'Invalid request method.';
}
?>
