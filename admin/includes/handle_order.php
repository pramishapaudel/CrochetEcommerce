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
        // First check if enough quantity is available
        $checkStmt = $conn->prepare("
            SELECT 
                product.productQuantity,
                orders.orderQuantity
            FROM orders 
            JOIN product ON orders.productId = product.productId
            WHERE orders.orderId = ?
        ");
        $checkStmt->bind_param('i', $orderID);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $row = $result->fetch_assoc();
        $checkStmt->close();

        // Debug: Log the order quantity and product quantity
        error_log("Order ID: $orderID, Order Quantity: " . $row['orderQuantity'] . ", Product Quantity: " . $row['productQuantity']);

        if ($row['productQuantity'] < $row['orderQuantity']) {
            echo 'Error: Insufficient stock';
            $conn->close();
            exit();
        }

        // Update the order status to accepted and reduce product quantity by ordered quantity
        $stmt = $conn->prepare("
            UPDATE orders 
            JOIN product ON orders.productId = product.productId
            SET 
                orders.status = 'complete', 
                product.productQuantity = product.productQuantity - orders.orderQuantity
            WHERE orders.orderId = ?
        ");
    } elseif ($action == 'reject') {
        // Delete the order
        $stmt = $conn->prepare("DELETE FROM orders WHERE orderId = ?");
    }

    if (isset($stmt)) {
        $stmt->bind_param('i', $orderID);
        if ($stmt->execute()) {
            // Debug: Log the number of affected rows
            error_log("Update successful, affected rows: " . $stmt->affected_rows);
            echo 'success';
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo 'error';
    }
}

$conn->close();
?>