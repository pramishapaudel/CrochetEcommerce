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
    $query = "SELECT orders.orderId,orders.status ,orders.date, users.userName as userName, product.productName,product.productId, product.productPrice, orders.date
              FROM orders 
              JOIN users ON orders.userId = users.userId 
              JOIN product ON orders.productId = product.productId 
              WHERE DATE(orders.date) = '$today' AND orders.status = 'pending'
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        function handleOrder(action, orderID, vehicleID) {
            $.ajax({
                type: "POST",
                url: "./includes/handle_order.php",
                data: { action: action, orderID: orderID, vehicleID: vehicleID},
                success: function(response) {
                    if(response === 'success') {
                        document.getElementById('order-' + orderID).remove();
                    } else {
                        alert(response);
                    }
                },
                error: function() {
                    alert('An error occurred while processing the order.');
                }
            });
        }
    </script>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <h2>Today's Orders</h2>
    <table>
        <thead>
            <tr>
                <th>User Name</th>
                <th>Vehicle Name</th>
                <th>Price</th>
                <th>Order Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                            echo "<tr id='order-" . $row['orderId'] . "'>";
                            echo "<td>" . $row['userName'] . "</td>";
                            echo "<td>" . $row['productName'] . "</td>";
                            echo "<td>" . $row['productPrice'] . "</td>";
                            echo "<td>" . $row['date'] . "</td>";
                            echo "<td>
                                        <button onclick='handleOrder(\"accept\", " . $row['orderId'] . "," . $row['productId'] . ")'>Accept</button>
                                        <button onclick='handleOrder(\"reject\", " . $row['orderId'] . ", " . $row['productId'] . ")'>Reject</button>
                                </td>";
                            echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No orders today.</td></tr>";
                }
            ?>
        </tbody>
    </table>
</body>
</html>