<?php
    require('./connection.php');
    require('./header.php');

    if (isset($_SESSION["userID"])) {
        $userID = $_SESSION["userID"];

        // Prepare the SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT orders.orderID, products.vehicleID, products.vehicleName, products.vehicleDes, products.vehicleImg
                            FROM orders 
                            JOIN products ON orders.vehicleID = products.vehicleID 
                            WHERE orders.userID = ?");
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
            echo "<th style='border: none; padding: 8px; text-align: left; colspan: 4'>Vehicle</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            // Fetch all orders and display them in the table
            while ($order = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td style='border: none; padding: 8px;'><img src='" . htmlspecialchars($order['vehicleImg']) . "' alt='" . htmlspecialchars($order['vehicleName']) . "' style='height: 100px; width: 100px;'></td>";
                echo "<td style='border: none; padding: 8px; colspan: 3'>" . htmlspecialchars($order['vehicleDes']) . "</td>";
                echo "<td style='border: none; padding: 8px;'><button onclick='deleteOrder(" . htmlspecialchars($order['orderID']) . ")'>Delete</button></td>";
                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
        } else {
            echo "No orders found for this user.";
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
        $.ajax({
            type: "POST",
            url: "delete_order.php",
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
</script>
