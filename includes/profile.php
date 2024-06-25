<?php
    // profile.php
    require('./connection.php');
    session_start();

    if (!isset($_SESSION['userID'])) {
        header("Location: login.php");
        exit();
    }

    // Fetch user details using $_SESSION['userID']
    $userID = $_SESSION['userID'];
    $sql = "SELECT * FROM users WHERE UserID = ?";
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
    <title>Profile</title>
    <link rel="stylesheet" href="../assets/css/profile.css">
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <button class="back-button" onclick='window.history.back()'>&lt; Go back</button>
            <h1>Profile</h1>
        </div>
        <div class="profile-details">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['Name']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['Contact']); ?></p>
            <p><strong>Birth Date:</strong> <?php echo htmlspecialchars($user['DOB']); ?></p>
            <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['Gender']); ?></p>
            <p><strong>Citizenship:</strong> <?php echo htmlspecialchars($user['Citizenship_no']); ?></p>
            <p><strong>License:</strong> <?php echo htmlspecialchars($user['License_no']); ?></p>
        </div>
    </div>
</body>
</html>
