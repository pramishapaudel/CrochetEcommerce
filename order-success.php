<?php
session_start();
require('./includes/connection.php');

if (!isset($_SESSION['userID'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['order_id'])) {
    header('Location: index.php');
    exit();
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['userID'];

// Get order details with user and product information
$query = "
    SELECT 
        o.*,
        u.userName,
        u.Contact,
        u.Email,
        u.Address,
        p.productName,
        p.productPrice,
        (p.productPrice * o.orderQuantity) as total_amount
    FROM orders o 
    JOIN users u ON o.userId = u.userId 
    JOIN product p ON o.productId = p.productId 
    WHERE o.orderId = ? AND o.userId = ?
";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - Crochet Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgb(82, 5, 5);
        }
        
        .success-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }
        
        .success-icon {
            font-size: 80px;
            color: rgb(11, 212, 11);
            margin-bottom: 20px;
        }
        
        .success-title {
            font-size: 28px;
            color: rgb(82, 5, 5);
            margin-bottom: 15px;
            font-weight: bold;
        }
        
        .success-message {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .order-details {
            background: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: left;
            border: 1px solid #e9ecef;
        }
        
        .order-details h3 {
            color: rgb(82, 5, 5);
            margin-bottom: 15px;
            border-bottom: 2px solid #FF758F;
            padding-bottom: 10px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
        }
        
        .detail-label {
            font-weight: bold;
            color: rgb(82, 5, 5);
        }
        
        .detail-value {
            color: #333;
        }
        
        .amount {
            color: rgb(11, 212, 11);
            font-weight: 600;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .status-paid {
            background: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-complete {
            background: #cce5ff;
            color: #004085;
        }
        
        .payment-method {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .payment-khalti {
            background: #5C2D91;
            color: white;
        }
        
        .payment-cod {
            background: #6c757d;
            color: white;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: linear-gradient(90deg, #FF758F, #C9184A);
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(90deg, #C9184A, #FF758F);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .success-container {
                padding: 20px;
                margin: 10px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
            
            .detail-row {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1 class="success-title">Order Placed Successfully!</h1>
        
        <p class="success-message">
            Thank you for your order! We've received your order and will process it shortly. 
            You will receive an email confirmation with your order details.
        </p>
        
        <div class="order-details">
            <h3>Order Details</h3>
            
            <div class="detail-row">
                <span class="detail-label">Order ID:</span>
                <span class="detail-value">#<?php echo $order['orderId']; ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Customer Name:</span>
                <span class="detail-value"><?php echo htmlspecialchars($order['userName']); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Phone:</span>
                <span class="detail-value"><?php echo htmlspecialchars($order['Contact']); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value"><?php echo htmlspecialchars($order['Email']); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Delivery Address:</span>
                <span class="detail-value"><?php echo htmlspecialchars($order['Address']); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Product:</span>
                <span class="detail-value"><?php echo htmlspecialchars($order['productName']); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Quantity:</span>
                <span class="detail-value"><?php echo $order['orderQuantity']; ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Payment Method:</span>
                <span class="detail-value">
                    <span class="payment-method payment-<?php echo strtolower($order['payment_method']); ?>">
                        <?php echo strtoupper($order['payment_method']); ?>
                    </span>
                </span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Total Amount:</span>
                <span class="detail-value amount">Rs. <?php echo number_format($order['total_amount'], 2); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Order Date:</span>
                <span class="detail-value"><?php echo date('F j, Y g:i A', strtotime($order['date'])); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">
                    <span class="status-badge status-<?php echo $order['status']; ?>">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                </span>
            </div>
            
            <?php if ($order['transaction_id']): ?>
            <div class="detail-row">
                <span class="detail-label">Transaction ID:</span>
                <span class="detail-value"><?php echo htmlspecialchars($order['transaction_id']); ?></span>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="action-buttons">
            <a href="orders.php" class="btn btn-primary">
                <i class="fas fa-list"></i> View My Orders
            </a>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-home"></i> Continue Shopping
            </a>
        </div>
    </div>
</body>
</html> 