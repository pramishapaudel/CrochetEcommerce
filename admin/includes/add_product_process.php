<?php
require('../../includes/connection.php');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Function to log errors
function logError($message) {
    echo "<strong>ERROR:</strong> " . $message . "<br>";
    error_log($message);
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validate inputs exist
        if (!isset($_POST['productName']) || !isset($_POST['productDes']) || 
            !isset($_POST['price']) || !isset($_POST['quantity'])) {
            throw new Exception("Missing required form fields");
        }
        
        // Get form inputs
        $productName = isset($_POST['productName']) ? mysqli_real_escape_string($conn, $_POST['productName']) : '';
        $productDes = isset($_POST['productDes']) ? mysqli_real_escape_string($conn, $_POST['productDes']) : '';
        $price = isset($_POST['price']) ? mysqli_real_escape_string($conn, $_POST['price']) : 0;
        $quantity = isset($_POST['quantity']) ? mysqli_real_escape_string($conn, $_POST['quantity']) : 0;
        
       
        
        // Basic validation
        if (empty($productName) || empty($productDes) || empty($price) || empty($quantity)) {
            throw new Exception("All fields are required");
        }
        
        // Handle image upload - separate this logic
        $imagePath = "";
        if (isset($_FILES['productImg']) && $_FILES['productImg']['error'] == 0) {
           
            $target_dir = "../uploads/";
            
            // Create folder if it doesn't exist
            if (!file_exists($target_dir)) {
                if (!mkdir($target_dir, 0777, true)) {
                    throw new Exception("Failed to create upload directory");
                }
            }
            
            $target_file = $target_dir . basename($_FILES["productImg"]["name"]);
            
            
            
            // Check if file is actually an image
            $check = getimagesize($_FILES["productImg"]["tmp_name"]);
            if ($check === false) {
                throw new Exception("File is not an image");
            }
            
            // Move the uploaded file
            if (!move_uploaded_file($_FILES["productImg"]["tmp_name"], $target_file)) {
                throw new Exception("Failed to move uploaded file");
            }
            
            $imagePath = "./" . basename($_FILES["productImg"]["name"]);
            
        } else {
            throw new Exception("Image upload failed with error code: " . 
                (isset($_FILES['productImg']) ? $_FILES['productImg']['error'] : 'No file uploaded'));
        }
        
        // Database insertion - simplified version without adminId
        $query = "INSERT INTO product (productName, productDetails, productPrice, productQuantity, productImage) 
                  VALUES (?, ?, ?, ?, ?)";
                  
        // Use prepared statement to prevent SQL injection
        $stmt = mysqli_prepare($conn, $query);
        
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt, "sssss", $productName, $productDes, $price, $quantity, $imagePath);
        
        
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            // Redirect to browse page with success message
            header("Location: ../browse.php?success=Product added successfully!");
            exit();
        } else {
            throw new Exception("Database insertion failed: " . mysqli_stmt_error($stmt));
        }
        
    } catch (Exception $e) {
        logError("Exception caught: " . $e->getMessage());
        // Redirect with error message
        header("Location: ../add_products.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    // Redirect with error message for invalid request method
    header("Location: ../add_products.php?error=Invalid request method.");
    exit();
}
?>