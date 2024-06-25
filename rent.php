<?php
    require('./header.php');
    require('./connection.php');

    // Check if 'id' parameter is set in the URL
    if (isset($_GET['id'])) {
        $vID = $_GET['id'];

        // Prepare a SQL statement to prevent SQL injection
        $stmt = $conn->prepare('SELECT * FROM products WHERE vehicleID = ?');
        $stmt->bind_param('i', $vID);
        $stmt->execute();
        $res = $stmt->get_result();

        // Check if the product exists
        if ($res->num_rows > 0) {
            $product = $res->fetch_assoc();
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title><?php echo htmlspecialchars($product['vehicleName']); ?></title>
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
                <script>
                    function confirmOrder() {
                        if(confirm('Confirm Rent Order?')){
                            var vehicleID = "<?php echo htmlspecialchars($vID); ?>";
                            var userID = "<?php echo htmlspecialchars($_SESSION['userID']); ?>";

                            $.ajax({
                                type: "POST",
                                url: "confirm_order.php",
                                data: {
                                    vehicleID: vehicleID,
                                    userID: userID
                                },
                                success: function(response) {
                                    alert(response);
                                },
                                error: function(xhr, status, error) {
                                    alert("An error occurred: " + xhr.responseText);
                                }
                            });
                        }
                    }
                </script>
            </head>
            <body>
                <h1><?php echo htmlspecialchars($product['vehicleName']); ?></h1>
                <img src="<?php echo htmlspecialchars($product['vehicleImg']); ?>" alt="<?php echo htmlspecialchars($product['vehicleName']); ?>" style="height: 200px; width: 200px;">
                <p><?php echo htmlspecialchars($product['vehicleDes']); ?></p>
                <p><?php echo htmlspecialchars($product['price']); ?></p>
                <?php if (isset($_SESSION['Username'])){ ?>
                    <button onclick="confirmOrder()">Rent!</button>
                <?php } else { ?>
                    <button type="submit" onclick="alert('Login First!')">Rent!</button></a>
                <?php } ?>
                <!-- Add more product details here -->
            </body>
            </html>
            <?php
        } else {
            echo "Product not found.";
        }

        // Close the statement and the connection
        $stmt->close();
        $conn->close();
    } else {
        echo "No product ID provided.";
    }
?>

    