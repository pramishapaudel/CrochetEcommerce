<?php
session_start();
require_once './includes/connection.php';

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit;
}

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    header('Location: checkout.php');
    exit;
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['userID'];

// Get order details
$query = "
    SELECT o.*, u.userName as user_name, u.Email as user_email, u.Contact as user_phone, p.productName, p.productPrice
    FROM orders o 
    JOIN users u ON o.userId = u.userId 
    JOIN product p ON o.productId = p.productId
    WHERE o.orderId = ? AND o.userId = ? AND o.status = 'pending'
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    header('Location: checkout.php');
    exit;
}

// Calculate total amount
$total_amount = $order['productPrice'] * $order['orderQuantity'];

// Prepare Khalti KPG-2 payload
$payload = [
    "return_url" => "http://localhost/CrochetEcommerce/verify_khalti.php",
    "website_url" => "http://localhost/CrochetEcommerce/",
    "amount" => (int)($total_amount * 100), // in paisa
    "purchase_order_id" => (string)$order['orderId'],
    "purchase_order_name" => "Order #" . $order['orderId'] . " - " . $order['productName'],
    "customer_info" => [
        "name" => $order['user_name'],
        "email" => $order['user_email'],
        "phone" => $order['user_phone']
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