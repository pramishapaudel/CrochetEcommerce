<?php
require('../../includes/connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['userID'])) {
        $userID = $_POST['userID'];

        // Update the user's status to verified in the database
        $stmt = $conn->prepare("UPDATE users SET status = 'verified' WHERE userID = ?");
        $stmt->bind_param('i', $userID);

        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'Error: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        echo 'Invalid user ID.';
    }
} else {
    echo 'Invalid request method.';
}

$conn->close();
?>
