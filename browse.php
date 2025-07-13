<?php include 'includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Products</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        nav {
            background-color: white;
            padding: 10px 0;
            text-align: center;
        }
        nav ul {
            display: flex;
            justify-content: center;
            align-items: center;
            list-style: none;
            margin: 0;
            padding: 0;
            flex-wrap: wrap;
        }
        nav ul li {
            margin: 0 15px;
            position: relative;
        }
        nav ul li a,
        .dropbtn {
            text-decoration: none;
            color: maroon;
            font-weight: bold;
            font-size: 1.2em;
        }
        .dropbtn {
            background: linear-gradient(to right, #FF758F, #C9184A);
            border: none;
            padding: 5px 12px;
            border-radius: 10px;
            color: white;
            cursor: pointer;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            top: 30px;
            left: 0;
            background-color: #f9f9f9;
            min-width: 120px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 1;
            flex-direction: column;
        }
        .dropdown:hover .dropdown-content {
            display: flex;
        }
        .cart-badge {
            background: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            position: relative;
            top: -8px;
            left: -5px;
        }
        .container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            padding: 40px;
            box-sizing: border-box;
            max-width: 1200px;
            margin: auto;
        }
        .product {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 16px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .image-wrapper {
            width: 100%;
            height: 220px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .image-wrapper img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            border-radius: 8px;
        }
        .product p {
            margin: 6px 0;
        }
        .price {
            color: #28a745;
            font-weight: bold;
            font-size: 1.2em;
        }
        input[type="number"] {
            width: 60px;
            padding: 6px;
            margin-bottom: 10px;
            border: 1px solid #aaa;
            border-radius: 5px;
            text-align: center;
        }
        .action-btn {
            padding: 10px 15px;
            margin-bottom: 6px;
            background: linear-gradient(90deg, #FF758F, #C9184A);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s ease;
            width: 100%;
        }
        .action-btn:hover {
            background: linear-gradient(90deg, #C9184A, #FF758F);
            transform: translateY(-2px);
        }
        footer {
            background-color: #f8f8f8;
            padding: 20px;
            text-align: center;
            margin-top: 40px;
        }
        .footer-links a {
            margin: 0 10px;
            text-decoration: none;
            color: #333;
        }
        @media (max-width: 600px) {
            .container {
                padding: 10px;
                gap: 16px;
            }
            .image-wrapper {
                height: 160px;
            }
            nav ul {
                flex-direction: column;
            }
            nav ul li {
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>

    <?php
    // Display error messages
    if (isset($_GET['error'])) {
        $error_message = '';
        switch ($_GET['error']) {
            case 'insufficient_stock':
                $error_message = 'Insufficient stock available for this product.';
                break;
            case 'missing_data':
                $error_message = 'Please select a product and quantity.';
                break;
            case 'invalid_quantity':
                $error_message = 'Please enter a valid quantity.';
                break;
            default:
                $error_message = 'An error occurred. Please try again.';
        }
        if ($error_message) {
            echo '<div style="background: #f8d7da; color: #721c24; padding: 15px; margin: 20px; border-radius: 8px; text-align: center;">' . htmlspecialchars($error_message) . '</div>';
        }
    }
    ?>
    
    <div class="product-grid">
        <?php
        include 'includes/connection.php';
        $result = mysqli_query($conn, "SELECT * FROM product");
        while ($row = mysqli_fetch_assoc($result)) {
        ?>
            <div class="product-card">
                <div class="product-image">
                    <img src="./admin/uploads/<?php echo htmlspecialchars($row['productImage']); ?>" alt="<?php echo htmlspecialchars($row['productName']); ?>">
                </div>
                <div class="product-info">
                    <h3><?php echo htmlspecialchars($row['productName']); ?></h3>
                    <p class="desc"><?php echo htmlspecialchars($row['productDetails']); ?></p>
                    <p class="stock">Available: <?php echo (int)$row['productQuantity']; ?></p>
                    <p class="price">Rs. <?php echo number_format($row['productPrice'], 2); ?></p>
                </div>
                <form class="add-to-cart-form" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $row['productId']; ?>">
                    <input type="number" name="quantity" value="1" min="1" max="<?php echo $row['productQuantity']; ?>" class="quantity-input">
                    <button type="submit" class="btn add-cart-btn">Add to Cart</button>
                </form>
                <form class="buy-now-form" action="buy_now.php" method="POST">
                    <input type="hidden" name="buy_now_product_id" value="<?php echo $row['productId']; ?>">
                    <input type="hidden" name="quantity" value="1" class="buy-now-quantity">
                    <button type="submit" class="btn buy-now-btn">Buy Now</button>
                </form>
            </div>
        <?php } ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(function () {
            // Sync quantity between add to cart and buy now forms
            $('.quantity-input').on('change', function() {
                var quantity = $(this).val();
                var productCard = $(this).closest('.product-card');
                productCard.find('.buy-now-quantity').val(quantity);
            });
            
            $('.add-to-cart-form').on('submit', function (e) {
                e.preventDefault();
                var form = $(this);
                $.post('add_to_cart.php', form.serialize(), function (response) {
                    alert('Added to cart!');
                    if (response.cart_count !== undefined) {
                        $('.cart-badge').text(response.cart_count);
                    }
                }, 'json');
            });
        });
    </script>

    <footer>
        <div class="footer-container">
            <div class="footer-links">
                <a href="index.php">Home</a>
                <a href="aboutus.php">About Us</a>
                <a href="terms.php">Terms & Conditions</a>
            </div>
            <p>© 2025 Crochet E-commerce. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
