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
    // Payment successful, create orders and clear cart
    $cart_id = isset($_GET['purchase_order_id']) ? $_GET['purchase_order_id'] : null;
    if (!$cart_id && isset($data['purchase_order_id'])) {
        $cart_id = $data['purchase_order_id'];
    }
    if ($cart_id) {
        // Get user_id from cart
        $cart_stmt = $conn->prepare("SELECT user_id FROM cart WHERE cart_id=? AND status='active'");
        $cart_stmt->bind_param('i', $cart_id);
        $cart_stmt->execute();
        $cart_result = $cart_stmt->get_result();
        $cart_row = $cart_result->fetch_assoc();
        if ($cart_row) {
            $user_id = $cart_row['user_id'];
            // Get cart items
            $items_stmt = $conn->prepare("SELECT product_id, quantity FROM cart_items WHERE cart_id=?");
            $items_stmt->bind_param('i', $cart_id);
            $items_stmt->execute();
            $items_result = $items_stmt->get_result();
            $order_ids = [];
            while ($item = $items_result->fetch_assoc()) {
                $product_id = $item['product_id'];
                $quantity = $item['quantity'];
                // Create order
                $order_stmt = $conn->prepare("INSERT INTO orders (productId, orderQuantity, userId, date, status, transaction_id, payment_method) VALUES (?, ?, ?, NOW(), 'paid', ?, 'khalti')");
                $order_stmt->bind_param('iiis', $product_id, $quantity, $user_id, $pidx);
                $order_stmt->execute();
                $order_ids[] = $conn->insert_id;
                // Decrease stock
                $update_stock = $conn->prepare("UPDATE product SET productQuantity = productQuantity - ? WHERE productId = ?");
                $update_stock->bind_param('ii', $quantity, $product_id);
                $update_stock->execute();
            }
            // Mark cart as completed
            $clear_cart = $conn->prepare("UPDATE cart SET status='completed' WHERE cart_id=?");
            $clear_cart->bind_param('i', $cart_id);
            $clear_cart->execute();
            // Show success message
            echo '<div style="max-width:500px;margin:60px auto;padding:40px 30px;background:#f8f9ff;border-radius:16px;box-shadow:0 4px 24px rgba(92,45,145,0.08);text-align:center;">';
            echo '<h2 style="color:#5C2D91;margin-bottom:16px;">Order Successful!</h2>';
            echo '<p style="font-size:18px;margin-bottom:24px;">Thank you for your payment.<br>Your order was placed successfully.</p>';
            echo '<p style="font-size:16px;margin-bottom:24px;">You will be redirected to the <a href="index.php">home page</a> in 3 seconds.</p>';
            echo '<a href="index.php" style="display:inline-block;padding:12px 32px;background:#5C2D91;color:#fff;border-radius:8px;font-size:16px;text-decoration:none;font-weight:600;">Go to Home</a>';
            echo '</div>';
            echo '<script>setTimeout(function(){ window.location.href = "index.php"; }, 3000);</script>';
        } else {
            echo '<div style="max-width:500px;margin:60px auto;padding:40px 30px;background:#fff5f5;border-radius:16px;box-shadow:0 4px 24px rgba(220,38,38,0.08);text-align:center;">';
            echo '<h2 style="color:#dc2626;margin-bottom:16px;">Order Error</h2>';
            echo '<p style="font-size:18px;margin-bottom:24px;">Could not find your cart. Please contact support.</p>';
            echo '<a href="checkout.php" style="display:inline-block;padding:12px 32px;background:#dc2626;color:#fff;border-radius:8px;font-size:16px;text-decoration:none;font-weight:600;">Go to Checkout</a>';
            echo '</div>';
        }
    } else {
        echo '<div style="max-width:500px;margin:60px auto;padding:40px 30px;background:#fff5f5;border-radius:16px;box-shadow:0 4px 24px rgba(220,38,38,0.08);text-align:center;">';
        echo '<h2 style="color:#dc2626;margin-bottom:16px;">Order Error</h2>';
        echo '<p style="font-size:18px;margin-bottom:24px;">Could not determine your cart. Please contact support.</p>';
        echo '<a href="checkout.php" style="display:inline-block;padding:12px 32px;background:#dc2626;color:#fff;border-radius:8px;font-size:16px;text-decoration:none;font-weight:600;">Go to Checkout</a>';
        echo '</div>';
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
