<?php
session_start();
require_once './includes/connection.php';

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit;
}

// Check if cart_id is provided
if (!isset($_GET['cart_id'])) {
    header('Location: checkout.php');
    exit;
}

$cart_id = $_GET['cart_id'];
$user_id = $_SESSION['userID'];

// Get cart items and user info
$query = "SELECT u.userName as user_name, u.Email as user_email, u.Contact as user_phone
          FROM users u
          WHERE u.userId = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    header('Location: checkout.php');
    exit;
}

// Get cart items
$cart_items_query = "SELECT ci.quantity, p.productName, p.productPrice
                     FROM cart_items ci
                     JOIN product p ON ci.product_id = p.productId
                     WHERE ci.cart_id = ?";
$stmt = mysqli_prepare($conn, $cart_items_query);
mysqli_stmt_bind_param($stmt, "i", $cart_id);
mysqli_stmt_execute($stmt);
$cart_items_result = mysqli_stmt_get_result($stmt);
$cart_items = mysqli_fetch_all($cart_items_result, MYSQLI_ASSOC);

if (empty($cart_items)) {
    header('Location: checkout.php');
    exit;
}

// Calculate total amount
$total_amount = 0;
$order_names = [];
foreach ($cart_items as $item) {
    $total_amount += $item['productPrice'] * $item['quantity'];
    $order_names[] = $item['productName'] . ' x' . $item['quantity'];
}

// Prepare Khalti KPG-2 payload
$payload = [
    "return_url" => "http://localhost/CrochetEcommerce/verify_khalti.php",
    "website_url" => "http://localhost/CrochetEcommerce/",
    "amount" => (int)($total_amount * 100), // in paisa
    "purchase_order_id" => (string)$cart_id,
    "purchase_order_name" => "Cart #$cart_id: " . implode(', ', $order_names),
    "customer_info" => [
        "name" => $user['user_name'],
        "email" => $user['user_email'],
        "phone" => $user['user_phone']
    ]
];

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://a.khalti.com/api/v2/epayment/initiate/',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => [
        'Authorization: key a35c5dca465d40bc936205b74d3d5577', // Live secret key
        'Content-Type: application/json',
    ],
]);

$response = curl_exec($curl);
curl_close($curl);

$data = json_decode($response, true);

// Debug information
error_log("Khalti API Response: " . $response);
error_log("Khalti API Data: " . print_r($data, true));

if (isset($data['payment_url'])) {
    header("Location: " . $data['payment_url']);
    exit;
} else {
    echo "<h2>Error initiating Khalti payment</h2>";
    echo "<p>Response: " . htmlspecialchars($response) . "</p>";
    if (isset($data['detail'])) {
        echo "<p>Detail: " . htmlspecialchars($data['detail']) . "</p>";
    }
    echo "<p><a href='checkout.php'>Back to Checkout</a></p>";
}
?> 