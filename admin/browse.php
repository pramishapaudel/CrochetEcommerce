<?php
require('../includes/connection.php');
require('./includes/header.php');

$select = 'SELECT * FROM product ORDER BY productId DESC'; // Ensure latest products are retrieved
$result = $conn->query($select);

if ($result && $result->num_rows > 0) {
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
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
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
            padding: 10px;
        }
        .product img {
            height: 90px;
            width: 100px;
            object-fit: cover;
        }
        .product div {
            margin-top: 10px;
        }
        .delete-btn, .edit-btn {
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
            top: 40px;
            background-color: blue;
        }
        p {
            font-size: 1em;
        }
    </style>
</head>
<body>

    <?php 
    if (isset($_SESSION['message'])) {
        echo '<script>alert("' . $_SESSION['message'] . '")</script>';
        unset($_SESSION['message']); // Clear message after displaying
    }
    ?>

    <div class="container">
        <?php
        while ($row = $result->fetch_assoc()) {
            $productLink = "product_page.php?id=" . $row['productId']; 
            $editLink = "./includes/edit_product.php?id=" . $row['productId'];
        ?>
        <div class="product" data-id="<?php echo $row['productId']; ?>">
            <button class="delete-btn">Delete</button>
            <button class="edit-btn" onclick="location.href='<?php echo $editLink; ?>'">Edit</button>
            <img src="<?php echo '../admin/uploads/' . htmlspecialchars($row['productImage']); ?>" alt="<?php echo htmlspecialchars($row['productName']); ?>">
            <div>
                <p><?php echo htmlspecialchars($row['productName']); ?></p>
                <p><?php echo substr(htmlspecialchars($row['productDetails']), 0, 50); ?></p>
                <p>Total: <?php echo (int) $row['productQuantity']; ?></p>
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
                if (confirm("Delete product?")) {
                    let productDiv = $(this).closest('.product');
                    let productID = productDiv.data('id');
                    
                    $.ajax({
                        type: 'POST',
                        url: './includes/delete_product.php',
                        data: { productID: productID },
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
    echo "No products available"; // Improved error handling
}

$conn->close();
?>
