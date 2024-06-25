<?php
    require('../connection.php');
    require('./includes/header.php');
    $select = 'SELECT * FROM products';

    $result = $conn->query($select);

    // Check if the query was successful
    if ($result) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Products</title>
        <style>
            .container {
                display: flex;
                flex-wrap: wrap;
                gap: 20px; /* Space between items */
            }
            .product {
                border: 1px solid red;
                height: 300px;
                width: 300px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
                position: relative;
            }
            .product img {
                height: 100px;
                width: 100px;
            }
            .product div {
                margin-top: 10px;
            }
            .delete-btn {
                position: absolute;
                top: 10px;
                right: 10px;
                background-color: red;
                color: white;
                border: none;
                padding: 5px;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <?php
            while ($row = $result->fetch_assoc()) {
                $productLink = "product_page.php?id=" . $row['vehicleID']; // Link to the product's page
            ?>
            <div class="product" data-id="<?php echo $row['vehicleID']; ?>">
                <button class="delete-btn">Delete</button>
                    <img src="../<?php echo $row['vehicleImg']; ?>" alt="<?php echo $row['vehicleName']; ?>">
                    <div>
                        <p><?php echo $row['vehicleName']; ?></p>
                        <p><?php echo $row['vehicleDes']; ?></p>
                    </div>
            </div>
            <?php 
            }
            ?>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script>
            $(document).ready(function() {
                $('.delete-btn').click(function() {
                    if(confirm("Delete product?")) {
                        let productDiv = $(this).closest('.product');
                        let vehicleID = productDiv.data('id');
                        $.ajax({
                            type: 'POST',
                            url: './includes/delete_product.php',
                            data: { vehicleID: vehicleID },
                            success: function(response) {
                                if (response.trim() === 'success') {
                                    productDiv.remove();
                                } else {
                                    alert('Error deleting product');
                                }
                            }
                        });
                }
                });
            });
        </script>
    </body>
    </html>
    <?php
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
?>
