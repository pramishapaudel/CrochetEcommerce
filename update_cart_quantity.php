<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require('./includes/connection.php');
if (!isset($_SESSION['userID'])) {
    exit('not_logged_in');
}
if (isset($_POST['cart_item_id']) && isset($_POST['quantity'])) {
    $cart_item_id = intval($_POST['cart_item_id']);
    $quantity = max(1, intval($_POST['quantity'])); // Prevent quantity < 1
    $sql = "UPDATE cart_items SET quantity=? WHERE cart_item_id=?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) { echo 'prepare_failed'; exit(); }
    $stmt->bind_param("ii", $quantity, $cart_item_id);
    if (!$stmt->execute()) { echo 'execute_failed'; exit(); }
    $stmt->close();
    echo 'success';
    exit();
}
echo 'error';
exit();
?>
