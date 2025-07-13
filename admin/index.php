<?php
session_start();
require('../includes/connection.php');

if (!isset($_SESSION['admUsername']) || !isset($_SESSION['adminID'])) {
    header("Location: ../login.php");
    exit();
}

// Get today's orders
$today = date('Y-m-d');
$today_orders_query = "
    SELECT 
        orders.orderId,
        orders.status,
        orders.date,
        orders.orderQuantity,
        users.userName,
        users.Contact,
        product.productName,
        product.productId,
        product.productPrice,
        product.productImage
    FROM orders 
    JOIN users ON orders.userId = users.userId 
    JOIN product ON orders.productId = product.productId 
    WHERE DATE(orders.date) = '$today' AND orders.status = 'pending'
    ORDER BY orders.date DESC
";

$today_orders = $conn->query($today_orders_query);

// Get quick stats
$total_orders_query = "SELECT COUNT(*) as total FROM orders WHERE DATE(date) = '$today'";
$total_orders_result = $conn->query($total_orders_query);
$total_orders = $total_orders_result->fetch_assoc()['total'];

$pending_orders_query = "SELECT COUNT(*) as pending FROM orders WHERE DATE(date) = '$today' AND status = 'pending'";
$pending_orders_result = $conn->query($pending_orders_query);
$pending_orders = $pending_orders_result->fetch_assoc()['pending'];

$total_revenue_query = "SELECT SUM(productPrice * orderQuantity) as revenue FROM orders o JOIN product p ON o.productId = p.productId WHERE DATE(o.date) = '$today' AND o.status = 'completed'";
$total_revenue_result = $conn->query($total_revenue_query);
$total_revenue = $total_revenue_result->fetch_assoc()['revenue'] ?? 0;

$total_products_query = "SELECT COUNT(*) as products FROM product";
$total_products_result = $conn->query($total_products_query);
$total_products = $total_products_result->fetch_assoc()['products'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Yarn-Joy</title>
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

        /* Orders Section */
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

        /* Table */
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

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
            font-size: 14px;
        }

        .btn-accept {
            background: #28a745;
            color: white;
        }

        .btn-accept:hover {
            background: #218838;
        }

        .btn-reject {
            background: #dc3545;
            color: white;
        }

        .btn-reject:hover {
            background: #c82333;
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

            .action-buttons {
                flex-direction: column;
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
            <a href="change_password.php"><i class="fas fa-key"></i> Change Password</a>
            <a href="includes/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Navigation -->
    <div class="nav">
        <ul>
            <li><a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> All Orders</a></li>
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
                <p>Total Orders Today</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock"></i>
                <h3><?php echo $pending_orders; ?></h3>
                <p>Pending Orders</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-rupee-sign"></i>
                <h3>Rs <?php echo number_format($total_revenue, 2); ?></h3>
                <p>Revenue Today</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-box"></i>
                <h3><?php echo $total_products; ?></h3>
                <p>Total Products</p>
            </div>
        </div>

        <!-- Today's Orders -->
        <div class="orders-section">
            <div class="section-header">
                <h2><i class="fas fa-calendar-day"></i> Today's Pending Orders</h2>
            </div>

            <?php if ($today_orders->num_rows > 0): ?>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Customer</th>
                            <th>Details</th>
                            <th>Total Price</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $today_orders->fetch_assoc()): ?>
                            <tr id="order-<?php echo $order['orderId']; ?>">
                                <td>
                                    <img src="uploads/<?php echo htmlspecialchars($order['productImage']); ?>" 
                                         alt="<?php echo htmlspecialchars($order['productName']); ?>" 
                                         class="product-image">
                                </td>
                                <td>
                                    <div class="customer-info">
                                        <h4><?php echo htmlspecialchars($order['userName']); ?></h4>
                                        <p><?php echo htmlspecialchars($order['Contact']); ?></p>
                                    </div>
                                </td>
                                <td>
                                    <div class="product-info">
                                        <h4><?php echo htmlspecialchars($order['productName']); ?></h4>
                                        <span class="quantity">Qty: <?php echo $order['orderQuantity']; ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="price">Rs <?php echo number_format($order['productPrice'] * $order['orderQuantity'], 2); ?></span>
                                </td>
                                <td>
                                    <span class="date"><?php echo date('M d, Y H:i', strtotime($order['date'])); ?></span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn" onclick="handleOrder('accept', <?php echo $order['orderId']; ?>, <?php echo $order['productId']; ?>)">
                                            <i class="fas fa-check"></i> Accept
                                        </button>
                                        <button class="action-btn" onclick="handleOrder('reject', <?php echo $order['orderId']; ?>, <?php echo $order['productId']; ?>)">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-orders">
                    <i class="fas fa-inbox"></i>
                    <h3>No pending orders today!</h3>
                    <p>All caught up. Check back later for new orders.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        function handleOrder(action, orderID, productID) {
            const button = event.target.closest('.action-btn');
            const originalText = button.innerHTML;
            
            // Show loading state
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            button.disabled = true;

            $.ajax({
                type: "POST",
                url: "./includes/handle_order.php",
                data: { action: action, orderID: orderID },
                success: function(response) {
                    if(response === 'success') {
                        // Remove the order row
                        const row = document.getElementById('order-' + orderID);
                        row.style.opacity = '0';
                        row.style.transition = 'opacity 0.3s';
                        
                        setTimeout(() => {
                            row.remove();
                            // Update stats
                            location.reload();
                        }, 300);
                    } else {
                        alert('Error: ' + response);
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }
                },
                error: function() {
                    alert('An error occurred while processing the order.');
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            });
        }

        // Auto-refresh every 30 seconds
        setInterval(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>

<?php
$conn->close();
?>
