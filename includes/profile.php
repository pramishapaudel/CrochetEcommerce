<?php
// includes/profile.php
require('./connection.php');
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];
$sql = "SELECT * FROM users WHERE userId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="../assets/css/profile.css">
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <button class="back-button" onclick='window.history.back()'>&lt; Go back</button>
            <h1>User Profile</h1>
        </div>
        <div class="profile-details">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['userName'] ?? 'N/A'); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['Email'] ?? 'N/A'); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($user['Address'] ?? 'N/A'); ?></p>
            <p><strong>Contact:</strong> <?php echo htmlspecialchars($user['Contact'] ?? 'N/A'); ?></p>
        </div>
    </div>
</body>
</html>
