<?php
session_start();
require('../includes/connection.php');

if (!isset($_SESSION['admUsername']) || !isset($_SESSION['adminID'])) {
    header("Location: ../login.php");
    exit();
}

// Get date range filters
$start_date = $_GET['start_date'] ?? date('Y-m-01'); // First day of current month
$end_date = $_GET['end_date'] ?? date('Y-m-d'); // Today
$payment_method = $_GET['payment_method'] ?? '';

// Build the query with filters
$where_conditions = ["o.status = 'paid'"];
$params = [];
$param_types = "";

if ($payment_method) {
    $where_conditions[] = "o.payment_method = ?";
    $params[] = $payment_method;
    $param_types .= "s";
}

$where_conditions[] = "DATE(o.date) BETWEEN ? AND ?";
$params[] = $start_date;
$params[] = $end_date;
$param_types .= "ss";

$where_clause = implode(" AND ", $where_conditions);

$sql = "
    SELECT 
        o.orderId,
        o.date,
        o.payment_method,
        o.transaction_id,
        o.orderQuantity,
        u.userName,
        u.Contact,
        u.Email,
        p.productName,
        p.productPrice,
        (p.productPrice * o.orderQuantity) as total_amount
    FROM orders o 
    JOIN users u ON o.userId = u.userId 
    JOIN product p ON o.productId = p.productId 
    WHERE $where_clause
    ORDER BY o.date DESC
";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Calculate summary statistics
$summary_sql = "
    SELECT 
        COUNT(*) as total_transactions,
        SUM(p.productPrice * o.orderQuantity) as total_revenue,
        COUNT(CASE WHEN o.payment_method = 'khalti' THEN 1 END) as khalti_transactions,
        SUM(CASE WHEN o.payment_method = 'khalti' THEN p.productPrice * o.orderQuantity ELSE 0 END) as khalti_revenue,
        COUNT(CASE WHEN o.payment_method = 'cod' THEN 1 END) as cod_transactions,
        SUM(CASE WHEN o.payment_method = 'cod' THEN p.productPrice * o.orderQuantity ELSE 0 END) as cod_revenue
    FROM orders o 
    JOIN product p ON o.productId = p.productId 
    WHERE $where_clause
";

