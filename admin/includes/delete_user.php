<?php
require('../../includes/connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['userID'])) {
        $userID = $_POST['userID'];

        // Begin a transaction
        $conn->begin_transaction();

        try {
            // Delete related records in other tables first
            $stmt1 = $conn->prepare("DELETE FROM orders WHERE userId = ?");
            $stmt1->bind_param('i', $userID);
            $stmt1->execute();
            $stmt1->close();

            // Now delete the user
            $stmt2 = $conn->prepare("DELETE FROM users WHERE userId = ?");
            $stmt2->bind_param('i', $userID);
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
        echo 'Invalid user ID.';
    }
} else {
    echo 'Invalid request method.';
}
?>
