<?php
session_start();
require('../includes/connection.php');
require('./includes/header.php');

if (!isset($_SESSION['admUsername']) || !isset($_SESSION['adminID'])) {
    header("Location: ../login.php");
    exit();
}

// Get today's date
$today = date('Y-m-d');
$current_month = date('Y-m');

// Get total revenue statistics
$total_revenue_query = "
    SELECT 
        SUM(p.productPrice * o.orderQuantity) as total_revenue,
        COUNT(*) as total_orders
    FROM orders o 
    JOIN product p ON o.productId = p.productId 
    WHERE o.status = 'paid'
";
$total_revenue_result = $conn->query($total_revenue_query);
$total_revenue_data = $total_revenue_result->fetch_assoc();

// Get today's revenue
$today_revenue_query = "
    SELECT 
        SUM(p.productPrice * o.orderQuantity) as today_revenue,
        COUNT(*) as today_orders
    FROM orders o 
    JOIN product p ON o.productId = p.productId 
    WHERE o.status = 'paid' AND DATE(o.date) = '$today'
";
$today_revenue_result = $conn->query($today_revenue_query);
$today_revenue_data = $today_revenue_result->fetch_assoc();

// Get this month's revenue
$month_revenue_query = "
    SELECT 
        SUM(p.productPrice * o.orderQuantity) as month_revenue,
        COUNT(*) as month_orders
    FROM orders o 
    JOIN product p ON o.productId = p.productId 
    WHERE o.status = 'paid' AND DATE_FORMAT(o.date, '%Y-%m') = '$current_month'
";
$month_revenue_result = $conn->query($month_revenue_query);
$month_revenue_data = $month_revenue_result->fetch_assoc();

// Get Khalti payment statistics
$khalti_stats_query = "
    SELECT 
        COUNT(*) as khalti_orders,
        SUM(p.productPrice * o.orderQuantity) as khalti_revenue
    FROM orders o 
    JOIN product p ON o.productId = p.productId 
    WHERE o.payment_method = 'khalti' AND o.status = 'paid'
";
$khalti_stats_result = $conn->query($khalti_stats_query);
$khalti_stats_data = $khalti_stats_result->fetch_assoc();

// Get recent transactions
$recent_transactions_query = "
    SELECT 
        o.orderId,
        o.date,
        o.status,
        o.payment_method,
        o.transaction_id,
        o.orderQuantity,
        u.userName,
        u.Contact,
        p.productName,
        p.productPrice,
        (p.productPrice * o.orderQuantity) as total_amount
    FROM orders o 
    JOIN users u ON o.userId = u.userId 
    JOIN product p ON o.productId = p.productId 
    WHERE o.status = 'paid'
    ORDER BY o.date DESC 
    LIMIT 10
";
$recent_transactions_result = $conn->query($recent_transactions_query);

// Get pending orders
$pending_orders_query = "
    SELECT 
        o.orderId,
        o.date,
        o.status,
        o.payment_method,
        o.orderQuantity,
        u.userName,
        u.Contact,
        p.productName,
        p.productPrice,
        (p.productPrice * o.orderQuantity) as total_amount
    FROM orders o 
    JOIN users u ON o.userId = u.userId 
    JOIN product p ON o.productId = p.productId 
    WHERE o.status = 'pending'
    ORDER BY o.date DESC
