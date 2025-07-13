<?php
require('../../includes/connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['product_id'])) {
        $productId = $_POST['product_id'];

        // Begin a transaction
        $conn->begin_transaction();

        try {
            // Retrieve the image path before deleting the product
            $stmt0 = $conn->prepare("SELECT productImage FROM product WHERE productId = ?");
            $stmt0->bind_param('i', $productId);
            $stmt0->execute();
            $stmt0->bind_result($productImage);
            $stmt0->fetch();
            $stmt0->close();

            // Delete related records in the cart_items table first
            $stmt_cart = $conn->prepare("DELETE FROM cart_items WHERE product_id = ?");
            $stmt_cart->bind_param('i', $productId);
            $stmt_cart->execute();
            $stmt_cart->close();

            // Delete related records in the orders table
            $stmt1 = $conn->prepare("DELETE FROM orders WHERE productId = ?");
            $stmt1->bind_param('i', $productId);
            $stmt1->execute();
            $stmt1->close();

            // Now delete the product from the products table
            $stmt2 = $conn->prepare("DELETE FROM product WHERE productId = ?");
            $stmt2->bind_param('i', $productId);
            $stmt2->execute();
            $stmt2->close();

            // Commit the transaction
            $conn->commit();

            // Delete the image file from the server
            if (!empty($productImage) && file_exists("../uploads/" . $productImage)) {
                unlink("../uploads/" . $productImage);
            }

            // Redirect with success message
            header("Location: ../browse.php?success=Product deleted successfully!");
            exit();
        } catch (Exception $e) {
            // An exception has been thrown
            // Rollback the transaction
            $conn->rollback();
            
            // Redirect with error message
            header("Location: ../browse.php?error=Error deleting product: " . urlencode($e->getMessage()));
            exit();
        }

        $conn->close();
    
        // Redirect with error message
        header("Location: ../browse.php?error=Invalid product ID.");
        exit();
    }
} else {
    // Redirect with error message
    header("Location: ../browse.php?error=Invalid request method.");
    exit();
}
?>
