<?php
    require('../../includes/connection.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['vehicleID'])) {
            $vehicleID = $_POST['vehicleID'];

            // Begin a transaction
            $conn->begin_transaction();

            try {
                // Delete related records in the orders table first
                $stmt1 = $conn->prepare("DELETE FROM orders WHERE vehicleID = ?");
                $stmt1->bind_param('i', $vehicleID);
                $stmt1->execute();
                $stmt1->close();

                // Now delete the product from the products table
                $stmt2 = $conn->prepare("DELETE FROM products WHERE vehicleID = ?");
                $stmt2->bind_param('i', $vehicleID);
                $stmt2->execute();
                $stmt2->close();

                // Commit the transaction
                $conn->commit();

                echo 'success';
            } catch (Exception $e) {
                // An exception has been thrown
                // Rollback the transaction
                $conn->rollback();
                echo 'Error: ' . $e->getMessage();
            }

            $conn->close();
        } else {
            echo 'Invalid vehicle ID.';
        }
    } else {
        echo 'Invalid request method.';
    }
?>