";
$pending_orders_result = $conn->query($pending_orders_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Transaction Analytics</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: rgba(255, 255, 255, 0.9);
            color: rgb(82, 5, 5);
            margin: 0;
            padding: 20px;
        }
        
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: linear-gradient(90deg, #FF758F, #C9184A);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #FF758F;
        }
        
        .stat-card.khalti {
            border-left-color: #5C2D91;
        }
        
        .stat-card.today {
            border-left-color: rgb(11, 212, 11);
        }
        
        .stat-card.month {
            border-left-color: #C9184A;
        }
        
        .stat-card.total {
            border-left-color: #FF758F;
        }
        
        .stat-title {
            font-size: 0.9rem;
            color: rgb(82, 5, 5);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: rgb(82, 5, 5);
            margin-bottom: 5px;
        }
        
        .stat-subtitle {
            font-size: 0.8rem;
            color: #666;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }
        
        .section {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .section-header {
            background: linear-gradient(90deg, #FF758F, #C9184A);
            color: white;
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .section-header h2 {
            color: white;
            font-size: 1.3rem;
            font-weight: 600;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        
        th {
            background: linear-gradient(90deg, #FF758F, #C9184A);
            font-weight: 600;
            color: white;
            font-size: 0.9rem;
        }
        
        td {
            font-size: 0.9rem;
            color: rgb(82, 5, 5);
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-paid {
            background: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .payment-method {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .payment-khalti {
            background: #5C2D91;
            color: white;
        }
        
        .payment-cod {
            background: #6c757d;
            color: white;
        }
        
        .transaction-id {
            font-family: monospace;
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        
        .amount {
            font-weight: 600;
            color: rgb(11, 212, 11);
        }
        
        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            margin: 2px;
            transition: all 0.3s ease;
        }
        
        .accept-btn {
            background: linear-gradient(90deg, #FF758F, #C9184A);
            color: white;
        }
        
        .accept-btn:hover {
            background: linear-gradient(90deg, #C9184A, #FF758F);
            transform: translateY(-2px);
        }
        
        .reject-btn {
            background: #dc3545;
            color: white;
        }
        
        .reject-btn:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        
        .empty-state {
            padding: 40px;
            text-align: center;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <div class="header">
            <h1>Admin Dashboard</h1>
            <p>Transaction Analytics & Order Management</p>
        </div>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card today">
                <div class="stat-title">Today's Revenue</div>
                <div class="stat-value">Rs <?php echo number_format($today_revenue_data['today_revenue'] ?? 0, 2); ?></div>
                <div class="stat-subtitle"><?php echo $today_revenue_data['today_orders'] ?? 0; ?> orders today</div>
            </div>
            
            <div class="stat-card month">
                <div class="stat-title">This Month's Revenue</div>
                <div class="stat-value">Rs <?php echo number_format($month_revenue_data['month_revenue'] ?? 0, 2); ?></div>
                <div class="stat-subtitle"><?php echo $month_revenue_data['month_orders'] ?? 0; ?> orders this month</div>
            </div>
            
            <div class="stat-card khalti">
                <div class="stat-title">Khalti Payments</div>
                <div class="stat-value">Rs <?php echo number_format($khalti_stats_data['khalti_revenue'] ?? 0, 2); ?></div>
                <div class="stat-subtitle"><?php echo $khalti_stats_data['khalti_orders'] ?? 0; ?> Khalti orders</div>
            </div>
            
            <div class="stat-card total">
                <div class="stat-title">Total Revenue</div>
                <div class="stat-value">Rs <?php echo number_format($total_revenue_data['total_revenue'] ?? 0, 2); ?></div>
                <div class="stat-subtitle"><?php echo $total_revenue_data['total_orders'] ?? 0; ?> total orders</div>
            </div>
        </div>
        
        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Recent Transactions -->
            <div class="section">
                <div class="section-header">
                    <h2>Recent Transactions</h2>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Transaction ID</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent_transactions_result->num_rows > 0): ?>
                                <?php while ($row = $recent_transactions_result->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $row['orderId']; ?></td>
                                        <td>
                                            <div><?php echo htmlspecialchars($row['userName']); ?></div>
                                            <small><?php echo htmlspecialchars($row['Contact']); ?></small>
                                        </td>
                                        <td>
                                            <div><?php echo htmlspecialchars($row['productName']); ?></div>
                                            <small>Qty: <?php echo $row['orderQuantity']; ?></small>
                                        </td>
                                        <td class="amount">Rs <?php echo number_format($row['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="payment-method payment-<?php echo strtolower($row['payment_method']); ?>">
                                                <?php echo strtoupper($row['payment_method']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($row['transaction_id']): ?>
                                                <span class="transaction-id"><?php echo htmlspecialchars($row['transaction_id']); ?></span>
                                            <?php else: ?>
                                                <span style="color: #999;">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('M d, Y H:i', strtotime($row['date'])); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $row['status']; ?>">
                                                <?php echo ucfirst($row['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="empty-state">No transactions found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Pending Orders -->
            <div class="section">
                <div class="section-header">
                    <h2>Pending Orders</h2>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th>Amount</th>
                                <th>Payment</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($pending_orders_result->num_rows > 0): ?>
                                <?php while ($row = $pending_orders_result->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $row['orderId']; ?></td>
                                        <td>
                                            <div><?php echo htmlspecialchars($row['userName']); ?></div>
                                            <small><?php echo htmlspecialchars($row['Contact']); ?></small>
                                        </td>
                                        <td>
                                            <div><?php echo htmlspecialchars($row['productName']); ?></div>
                                            <small>Qty: <?php echo $row['orderQuantity']; ?></small>
                                        </td>
                                        <td class="amount">Rs <?php echo number_format($row['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="payment-method payment-<?php echo strtolower($row['payment_method']); ?>">
                                                <?php echo strtoupper($row['payment_method']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="action-btn accept-btn" onclick="handleOrder('accept', <?php echo $row['orderId']; ?>)">Accept</button>
                                            <button class="action-btn reject-btn" onclick="handleOrder('reject', <?php echo $row['orderId']; ?>)">Reject</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="empty-state">No pending orders</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function handleOrder(action, orderID) {
            if (confirm('Are you sure you want to ' + action + ' this order?')) {
                $.ajax({
                    type: "POST",
                    url: "./includes/handle_order.php",
                    data: { action: action, orderID: orderID },
                    success: function(response) {
                        if(response === 'success') {
                            location.reload();
                        } else {
                            alert(response);
                        }
                    },
                    error: function() {
                        alert('An error occurred while processing the order.');
                    }
                });
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?> 