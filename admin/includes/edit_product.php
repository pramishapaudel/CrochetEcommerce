<?php
require('../../includes/connection.php');

if (isset($_GET['id'])) {
    $vehicleID = $_GET['id'];

    // Fetch the product details
    $stmt = $conn->prepare('SELECT * FROM products WHERE vehicleID = ?');
    $stmt->bind_param('i', $vehicleID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "Product not found.";
        exit();
    }
} else {
    echo "No product ID provided.";
    exit();
}

// Update the product details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vehicleName = $_POST['vehicleName'];
    $vehicleDes = $_POST['vehicleDes'];
    $price = $_POST['price'];
    $vehicleQuantity = $_POST['vehicleQuantity'];
    // $vehicleLeft = $_POST['vehicleLeft'];
    // $vehiclePending = $_POST['vehiclePending'];

    // File upload handling
    if (isset($_FILES['vehicleImg']) && $_FILES['vehicleImg']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $fileType = $_FILES['vehicleImg']['type'];
        $fileSize = $_FILES['vehicleImg']['size'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (in_array($fileType, $allowedTypes) && $fileSize <= $maxSize) {
            $target_dir = "../img/";
            $target_file = $target_dir . basename($_FILES["vehicleImg"]["name"]);
            if (move_uploaded_file($_FILES["vehicleImg"]["tmp_name"], $target_file)) {
                // Delete the old image file if it's different
                if ($product['vehicleImg'] && file_exists("../" . $product['vehicleImg']) && $product['vehicleImg'] !== $target_file) {
                    unlink("../" . $product['vehicleImg']);
                }
                $vehicleImg = $target_file;
            } else {
                echo "Sorry, there was an error uploading your file.";
                exit();
            }
        } else {
            echo "Invalid file type or file size too large.";
            exit();
        }
    } else {
        $vehicleImg = $product['vehicleImg'];
    }

    // Update the product in the database
    $stmt = $conn->prepare('UPDATE products SET vehicleName = ?, vehicleDes = ?, price = ?, vehicleQuantity = ?, vehicleLeft = ?, vehicleImg = ? WHERE vehicleID = ?');
    $stmt->bind_param('ssdiisi', $vehicleName, $vehicleDes, $price, $vehicleQuantity, $vehicleQuantity, $vehicleImg, $vehicleID);

    if ($stmt->execute()) {
        echo "<script>alert('Vehicle Updated Successfully');</script>";
        header("Location: ../browse.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    exit();
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
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <center><h1>Edit Product</h1></center>
    <form method="post" enctype="multipart/form-data">
        <label for="vehicleName">Vehicle Name:</label>
        <input type="text" id="vehicleName" name="vehicleName" value="<?php echo htmlspecialchars($product['vehicleName']); ?>" required>

        <label for="vehicleDes">Vehicle Description:</label>
        <textarea id="vehicleDes" name="vehicleDes" required><?php echo htmlspecialchars($product['vehicleDes']); ?></textarea>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>

        <label for="vehicleQuantity">Total Quantity:</label>
        <input type="number" id="vehicleQuantity" name="vehicleQuantity" value="<?php echo htmlspecialchars($product['vehicleQuantity']); ?>" required>

        <!-- <label for="vehicleLeft">Remaining Quantity:</label>
        <input type="number" id="vehicleLeft" name="vehicleLeft" value="<?php echo htmlspecialchars($product['vehicleLeft']); ?>" required>

        <label for="vehiclePending">Pending Quantity:</label>
        <input type="number" id="vehiclePending" name="vehiclePending" value="<?php echo htmlspecialchars($product['vehiclePending']); ?>" required> -->

        <label for="vehicleImg">Vehicle Image (Max 2MB, JPG/PNG):</label>
        <input type="file" accept=".jpg, .png, .jpeg" id="vehicleImg" name="vehicleImg">

        <button type="submit">Update Product</button>
    </form>
</body>
</html>
