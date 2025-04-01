<?php
require('../includes/connection.php');
require('./includes/header.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <style>
        form {
            max-width: 600px;
            margin: auto;
            padding: 1em;
            background: #f9f9f9;
            border-radius: 5px;
        }
        input, textarea {
            margin-bottom: 1em;
            padding: 0.5em;
            font-size: 1em;
            width: 100%;
        }
        input[type="submit"] {
            padding: 0.7em;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <center><h1>Add New Product</h1></center>
    <form id="productForm" method="post" enctype="multipart/form-data">
        <label for="productName">Product Name:</label>
        <input type="text" id="productName" name="productName" required>

        <label for="productDes">Product Description:</label>
        <textarea id="productDes" name="productDes" rows="4" required></textarea>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" required>

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" required>

        <label for="category">Category:</label>
        <select id="category" name="category" required>
            <option value="keychains">Keychains</option>
            <option value="bags">Bags</option>
        </select>

        <label for="productImg">Product Image:</label>
        <input type="file" id="productImg" name="productImg" accept="image/*" required>

        <input type="submit" value="Add Product">
    </form>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#productForm').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: './includes/add_product_process.php',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        document.getElementById('productForm').reset();
                        alert(response);  
                    },
                    error: function() {
                        alert('Error adding product.');
                    }
                });
            });
        });
    </script>
</body>
</html>