$summary_stmt = $conn->prepare($summary_sql);
if (!empty($params)) {
    $summary_stmt->bind_param($param_types, ...$params);
}
$summary_stmt->execute();
$summary_result = $summary_stmt->get_result();
$summary_data = $summary_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Arial, 'Merriweather', serif;
            background: #f8f8f8;
            color: rgb(82, 5, 5);
        }
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
        .main-content {
            padding: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }
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
        .transactions-section {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            overflow: hidden;
            margin-bottom: 30px;
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
        .transactions-table {
            width: 100%;
            border-collapse: collapse;
        }
        .transactions-table th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 700;
            color: #C9184A;
            border-bottom: 1px solid #eee;
        }
        .transactions-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        .transactions-table tr:hover {
            background: #FFF0F5;
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
            .transactions-table {
                font-size: 14px;
            }
            .transactions-table th,
            .transactions-table td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Yarn-Joy Admin</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admUsername']); ?></span>
            <a href="profile.php">Profile</a>
            <a href="change_password.php">Change Password</a>
            <a href="includes/logout.php">Logout</a>
        </div>
    </div>
    <div class="nav">
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="orders.php">All Orders</a></li>
            <li><a href="transactions.php" class="active">Transactions</a></li>
            <li><a href="users.php">Users</a></li>
            <li><a href="add_products.php">Add Products</a></li>
            <li><a href="browse.php">Browse Products</a></li>
        </ul>
    </div>
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <h1>Transaction Analytics</h1>
            <p>Detailed transaction tracking and revenue analysis</p>
        </div>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-title">Total Revenue</div>
                <div class="stat-value">Rs <?php echo number_format($summary_data['total_revenue'] ?? 0, 2); ?></div>
                <div class="stat-subtitle"><?php echo $summary_data['total_transactions'] ?? 0; ?> transactions</div>
            </div>
            
            <div class="stat-card khalti">
                <div class="stat-title">Khalti Revenue</div>
                <div class="stat-value">Rs <?php echo number_format($summary_data['khalti_revenue'] ?? 0, 2); ?></div>
                <div class="stat-subtitle"><?php echo $summary_data['khalti_transactions'] ?? 0; ?> Khalti transactions</div>
            </div>
            
            <div class="stat-card cod">
                <div class="stat-title">COD Revenue</div>
                <div class="stat-value">Rs <?php echo number_format($summary_data['cod_revenue'] ?? 0, 2); ?></div>
                <div class="stat-subtitle"><?php echo $summary_data['cod_transactions'] ?? 0; ?> COD transactions</div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filters">
            <form method="GET" class="filter-group">
                <div>
                    <label for="start_date">Start Date:</label>
                    <input type="date" name="start_date" id="start_date" value="<?php echo $start_date; ?>">
                </div>
                
                <div>
                    <label for="end_date">End Date:</label>
                    <input type="date" name="end_date" id="end_date" value="<?php echo $end_date; ?>">
                </div>
                
                <div>
                    <label for="payment_method">Payment Method:</label>
                    <select name="payment_method" id="payment_method">
                        <option value="">All Methods</option>
                        <option value="khalti" <?php echo ($payment_method == 'khalti') ? 'selected' : ''; ?>>Khalti</option>
                        <option value="cod" <?php echo ($payment_method == 'cod') ? 'selected' : ''; ?>>Cash on Delivery</option>
                    </select>
                </div>
                
                <button type="submit" class="filter-btn">Apply Filters</button>
                
                <a href="transactions.php" class="export-btn">Reset</a>
                
                <a href="export_transactions.php?<?php echo http_build_query($_GET); ?>" class="export-btn">Export CSV</a>
            </form>
        </div>
        
        <!-- Transactions Table -->
        <div class="table-container">
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date & Time</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Transaction ID</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $zebra = false; while ($row = $result->fetch_assoc()): $zebra = !$zebra; ?>
                            <tr<?php if($zebra) echo ' style="background:#faf6fa;"'; ?>>
                                <td style="text-align:center;"><strong>#<?php echo $row['orderId']; ?></strong></td>
                                <td style="text-align:center;"><?php echo date('M d, Y H:i', strtotime($row['date'])); ?></td>
                                <td>
                                    <div class="customer-info">
                                        <div class="customer-name" style="font-weight:600; color:#C9184A;"><?php echo htmlspecialchars($row['userName']); ?></div>
                                        <div class="customer-contact" style="font-size:12px; color:#888;">📞 <?php echo htmlspecialchars($row['Contact']); ?></div>
                                        <div class="customer-contact" style="font-size:12px; color:#888;">✉️ <?php echo htmlspecialchars($row['Email']); ?></div>
                                    </div>
                                </td>
                                <td style="text-align:center;"><?php echo htmlspecialchars($row['productName']); ?></td>
                                <td style="text-align:center;"><?php echo $row['orderQuantity']; ?></td>
                                <td class="amount" style="text-align:center; font-weight:600; color:#009900;">Rs <?php echo number_format($row['total_amount'], 2); ?></td>
                                <td style="text-align:center;">
                                    <span class="payment-method payment-<?php echo strtolower($row['payment_method']); ?>" style="display:inline-block; padding:4px 12px; border-radius:12px; background:<?php echo strtolower($row['payment_method'])=='khalti'?'#5C2D91':'#C9184A'; ?>; color:#fff; font-size:13px; font-weight:600;">
                                        <?php echo strtoupper($row['payment_method']); ?>
                                    </span>
                                </td>
                                <td style="text-align:center;">
                                    <?php if ($row['transaction_id']): ?>
                                        <span class="transaction-id" style="font-family:monospace; background:#f8f9fa; padding:2px 6px; border-radius:4px; font-size:13px; color:#495057;"><?php echo htmlspecialchars($row['transaction_id']); ?></span>
                                    <?php else: ?>
                                        <span style="color: #999;">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <!-- Summary Row -->
                        <tr class="summary-row" style="background:#f8f9fa; font-weight:700;">
                            <td colspan="5" style="text-align:right;">Total</td>
                            <td class="amount" style="text-align:center; color:#009900;">Rs <?php echo number_format($summary_data['total_revenue'] ?? 0, 2); ?></td>
                            <td colspan="2"></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="empty-state" style="text-align:center; color:#C9184A;">No transactions found for the selected criteria</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?> 