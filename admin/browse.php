<?php
session_start();
require('../includes/connection.php');

$select = 'SELECT * FROM product ORDER BY productId DESC';
$result = $conn->query($select);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Browse Products</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php require('./includes/header.php'); ?>
<div class="main-content">
    <h1 style="color:#C9184A;font-family:'Merriweather',serif;text-align:center;margin-bottom:30px;">Browse Products</h1>
    <div class="message-container">
        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                ✅ <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                ❌ <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="product-grid" style="margin-top:30px;">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="product-card admin-card">
                    <form action="includes/delete_product.php" method="POST" style="width:100%;margin-bottom:8px;">
                        <input type="hidden" name="product_id" value="<?php echo $row['productId']; ?>">
                        <button type="submit" class="btn delete-btn" style="width:100%;">Delete</button>
                    </form>
                    <form action="includes/edit_product.php" method="GET" style="width:100%;margin-bottom:8px;">
                        <input type="hidden" name="id" value="<?php echo $row['productId']; ?>">
                        <button type="submit" class="btn edit-btn" style="width:100%;">Edit</button>
                    </form>
                    <div class="product-image" style="margin-bottom:10px;">
                        <img src="uploads/<?php echo htmlspecialchars($row['productImage']); ?>" alt="<?php echo htmlspecialchars($row['productName']); ?>" onerror="this.onerror=null; this.src='default.jpg';">
                    </div>
                    <div class="product-info" style="text-align:center;">
                        <h3><?php echo htmlspecialchars($row['productName']); ?></h3>
                        <div class="desc"><?php echo substr(htmlspecialchars($row['productDetails']), 0, 50); ?></div>
                        <div class="stock">Total: <?php echo (int)$row['productQuantity']; ?></div>
                        <div class="price">Rs. <?php echo number_format($row['productPrice'], 2); ?></div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-orders">
                <i class="fas fa-inbox"></i>
                <h3>No products available!</h3>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
    // Auto-hide messages after 5 seconds
    setTimeout(function() {
        const messages = document.querySelectorAll('.success-message, .error-message');
        messages.forEach(function(message) {
            message.style.display = 'none';
        });
    }, 5000);
</script>
<?php $conn->close(); ?>
</body>
</html>
