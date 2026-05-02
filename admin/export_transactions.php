<?php
session_start();
require('../includes/connection.php');

if (!isset($_SESSION['admUsername']) || !isset($_SESSION['adminID'])) {
    header("Location: ../login.php");
    exit();
}

// Get date range filters
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');
$payment_method = $_GET['payment_method'] ?? '';
$report_type = $_GET['report_type'] ?? 'transactions';

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

// Set headers for CSV download
$filename = ($report_type === 'daily_summary' ? 'daily_summary_' : 'transactions_') . date('Y-m-d') . ".csv";
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Create file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for proper encoding
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

if ($report_type === 'daily_summary') {
    $summary_sql = "
        SELECT
            DATE(o.date) AS report_date,
            COUNT(*) AS total_transactions,
            SUM(p.productPrice * o.orderQuantity) AS total_revenue,
            SUM(CASE WHEN o.payment_method = 'khalti' THEN p.productPrice * o.orderQuantity ELSE 0 END) AS khalti_revenue,
            SUM(CASE WHEN o.payment_method = 'cod' THEN p.productPrice * o.orderQuantity ELSE 0 END) AS cod_revenue
        FROM orders o
        JOIN product p ON o.productId = p.productId
        WHERE $where_clause
        GROUP BY DATE(o.date)
        ORDER BY report_date DESC
    ";

    $summary_stmt = $conn->prepare($summary_sql);
    if (!empty($params)) {
        $summary_stmt->bind_param($param_types, ...$params);
    }
    $summary_stmt->execute();
    $summary_result = $summary_stmt->get_result();

    fputcsv($output, array(
        'Date',
        'Total Transactions',
        'Total Revenue',
        'Khalti Revenue',
        'COD Revenue'
    ));

    while ($row = $summary_result->fetch_assoc()) {
        fputcsv($output, array(
            $row['report_date'],
            $row['total_transactions'],
            $row['total_revenue'],
            $row['khalti_revenue'],
            $row['cod_revenue']
        ));
    }
    $summary_stmt->close();
} else {
    // Add CSV headers
    fputcsv($output, array(
        'Order ID',
        'Date & Time',
        'Customer Name',
        'Customer Contact',
        'Customer Email',
        'Product Name',
        'Quantity',
        'Unit Price',
        'Total Amount',
        'Payment Method',
        'Transaction ID'
    ));

    // Add data rows
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, array(
            $row['orderId'],
            date('Y-m-d H:i:s', strtotime($row['date'])),
            $row['userName'],
            $row['Contact'],
            $row['Email'],
            $row['productName'],
            $row['orderQuantity'],
            $row['productPrice'],
            $row['total_amount'],
            strtoupper($row['payment_method']),
            $row['transaction_id'] ?: '-'
        ));
    }
}

// Close the file pointer
fclose($output);

$conn->close();
exit();
?> 