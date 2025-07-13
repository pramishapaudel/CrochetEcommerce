<?php
session_start();
require('../includes/connection.php');

if (!isset($_SESSION['admUsername']) || !isset($_SESSION['adminID'])) {
    header("Location: ../login.php");
    exit();
}

// Get all orders with filtering
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$payment_filter = isset($_GET['payment']) ? $_GET['payment'] : '';

$where_conditions = [];
$params = [];
$param_types = '';

if ($status_filter) {
    $where_conditions[] = "orders.status = ?";
    $params[] = $status_filter;
    $param_types .= 's';
}

if ($payment_filter) {
    $where_conditions[] = "orders.payment_method = ?";
    $params[] = $payment_filter;
    $param_types .= 's';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

$sql = "
SELECT 
    orders.orderId,
    orders.date,
    orders.status,
    orders.orderQuantity,
    orders.payment_method,
    orders.transaction_id,
    users.userName AS userName,
    users.Contact AS userContact,
    users.Email AS userEmail,
    product.productName,
    product.productPrice,
    product.productImage,
    (product.productPrice * orders.orderQuantity) as total_amount
FROM 
    orders
JOIN 
    users ON orders.userId = users.userId
JOIN 
    product ON orders.productId = product.productId
$where_clause
ORDER BY 
    orders.date DESC
";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get stats
$total_orders_query = "SELECT COUNT(*) as total FROM orders";
$total_orders_result = $conn->query($total_orders_query);
$total_orders = $total_orders_result->fetch_assoc()['total'];

$total_revenue_query = "SELECT SUM(productPrice * orderQuantity) as revenue FROM orders o JOIN product p ON o.productId = p.productId WHERE o.status = 'completed'";
$total_revenue_result = $conn->query($total_revenue_query);
$total_revenue = $total_revenue_result->fetch_assoc()['revenue'] ?? 0;

$pending_orders_query = "SELECT COUNT(*) as pending FROM orders WHERE status = 'pending'";
$pending_orders_result = $conn->query($pending_orders_query);
$pending_orders = $pending_orders_result->fetch_assoc()['pending'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Orders - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, 'Merriweather', serif;
            background: #f8f8f8;
            color: rgb(82, 5, 5);
        }

        /* Header */
        .header {
            background: #fff;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: #C9184A;
            font-family: 'Merriweather', serif;
            font-size: 2em;
            letter-spacing: 1px;
        }

        .header .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header .user-info span {
            color: #C9184A;
            font-weight: bold;
        }

        .header .user-info a {
            color: #fff;
            background: linear-gradient(90deg, #FF758F, #C9184A);
            text-decoration: none;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.3s;
            border: none;
            margin-left: 5px;
        }

        .header .user-info a:hover {
            background: linear-gradient(90deg, #C9184A, #FF758F);
        }

        /* Navigation */
        .nav {
            background: #fff;
            padding: 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .nav ul {
            list-style: none;
            display: flex;
            gap: 0;
            margin: 0;
            padding: 0;
        }

        .nav a {
            color: rgb(82, 5, 5);
            text-decoration: none;
            padding: 18px 32px;
            display: block;
            font-family: 'Merriweather', serif;
            font-weight: 700;
            font-size: 1.1em;
            border-bottom: 3px solid transparent;
            transition: background 0.3s, color 0.3s, border-bottom 0.3s;
        }

        .nav a.active, .nav a:hover {
            background: linear-gradient(90deg, #FF758F, #C9184A);
            color: #fff;
            border-bottom: 3px solid #C9184A;
        }

        /* Main Content */
        .main-content {
            padding: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            text-align: center;
            border: 2px solid #FF758F;
        }

        .stat-card i {
            font-size: 40px;
            margin-bottom: 15px;
            color: #C9184A;
        }

        .stat-card h3 {
            font-size: 32px;
            margin-bottom: 5px;
            color: #C9184A;
        }

        .stat-card p {
            color: #C9184A;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Filters */
        .filter-section {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            padding: 20px 30px;
            margin-bottom: 30px;
        }

        .filter-section label {
            font-weight: 600;
            color: #C9184A;
            margin-right: 10px;
        }

        .filter-section select, .filter-section input[type="date"] {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #FF758F;
            margin-right: 15px;
            font-size: 1em;
        }

        .filter-section button {
            padding: 8px 18px;
            background: linear-gradient(90deg, #FF758F, #C9184A);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        .filter-section button:hover {
            background: linear-gradient(90deg, #C9184A, #FF758F);
            transform: translateY(-2px);
        }

        /* Orders Table */
        .orders-section {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            overflow: hidden;
        }

        .section-header {
            padding: 20px 30px;
            border-bottom: 1px solid #eee;
            background: #f9f9f9;
        }

        .section-header h2 {
            color: #C9184A;
            font-size: 20px;
            font-family: 'Merriweather', serif;
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }

        .orders-table th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 700;
            color: #C9184A;
            border-bottom: 1px solid #eee;
        }

        .orders-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        .orders-table tr:hover {
            background: #FFF0F5;
        }

        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }

        .customer-info h4 {
            color: #333;
            margin-bottom: 5px;
        }

        .customer-info p {
            color: #666;
            font-size: 14px;
        }

        .product-info h4 {
            color: #333;
            margin-bottom: 5px;
        }

        .quantity {
            background: #e9ecef;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 14px;
            color: #666;
        }

        .price {
            font-weight: 600;
            color: #007bff;
            font-size: 18px;
        }

        .date {
            color: #666;
            font-size: 14px;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .payment-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
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

        .transaction-id {
            font-family: monospace;
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            color: #495057;
        }

        .no-orders {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .no-orders i {
            font-size: 60px;
            margin-bottom: 20px;
            color: #ddd;
        }

        .action-btn {
            padding: 8px 18px;
            background: linear-gradient(90deg, #FF758F, #C9184A);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        .action-btn:hover {
            background: linear-gradient(90deg, #C9184A, #FF758F);
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .nav ul {
                flex-direction: column;
            }

            .main-content {
                padding: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .orders-table {
                font-size: 14px;
            }

            .orders-table th,
            .orders-table td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1><i class="fas fa-store"></i> Yarn-Joy Admin</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admUsername']); ?></span>
            <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
            <a href="includes/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Navigation -->
    <div class="nav">
        <ul>
            <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="orders.php" class="active"><i class="fas fa-shopping-cart"></i> All Orders</a></li>
            <li><a href="transactions.php"><i class="fas fa-credit-card"></i> Transactions</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="add_products.php"><i class="fas fa-plus"></i> Add Products</a></li>
            <li><a href="browse.php"><i class="fas fa-eye"></i> Browse Products</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-shopping-bag"></i>
                <h3><?php echo $total_orders; ?></h3>
                <p>Total Orders</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock"></i>
                <h3><?php echo $pending_orders; ?></h3>
                <p>Pending Orders</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-rupee-sign"></i>
                <h3>Rs <?php echo number_format($total_revenue, 2); ?></h3>
                <p>Total Revenue</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-section">
            <h3><i class="fas fa-filter"></i> Filter Orders</h3>
            <form method="GET" class="filter-row">
                <div class="filter-group">
                    <label for="status">Status</label>
                    <select name="status" id="status">
                        <option value="">All Status</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="payment">Payment Method</label>
                    <select name="payment" id="payment">
                        <option value="">All Methods</option>
                        <option value="khalti" <?php echo $payment_filter === 'khalti' ? 'selected' : ''; ?>>Khalti</option>
                        <option value="cod" <?php echo $payment_filter === 'cod' ? 'selected' : ''; ?>>Cash on Delivery</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn-filter">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="orders-section">
            <div class="section-header">
                <h2><i class="fas fa-list"></i> Order Details</h2>
            </div>

            <?php if ($result->num_rows > 0): ?>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Customer</th>
                            <th>Details</th>
                            <th>Total Price</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <img src="uploads/<?php echo htmlspecialchars($order['productImage']); ?>" 
                                         alt="<?php echo htmlspecialchars($order['productName']); ?>" 
                                         class="product-image">
                                </td>
                                <td>
                                    <div class="customer-info">
                                        <h4><?php echo htmlspecialchars($order['userName']); ?></h4>
                                        <p><?php echo htmlspecialchars($order['userContact']); ?></p>
                                        <p><?php echo htmlspecialchars($order['userEmail']); ?></p>
                                    </div>
                                </td>
                                <td>
                                    <div class="product-info">
                                        <h4><?php echo htmlspecialchars($order['productName']); ?></h4>
                                        <span class="quantity">Qty: <?php echo $order['orderQuantity']; ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="price">Rs <?php echo number_format($order['total_amount'], 2); ?></span>
                                </td>
                                <td>
                                    <div>
                                        <span class="payment-badge payment-<?php echo $order['payment_method']; ?>">
                                            <?php echo strtoupper($order['payment_method']); ?>
                                        </span>
                                        <?php if ($order['transaction_id']): ?>
                                            <div class="transaction-id"><?php echo $order['transaction_id']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $order['status']; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="date"><?php echo date('M d, Y H:i', strtotime($order['date'])); ?></span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-orders">
                    <i class="fas fa-inbox"></i>
                    <h3>No orders found!</h3>
                    <p>No orders match your current filters.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
