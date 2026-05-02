<?php
require('../../includes/connection.php');
session_start();

if (!isset($_SESSION['adminID'])) {
    echo 'Unauthorized';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'Invalid request method.';
    exit();
}

$orderID = isset($_POST['orderID']) ? (int) $_POST['orderID'] : 0;
$newStatus = $_POST['status'] ?? '';
$allowedStatuses = ['pending', 'complete', 'paid', 'cancelled'];

if ($orderID <= 0 || !in_array($newStatus, $allowedStatuses, true)) {
    echo 'Invalid order ID or status.';
    exit();
}

$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE orderId = ?");
$stmt->bind_param('si', $newStatus, $orderID);

if ($stmt->execute()) {
    echo $stmt->affected_rows >= 0 ? 'success' : 'No rows updated';
} else {
    echo 'Error: ' . $stmt->error;
}

$stmt->close();
$conn->close();
?>
