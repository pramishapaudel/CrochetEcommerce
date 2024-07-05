<?php
require('../includes/connection.php');
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
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            flex-wrap: wrap;
            gap: 25px; /* Space between items */
        }
        .product {
            border: 1px solid red;
            height: 320px;
            width: 278px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
        }
        .product img {
            height: 90px;
            width: 100px;
        }
        .product div {
            margin-top: 10px;
        }
        .delete-btn,
        .edit-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: red;
            color: white;
            border: none;
            padding: 5px;
            cursor: pointer;
        }
        .edit-btn {
            top: 40px; /* Adjust position to not overlap with delete button */
            background-color: blue; /* Different color for edit button */
        }
        p {
            font-size: 1em;
        }
    </style>
</head>
<body>
    <?php if(isset($_SESSION['$message'])){
        echo '<script>alert("Update successfully")</script>';
    }
    ?>
    <div class="container">

        <?php
        while ($row = $result->fetch_assoc()) {
            $productLink = "product_page.php?id=" . $row['vehicleID']; // Link to the product's page
            $editLink = "./includes/edit_product.php?id=" . $row['vehicleID']; // Link to the edit product page
        ?>
        <div class="product" data-id="<?php echo $row['vehicleID']; ?>">
            <button class="delete-btn">Delete</button>
            <button class="edit-btn" onclick="location.href='<?php echo $editLink; ?>'">Edit</button>
            <img src="../<?php echo $row['vehicleImg']; ?>" alt="<?php echo $row['vehicleName']; ?>">
            <div>
                <p><?php echo $row['vehicleName']; ?></p>
                <p><?php echo substr($row['vehicleDes'], 0, 50); ?></p>
                <p>Total: <?php echo $row['vehicleQuantity']; ?></p>
                <p>Remaining: <?php echo $row['vehicleLeft']; ?></p>
                <p>Pending: <?php echo $row['vehiclePending']; ?></p>
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
