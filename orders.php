<?php
    require('./includes/connection.php');
    require('./includes/header.php');

    if (isset($_SESSION["userID"])) {
        $userID = $_SESSION["userID"];

        // Prepare the SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT orders.status, orders.orderID, products.vehicleID, products.vehicleName, products.vehicleDes, products.vehicleImg, products.price
                            FROM orders 
                            JOIN products ON orders.vehicleID = products.vehicleID 
                            WHERE orders.userID = ? ORDER BY orders.fordate DESC");
        $stmt->bind_param('i', $userID);

        // Execute the statement
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        // Check if there are any orders for the user
        if ($result->num_rows > 0) {
            echo "<table style='border-collapse: collapse; width: 100%;'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th style='border: none; padding: 8px; text-align: center;'>Vehicle Image</th>";
            echo "<th style='border: none; padding: 8px; text-align: center; colspan: 3'>Vehicle Details</th>";
            echo "<th style='border: none; padding: 8px; text-align: center;'>Price</th>";
            echo "<th style='border: none; padding: 8px; text-align: center;'>Status</th>";
            echo "<th style='border: none; padding: 8px; text-align: center;'>Actions</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            // Fetch all orders and display them in the table
            while ($order = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td style='border: none; text-align: center; padding: 8px;'><img src='" . htmlspecialchars($order['vehicleImg']) . "' alt='" . htmlspecialchars($order['vehicleName']) . "' style='height: 100px; width: 100px;'></td>";
                echo "<td style='border: none; text-align: center; padding: 8px; colspan: 3'>" . htmlspecialchars($order['vehicleDes']) . "</td>";
                echo "<td style='border: none; text-align: center; padding: 8px;'>" . htmlspecialchars($order['price']) . "</td>";
                echo "<td style='border: none; text-align: center; padding: 8px;'>" . htmlspecialchars($order['status']) . "</td>";
                if ($order['status'] == 'complete') {
                    echo "<td style='border: none; text-align: center; padding: 8px;'>" . "Ready to pick" . "</td>";
                }else if ($order['status'] == 'rejected'){
                    echo "<td style='border: none; text-align: center; padding: 8px;'>" . "Try renting other vehicle" . "</td>";
                }else{
                    echo "<td style='border: none; text-align: center; padding: 8px;'><button onclick='deleteOrder(" . htmlspecialchars($order['orderID']) . ")'>Cancel Order</button></td>";
                }
                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<center><h1>Looks like you haven't rented anything yet.....</h1></center>";
        }

        // Close the statement and connection
        $stmt->close();
        $conn->close();
    } else {
        echo "User is not logged in.";
    }
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    function deleteOrder(orderID) {
        if(confirm('Are you sure you want to cancel this order: ')) {
            $.ajax({
                type: "POST",
                url: "./includes/delete_order.php",
                data: { orderID: orderID },
                success: function(response) {
                    if(response === "success") {
                        alert("Order deleted successfully");
                        location.reload();
                    } else {
                        alert("Failed to delete order");
                    }
                }
            });
        }
    }
</script>
