<?php
session_start();
require_once './includes/connection.php';

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['userID'];

// Create a test order for Khalti testing
if (isset($_POST['create_test_order'])) {
    // Get a product from the database
    $product_query = "SELECT productId, productName, productPrice FROM product LIMIT 1";
    $result = $conn->query($product_query);
    $product = $result->fetch_assoc();
    
    if ($product) {
        // Create a test order
        $query = "INSERT INTO orders (productId, orderQuantity, userId, date, status, payment_method) 
                  VALUES (?, 1, ?, NOW(), 'pending', 'khalti')";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $product['productId'], $user_id);
        $stmt->execute();
        $order_id = $conn->insert_id;
        
        // Redirect to Khalti payment
        header("Location: initiate_khalti.php?order_id=" . $order_id);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Khalti Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .test-btn {
            background: linear-gradient(135deg, #5C2D91, #7B4397);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 10px 0;
        }
        .test-btn:hover {
            background: linear-gradient(135deg, #4A1A7A, #6A2F85);
        }
        .info-box {
            background: #f8f9ff;
            border: 1px solid #5C2D91;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>Test Khalti KPG-2 Payment</h1>
        
        <div class="info-box">
            <h3>Testing Instructions:</h3>
            <ol>
                <li>Click the "Create Test Order" button below</li>
                <li>This will create a test order and redirect to Khalti payment</li>
                <li>Complete the payment on Khalti's test environment</li>
                <li>You'll be redirected back to verify_khalti.php</li>
                <li>Check if the order status is updated to 'paid'</li>
            </ol>
        </div>
        
        <form method="POST">
            <button type="submit" name="create_test_order" class="test-btn">
                Create Test Order & Pay with Khalti
            </button>
        </form>
        
        <div class="info-box">
            <h3>Important Notes:</h3>
            <ul>
                <li>This uses the live Khalti secret key: <code>7cc5d349905c4e14bd9ddb7505f55d6f</code></li>
                <li>Make sure your server is accessible at <code>http://localhost/CrochetEcommerce/</code></li>
                <li>Check the error logs for any API response issues</li>
                <li>The payment will be processed in Khalti's test environment</li>
            </ul>
        </div>
        
        <a href="index.php" class="test-btn">Back to Home</a>
    </div>
</body>
</html> 