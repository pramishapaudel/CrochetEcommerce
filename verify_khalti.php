<?php
// verify_khalti.php - KPG-2 Payment Verification
require_once './includes/connection.php';

if (!isset($_GET['pidx'])) {
    die('Invalid request: missing pidx.');
}
$pidx = $_GET['pidx'];

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://a.khalti.com/api/v2/epayment/lookup/",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode(['pidx' => $pidx]),
    CURLOPT_HTTPHEADER => [
        'Authorization: key a35c5dca465d40bc936205b74d3d5577', // Live secret key
        'Content-Type: application/json',
    ],
]);
$response = curl_exec($curl);
curl_close($curl);

$data = json_decode($response, true);

if (isset($data['status']) && $data['status'] === 'Completed') {
    // Payment successful, update order status
    $order_id = isset($_GET['purchase_order_id']) ? $_GET['purchase_order_id'] : null;
    
    // If order_id is not in GET params, try to get it from the payment data
    if (!$order_id && isset($data['purchase_order_id'])) {
        $order_id = $data['purchase_order_id'];
    }
    
    if ($order_id) {
        // Get transaction details from Khalti response
        $transaction_id = $pidx;
        $payment_amount = isset($data['amount']) ? $data['amount'] / 100 : 0; // Convert from paisa to rupees
        
        $stmt = $conn->prepare("UPDATE orders SET status='paid', payment_method='khalti', transaction_id=? WHERE orderId=?");
        $stmt->bind_param('si', $transaction_id, $order_id);
        $stmt->execute();
        
        // Get order details for stock update
        $order_stmt = $conn->prepare("SELECT userId, productId, orderQuantity FROM orders WHERE orderId=?");
        $order_stmt->bind_param('i', $order_id);
        $order_stmt->execute();
        $order_result = $order_stmt->get_result();
        
        if ($order_row = $order_result->fetch_assoc()) {
            $user_id = $order_row['userId'];
            $product_id = $order_row['productId'];
            $quantity = $order_row['orderQuantity'];
            
            // Decrease stock for the product
            $update_stock = $conn->prepare("UPDATE product SET productQuantity = productQuantity - ? WHERE productId = ?");
            $update_stock->bind_param('ii', $quantity, $product_id);
            $update_stock->execute();
            
            // Clear cart for this user
            $clear_cart = $conn->prepare("UPDATE cart SET status='completed' WHERE user_id=? AND status='active'");
            $clear_cart->bind_param('i', $user_id);
            $clear_cart->execute();
        }
        
        echo '<div style="max-width:500px;margin:60px auto;padding:40px 30px;background:#f8f9ff;border-radius:16px;box-shadow:0 4px 24px rgba(92,45,145,0.08);text-align:center;">';
        echo '<h2 style="color:#5C2D91;margin-bottom:16px;">Order Successful!</h2>';
        echo '<p style="font-size:18px;margin-bottom:24px;">Thank you for your payment.<br>Your order <b>#' . htmlspecialchars($order_id) . '</b> was placed successfully.</p>';
        echo '<p style="font-size:16px;margin-bottom:24px;">You will be redirected to the <a href="index.php">home page</a> in 3 seconds.</p>';
        echo '<a href="index.php" style="display:inline-block;padding:12px 32px;background:#5C2D91;color:#fff;border-radius:8px;font-size:16px;text-decoration:none;font-weight:600;">Go to Home</a>';
        echo '</div>';
        echo '<script>setTimeout(function(){ window.location.href = "index.php"; }, 3000);</script>';
    } else {
        echo '<div style="max-width:500px;margin:60px auto;padding:40px 30px;background:#f8f9ff;border-radius:16px;box-shadow:0 4px 24px rgba(92,45,145,0.08);text-align:center;">';
        echo '<h2 style="color:#5C2D91;margin-bottom:16px;">Order Successful!</h2>';
        echo '<p style="font-size:18px;margin-bottom:24px;">Thank you for your payment.<br>Your order was placed successfully, but the order ID could not be determined.</p>';
        echo '<p style="font-size:16px;margin-bottom:24px;">You will be redirected to the <a href="index.php">home page</a> in 3 seconds.</p>';
        echo '<a href="index.php" style="display:inline-block;padding:12px 32px;background:#5C2D91;color:#fff;border-radius:8px;font-size:16px;text-decoration:none;font-weight:600;">Go to Home</a>';
        echo '</div>';
        echo '<script>setTimeout(function(){ window.location.href = "index.php"; }, 3000);</script>';
    }
} else {
    echo '<div style="max-width:500px;margin:60px auto;padding:40px 30px;background:#fff5f5;border-radius:16px;box-shadow:0 4px 24px rgba(220,38,38,0.08);text-align:center;">';
    echo '<h2 style="color:#dc2626;margin-bottom:16px;">Payment Failed</h2>';
    echo '<p style="font-size:18px;margin-bottom:24px;">Your payment was not completed successfully.</p>';
    echo '<p style="font-size:16px;margin-bottom:24px;">You will be redirected to the <a href="checkout.php">checkout page</a> in 3 seconds.</p>';
    echo '<a href="checkout.php" style="display:inline-block;padding:12px 32px;background:#dc2626;color:#fff;border-radius:8px;font-size:16px;text-decoration:none;font-weight:600;">Go to Checkout</a>';
    echo '</div>';
    echo '<script>setTimeout(function(){ window.location.href = "checkout.php"; }, 3000);</script>';
}
?>
