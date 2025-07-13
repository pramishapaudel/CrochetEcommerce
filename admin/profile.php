<?php
// profile.php
require('../includes/connection.php');
session_start();

if (!isset($_SESSION['admUsername']) || !isset($_SESSION['adminID'])) {
    header("Location: ../../login.php");
    exit();
}

$adminId = $_SESSION['adminID']; // Use same casing as set during login
$sql = "SELECT * FROM admin WHERE adminId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $adminId);
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
    <title>Admin Profile</title>
    <link rel="stylesheet" href="../assets/css/profile.css">
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <button class="back-button" onclick='window.history.back()'>&lt; Go back</button>
            <h1>Admin Profile</h1>
        </div>
        <div class="profile-details">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['adminName']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['adminNumber']); ?></p>
        </div>
    </div>
</body>
</html>
 