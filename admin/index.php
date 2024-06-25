<?php
    require('../includes/connection.php');
    require('./includes/header.php');

    // Ensure the admin is logged in
    if (!isset($_SESSION['adminID'])) {
        header("Location: ../login.php");
        exit();
    }

    // Fetch today's orders
    $today = date('Y-m-d');
    $query = "SELECT orders.orderID, users.name as userName, products.vehicleName, products.price, orders.date 
              FROM orders 
              JOIN users ON orders.userID = users.userID 
              JOIN products ON orders.vehicleID = products.vehicleID 
              WHERE DATE(orders.date) = '$today'
              ORDER BY orders.date DESC";

    $result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <h2>Today's Orders</h2>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>User Name</th>
                <th>Vehicle Name</th>
                <th>Price</th>
                <th>Order Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['orderID'] . "</td>";
                        echo "<td>" . $row['userName'] . "</td>";
                        echo "<td>" . $row['vehicleName'] . "</td>";
                        echo "<td>" . $row['price'] . "</td>";
                        echo "<td>" . $row['date'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No orders today.</td></tr>";
                }
            ?>
        </tbody>
    </table>
</body>
</html>