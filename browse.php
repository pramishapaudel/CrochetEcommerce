<?php
require('./includes/header.php');
require('includes/connection.php');

// Fetch products sorted by newest first
$select = 'SELECT * FROM product ORDER BY productId DESC';
$result = $conn->query($select);

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
            padding: 10px;
        }
        .product img {
            height: 100px;
            width: 100px;
            object-fit: cover;
        }
        .product div {
            text-align: center;
        }
        p {
            font-size: 1em;
        }
        .price {
            font-size: 1.1em;
            font-weight: bold;
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
            while ($row = $result->fetch_assoc()) {
                $imagePath = !empty($row['productImage']) ? 'admin/' . $row['productImage'] : 'admin/default.jpg';
        ?>
        <div class="product">
            <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                 alt="<?php echo htmlspecialchars($row['productName']); ?>" 
                 onerror="this.onerror=null; this.src='admin/default.jpg';">
            <div>
                <p><?php echo htmlspecialchars($row['productName']); ?></p>
                <p><?php echo substr(htmlspecialchars($row['productDetails']), 0, 50); ?>...</p>
                <p>Available: <?php echo (int) $row['productQuantity']; ?></p>
                <p class="price">Rs. <?php echo number_format((float) $row['productPrice'], 2); ?></p>
                <a href="rent.php?id=<?php echo (int) $row['productId']; ?>"><button>Buy Now</button></a>
            </div>
        </div>
        <?php 
            }
        ?>
    </div>
</body>
</html>
<?php
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
include('includes/footer.php');
?>
