<?php
require('./connection.php');
session_start();

if (isset($_POST['orderID']) && isset($_SESSION["userID"])) {
    $orderID = intval($_POST['orderID']);
    $userID = $_SESSION["userID"];

    // Prepare the SQL statement to delete the order (only if it belongs to the user)
    $stmt = $conn->prepare("DELETE FROM orders WHERE orderId = ? AND userId = ?");
    $stmt->bind_param('ii', $orderID, $userID);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "error";
}
?>
