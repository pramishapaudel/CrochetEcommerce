<?php
    require('../../includes/connection.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['productID'])) {
            $productId = $_POST['productID'];

            // Begin a transaction
            $conn->begin_transaction();

            try {
                // Retrieve the image path before deleting the vehicle
                $stmt0 = $conn->prepare("SELECT productImage FROM product WHERE productId = ?");
                $stmt0->bind_param('i', $productId);
                $stmt0->execute();
                $stmt0->bind_result($productImage);
                $stmt0->fetch();
                $stmt0->close();

                // Delete related records in the orders table first
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
                if (file_exists($productImage)) {
                    unlink($productImage);
                }

                echo 'success';
            } catch (Exception $e) {
                // An exception has been thrown
                // Rollback the transaction
                $conn->rollback();
                echo 'Error: ' . $e->getMessage();
            }

            $conn->close();
        } else {
            echo 'Invalid product ID.';
        }
    } else {
        echo 'Invalid request method.';
    }
?>
