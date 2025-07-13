<?php
session_start();
require('./includes/connection.php');

if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_POST['buy_now_product_id']) || !isset($_POST['quantity'])) {
    header('Location: browse.php?error=missing_data');
    exit();
}

$user_id = $_SESSION['userID'];
$product_id = (int)$_POST['buy_now_product_id'];
$quantity = (int)$_POST['quantity'];

// Validate quantity
if ($quantity <= 0) {
    header('Location: browse.php?error=invalid_quantity');
    exit();
}

// Get product details
$product_query = "SELECT * FROM product WHERE productId = ?";
$stmt = $conn->prepare($product_query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header('Location: browse.php');
    exit();
}

// Check if enough stock is available
if ($product['productQuantity'] < $quantity) {
    header('Location: browse.php?error=insufficient_stock');
    exit();
}

// Create a temporary cart for buy now
$cart_query = "INSERT INTO cart (user_id, status) VALUES (?, 'active')";
$stmt = $conn->prepare($cart_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_id = $conn->insert_id;

// Add the product to cart items
$cart_item_query = "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)";
$stmt = $conn->prepare($cart_item_query);
$stmt->bind_param("iii", $cart_id, $product_id, $quantity);
$stmt->execute();

// Redirect to checkout
header('Location: checkout.php');
exit();
?> 