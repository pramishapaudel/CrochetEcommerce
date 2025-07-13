<?php
session_start();
require('./includes/connection.php');

if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['userID'];
$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity']);

// Find or create active cart
$cart_sql = "SELECT cart_id FROM cart WHERE user_id=? AND status='active'";
$stmt = $conn->prepare($cart_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($cart_id);
if ($stmt->fetch()) {
    // Cart exists
} else {
    // Create new cart
    $stmt->close();
    $conn->query("INSERT INTO cart (user_id) VALUES ($user_id)");
    $cart_id = $conn->insert_id;
}
$stmt->close();

// Check if product already in cart
$item_sql = "SELECT cart_item_id, quantity FROM cart_items WHERE cart_id=? AND product_id=?";
$stmt = $conn->prepare($item_sql);
$stmt->bind_param("ii", $cart_id, $product_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->bind_result($cart_item_id, $old_quantity);
    $stmt->fetch();
    $new_quantity = $old_quantity + $quantity;
    $update_sql = "UPDATE cart_items SET quantity=? WHERE cart_item_id=?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ii", $new_quantity, $cart_item_id);
    $update_stmt->execute();
    $update_stmt->close();
} else {
    $insert_sql = "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("iii", $cart_id, $product_id, $quantity);
    $insert_stmt->execute();
    $insert_stmt->close();
}
$stmt->close();

// Get new cart count
$cart_count = 0;
$cart_sql = "SELECT cart_id FROM cart WHERE user_id=? AND status='active'";
$stmt = $conn->prepare($cart_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($cart_id);
$stmt->fetch();
$stmt->close();

if ($cart_id) {
    $count_sql = "SELECT SUM(quantity) FROM cart_items WHERE cart_id=?";
    $stmt = $conn->prepare($count_sql);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $stmt->bind_result($cart_count);
    $stmt->fetch();
    $stmt->close();
    if (!$cart_count) $cart_count = 0;
}

header('Content-Type: application/json');
echo json_encode(['cart_count' => $cart_count]);
exit();
?>
