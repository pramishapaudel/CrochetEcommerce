<?php
    require('./connection.php');
    session_start();

    if (isset($_POST['orderID']) && isset($_SESSION["userID"])) {
        $orderID = $_POST['orderID'];
        $userID = $_SESSION["userID"];

        // Prepare the SQL statement to delete the order
        $stmt = $conn->prepare("DELETE FROM orders WHERE orderID = ? AND userID = ?");
        $stmt->bind_param('ii', $orderID, $userID);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }

        // Close the statement and connection
        $stmt->close();
        $conn->close();
    } else {
        echo "error";
    }
?>
