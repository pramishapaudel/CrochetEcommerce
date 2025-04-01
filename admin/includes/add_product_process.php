<?php
require('../../includes/connection.php');

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form inputs
    $productName = $_POST['productName'];
    $productDes = $_POST['productDes'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $category = $_POST['category'];
    
    // Handle image upload
    if (isset($_FILES['productImg']) && $_FILES['productImg']['error'] == 0) {
        $target_dir = "../uploads/" . $category . "/";  // Folder based on category (keychains or bags)
        $target_file = $target_dir . basename($_FILES["productImg"]["name"]);
        
        // Create folder if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES["productImg"]["tmp_name"], $target_file)) {
            $imagePath = $category . "/" . basename($_FILES["productImg"]["name"]);
            
            // Insert product into database
            $query = "INSERT INTO product (productName, productDetails, productPrice, productQuantity, productImage)
                      VALUES ('$productName', '$productDes', '$price', '$quantity', '$imagePath')";
            if (mysqli_query($conn, $query)) {
                echo "Product added successfully!";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "Error uploading the image.";
        }
    } else {
        echo "No image uploaded or there was an upload error.";
    }
} else {
    echo "Invalid request method.";
}
?>
