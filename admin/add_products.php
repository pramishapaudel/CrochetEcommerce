<?php
session_start();
require('../includes/connection.php');

if (!isset($_SESSION['admUsername']) || !isset($_SESSION['adminID'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Arial, 'Merriweather', serif;
            background: #f8f8f8;
            color: rgb(82, 5, 5);
            margin: 0;
        }
        .header {
            background: #fff;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header h1 {
            color: #C9184A;
            font-family: 'Merriweather', serif;
            font-size: 2em;
            letter-spacing: 1px;
        }
        .header .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .header .user-info span {
            color: #C9184A;
            font-weight: bold;
        }
        .header .user-info a {
            color: #fff;
            background: linear-gradient(90deg, #FF758F, #C9184A);
            text-decoration: none;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.3s;
            border: none;
            margin-left: 5px;
        }
        .header .user-info a:hover {
            background: linear-gradient(90deg, #C9184A, #FF758F);
        }
        .nav {
            background: #fff;
            padding: 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 70px;
            z-index: 99;
        }
        .nav ul {
            list-style: none;
            display: flex;
            gap: 0;
            margin: 0;
            padding: 0;
        }
        .nav a {
            color: rgb(82, 5, 5);
            text-decoration: none;
            padding: 18px 32px;
            display: block;
            font-family: 'Merriweather', serif;
            font-weight: 700;
            font-size: 1.1em;
            border-bottom: 3px solid transparent;
            transition: background 0.3s, color 0.3s, border-bottom 0.3s;
        }
        .nav a.active, .nav a:hover {
            background: linear-gradient(90deg, #FF758F, #C9184A);
            color: #fff;
            border-bottom: 3px solid #C9184A;
        }
        .main-content {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 10px 0 10px;
        }
        .add-product-section {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(201,24,74,0.10), 0 1.5px 8px rgba(255,117,143,0.10);
            padding: 48px 40px 40px 40px;
            margin-bottom: 30px;
            text-align: center;
            max-width: 480px;
            width: 100%;
            animation: fadeIn 0.7s;
        }
        .add-product-section h2 {
            color: #C9184A;
            font-family: 'Merriweather', serif;
            margin-bottom: 32px;
            font-size: 2em;
        }
        .add-product-form {
            display: flex;
            flex-direction: column;
            gap: 22px;
            align-items: stretch;
        }
        .add-product-form label {
            font-weight: 600;
            color: #C9184A;
            margin-bottom: 8px;
            display: block;
            text-align: left;
            font-size: 1.08em;
        }
        .add-product-form input[type="text"],
        .add-product-form input[type="number"],
        .add-product-form textarea {
            width: 100%;
            padding: 13px 12px;
            border: 2px solid #C9184A;
            border-radius: 10px;
            font-size: 1.08em;
            background: #f9f9f9;
            transition: border 0.2s;
        }
        .add-product-form input[type="text"]:focus,
        .add-product-form input[type="number"]:focus,
        .add-product-form textarea:focus {
            border: 2px solid #FF758F;
            outline: none;
        }
        .add-product-form textarea {
            min-height: 90px;
            resize: vertical;
        }
        .add-product-form input[type="file"] {
            border: 2px solid #C9184A;
            border-radius: 10px;
            padding: 10px;
            background: #f9f9f9;
            width: 100%;
        }
        .add-product-form .form-row {
            display: flex;
            gap: 18px;
            width: 100%;
        }
        .add-product-form .form-row > div {
            flex: 1;
        }
        .add-product-btn {
            padding: 15px 0;
            background: linear-gradient(90deg, #FF758F, #C9184A);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1.15em;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            margin-top: 10px;
            width: 100%;
        }
        .add-product-btn:hover {
            background: linear-gradient(90deg, #C9184A, #FF758F);
            transform: translateY(-2px);
        }
        .view-products-btn {
            padding: 15px 0;
            background: #eee;
            color: #C9184A;
            border: none;
            border-radius: 10px;
            font-size: 1.15em;
            font-weight: 700;
            cursor: pointer;
            margin-right: 10px;
            margin-top: 10px;
            width: 100%;
            transition: background 0.2s;
        }
        .view-products-btn:hover {
            background: #f9f9f9;
        }
        @media (max-width: 600px) {
            .main-content {
                padding: 10px 0 0 0;
            }
            .add-product-section {
                padding: 20px 5px;
                max-width: 100%;
            }
            .add-product-form .form-row {
                flex-direction: column;
                gap: 10px;
            }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Yarn-Joy Admin</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admUsername']); ?></span>
            <a href="profile.php">Profile</a>
            <a href="change_password.php">Change Password</a>
            <a href="includes/logout.php">Logout</a>
        </div>
    </div>
    <div class="nav">
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="orders.php">All Orders</a></li>
            <li><a href="transactions.php">Transactions</a></li>
            <li><a href="users.php">Users</a></li>
            <li><a href="add_products.php" class="active">Add Products</a></li>
            <li><a href="browse.php">Browse Products</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="add-product-section">
            <h2>Add New Product</h2>
            <form class="add-product-form" action="includes/add_product_process.php" method="POST" enctype="multipart/form-data">
                <div>
                    <label for="productName">Product Name:</label>
                    <input type="text" id="productName" name="productName" required>
                </div>
                <div>
                    <label for="productDes">Product Description:</label>
                    <textarea id="productDes" name="productDes" required></textarea>
                </div>
                <div class="form-row">
                    <div>
                        <label for="price">Price:</label>
                        <input type="number" id="price" name="price" min="0" step="0.01" required>
                    </div>
                    <div>
                        <label for="quantity">Quantity:</label>
                        <input type="number" id="quantity" name="quantity" min="1" required>
                    </div>
                </div>
                <div>
                    <label for="productImg">Product Image:</label>
                    <input type="file" id="productImg" name="productImg" accept="image/*" required>
                </div>
                <div style="display: flex; gap: 16px; justify-content: center;">
                    <a href="browse.php" class="view-products-btn">View Products</a>
                    <button type="submit" class="add-product-btn">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
