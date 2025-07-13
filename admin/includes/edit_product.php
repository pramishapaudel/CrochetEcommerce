<?php
require('../../includes/connection.php');

$success_message = '';
$error_message = '';

if (isset($_GET['id'])) {
    $productID = $_GET['id'];

    // Fetch the product details
    $stmt = $conn->prepare('SELECT * FROM product WHERE productId = ?');
    $stmt->bind_param('i', $productID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        $error_message = "Product not found.";
    }
} else {
    $error_message = "No product ID provided.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $productName = $_POST['productName'];
        $productDes = $_POST['productDes'];
        $price = $_POST['price'];
        $productQuantity = $_POST['productQuantity'];
        $productImage = $product['productImage']; // Default to existing image

        // Handle image upload
        if (!empty($_FILES['productImage']['name'])) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            $fileType = $_FILES['productImage']['type'];
            $fileSize = $_FILES['productImage']['size'];
            $maxSize = 2 * 1024 * 1024; // 2MB

            if (in_array($fileType, $allowedTypes) && $fileSize <= $maxSize) {
                $target_dir = __DIR__ . "/../uploads/";  // Fixed path

                // Ensure the uploads directory exists
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                $fileName = basename($_FILES["productImage"]["name"]);
                $target_file = $target_dir . $fileName;
                $relativePath = $fileName; // Path stored in DB

                if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $target_file)) {
                    // Delete old image if it's different
                    if (!empty($product['productImage']) && file_exists($target_dir . $product['productImage']) && $product['productImage'] !== $relativePath) {
                        unlink($target_dir . $product['productImage']);
                    }
                    $productImage = $relativePath;
                } else {
                    throw new Exception("Error uploading file.");
                }
            } else {
                throw new Exception("Invalid file type or file size too large.");
            }
        }

        // Update product in database
        $stmt = $conn->prepare('UPDATE product SET productName = ?, productDetails = ?, productPrice = ?, productQuantity = ?, productImage = ? WHERE productID = ?');
        $stmt->bind_param('ssdisi', $productName, $productDes, $price, $productQuantity, $productImage, $productID);

        if ($stmt->execute()) {
            $success_message = "Product updated successfully!";
        } else {
            throw new Exception("Database error: " . $stmt->error);
        }

        $stmt->close();
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            margin: 20px 0;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            margin: 20px 0;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }
        
        form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background: linear-gradient(90deg, #FF758F, #C9184A);
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: linear-gradient(90deg, #C9184A, #FF758F);
            transform: translateY(-3px);
        }
        img {
            max-width: 100%;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .back-btn {
            background: #6c757d;
            margin-right: 10px;
        }
        .back-btn:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
    <center><h1>Edit Product</h1></center>
    
    <?php if (!empty($success_message)): ?>
        <div class="success-message">
            ✅ <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error_message)): ?>
        <div class="error-message">
            ❌ <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>
    
    <?php if (empty($error_message) && isset($product)): ?>
    <form method="post" enctype="multipart/form-data">
        <label for="productName">Product Name:</label>
        <input type="text" id="productName" name="productName" value="<?php echo htmlspecialchars($product['productName']); ?>" required>

        <label for="productDes">Product Description:</label>
        <textarea id="productDes" name="productDes" required><?php echo htmlspecialchars($product['productDetails']); ?></textarea>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($product['productPrice']); ?>" required>

        <label for="productQuantity">Total Quantity:</label>
        <input type="number" id="productQuantity" name="productQuantity" value="<?php echo htmlspecialchars($product['productQuantity']); ?>" required>

        <!-- Display current image -->
        <?php if (!empty($product['productImage'])): ?>
            <label>Current Image:</label>
            <img src="../uploads/<?php echo htmlspecialchars($product['productImage']); ?>" alt="Product Image">
        <?php endif; ?>

        <label for="productImage">Product Image (Max 2MB, JPG/PNG):</label>
        <input type="file" accept=".jpg, .png, .jpeg" id="productImage" name="productImage">

        <button type="button" class="back-btn" onclick="window.location.href='../browse.php'">Back to Products</button>
        <button type="submit">Update Product</button>
    </form>
    <?php else: ?>
        <div style="text-align: center; margin-top: 50px;">
            <button type="button" class="back-btn" onclick="window.location.href='../browse.php'">Back to Products</button>
        </div>
    <?php endif; ?>
    
    <script>
        // Auto-hide success message after 5 seconds
        <?php if (!empty($success_message)): ?>
        setTimeout(function() {
            document.querySelector('.success-message').style.display = 'none';
        }, 5000);
        <?php endif; ?>
        
        // Auto-hide error message after 5 seconds
        <?php if (!empty($error_message)): ?>
        setTimeout(function() {
            document.querySelector('.error-message').style.display = 'none';
        }, 5000);
        <?php endif; ?>
    </script>
</body>
</html>
