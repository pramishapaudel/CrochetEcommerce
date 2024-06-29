<?php
    require('../includes/connection.php');
    require('./includes/header.php');

    if (isset($_SESSION["adminID"])) {
        // SQL query to join orders, users, and products tables, sorted by order date
        $sql = "
        SELECT 
            orders.orderID,
            orders.date,
            orders.status,
            users.Name AS userName,
            users.Contact AS userContact,
            products.vehicleName,
            products.vehicleDes,
            products.vehicleImg
        FROM 
            orders
        JOIN 
            users ON orders.userID = users.UserID
        JOIN 
            products ON orders.vehicleID = products.vehicleID
        ORDER BY 
            orders.date DESC
        ";

        $result = $conn->query($sql);

        // Check if the query was successful
        if ($result && $result->num_rows > 0) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Orders View</title>
        <style>
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                padding: 10px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }
            th {
                background-color: #f2f2f2;
            }
            img {
                max-width: 100px;
            }
        </style>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="../assets/js/script.js"></script>
        </head>
        <body>
        <h1>Orders</h1>
        <table>
            <thead>
                <tr>
                    <th>Order Date</th>
                    <th>User Name</th>
                    <th>User Contact</th>
                    <th>Vehicle Name</th>
                    <th>Vehicle Description</th>
                    <th>Vehicle Image</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result->fetch_assoc()) {
                ?>
                <tr>
                    <td><?php echo $row['date']; ?></td>
                    <td><?php echo $row['userName']; ?></td>
                    <td><?php echo $row['userContact']; ?></td>
                    <td><?php echo $row['vehicleName']; ?></td>
                    <td><?php echo $row['vehicleDes']; ?></td>
                    <td><img src="../<?php echo $row['vehicleImg']; ?>" alt="<?php echo $row['vehicleName']; ?>"></td>
                    <td><?php echo $row['status']; ?></td>
                    <?php if ($row['status'] == 'complete') { ?>
                            <td><button onclick='updateOrderStatus("pick", <?php echo $row['orderID']; ?>)'>Picked</button></td>
                        <?php }if ($row['status'] == 'picked') { ?>
                            <td><button onclick='updateOrderStatus("return", <?php echo $row['orderID']; ?>)'>Return</button></td>
                    <?php } ?>
                </tr>
                <?php 
                }
                ?>
            </tbody>
        </table>
        </body>
        </html>
        <?php
        } else {
        echo "No orders found or error: " . $conn->error;
        }

        $conn->close();
    } else {
        echo "User is not logged in.";
    }
?>

