<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require('./includes/connection.php');
if (!isset($_SESSION['userID'])) {
    exit('not_logged_in');
}
if (isset($_POST['cart_item_id'])) {
    $cart_item_id = intval($_POST['cart_item_id']);
    $sql = "DELETE FROM cart_items WHERE cart_item_id=?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) { echo 'prepare_failed'; exit(); }
    $stmt->bind_param("i", $cart_item_id);
    if (!$stmt->execute()) { echo 'execute_failed'; exit(); }
    $stmt->close();
    echo 'success';
    exit();
}
echo 'error';
exit();
?>
