<?php
    require('../../includes/connection.php');

    if (isset($_POST['userID'])) {
        $userID = intval($_POST['userID']);

        // SQL to delete a user
        $sql = "DELETE FROM users WHERE userID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $userID);

        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }

        $stmt->close();
        $conn->close();
    } else {
        echo 'error';
    }
?>
