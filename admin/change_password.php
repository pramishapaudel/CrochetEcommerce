<?php
session_start();
require('../includes/connection.php');

// Check if admin is logged in
if (!isset($_SESSION['admUsername']) || !isset($_SESSION['adminID'])) {
    header("Location: ../login.php");
    exit();
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate inputs
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $message = "All fields are required!";
        $message_type = "error";
    } elseif ($new_password !== $confirm_password) {
        $message = "New passwords do not match!";
        $message_type = "error";
    } elseif (strlen($new_password) < 6) {
        $message = "New password must be at least 6 characters long!";
        $message_type = "error";
    } else {
        // Verify current password
        $admin_id = $_SESSION['adminID'];
        $sql = "SELECT adminPassword FROM admin WHERE adminId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $admin = $result->fetch_assoc();
            
            // Check if current password matches (plaintext comparison as per existing system)
            if ($current_password === $admin['adminPassword']) {
                // Update password
                $update_sql = "UPDATE admin SET adminPassword = ? WHERE adminId = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("si", $new_password, $admin_id);
                
                if ($update_stmt->execute()) {
                    $message = "Password updated successfully!";
                    $message_type = "success";
                } else {
                    $message = "Error updating password: " . $conn->error;
                    $message_type = "error";
                }
                $update_stmt->close();
            } else {
                $message = "Current password is incorrect!";
                $message_type = "error";
            }
        } else {
            $message = "Admin user not found!";
            $message_type = "error";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Admin Password</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .change-password-main {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 10px 0 10px;
        }
        .change-password-section {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(201,24,74,0.10), 0 1.5px 8px rgba(255,117,143,0.10);
            padding: 48px 40px 40px 40px;
            margin-bottom: 30px;
            text-align: center;
            max-width: 480px;
            width: 100%;
            animation: fadeIn 0.7s;
        }
        .change-password-section h2 {
            color: #C9184A;
            font-family: 'Merriweather', serif;
            margin-bottom: 32px;
            font-size: 2em;
        }
        .change-password-form {
            display: flex;
            flex-direction: column;
            gap: 22px;
            align-items: stretch;
        }
        .change-password-form label {
            font-weight: 600;
            color: #C9184A;
            margin-bottom: 8px;
            display: block;
            text-align: left;
            font-size: 1.08em;
        }
        .change-password-form input[type="password"] {
            width: 100%;
            padding: 13px 12px;
            border: 2px solid #C9184A;
            border-radius: 10px;
            font-size: 1.08em;
            background: #f9f9f9;
            transition: border 0.2s;
        }
        .change-password-form input[type="password"]:focus {
            border: 2px solid #FF758F;
            outline: none;
        }
        .change-password-btn {
            padding: 15px 0;
            background: linear-gradient(90deg, #FF758F, #C9184A);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1.15em;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            margin-top: 10px;
            width: 100%;
        }
        .change-password-btn:hover {
            background: linear-gradient(90deg, #C9184A, #FF758F);
            transform: translateY(-2px);
        }
        .form-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
        }
        .form-message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .form-message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        @media (max-width: 600px) {
            .change-password-main {
                padding: 10px 0 0 0;
            }
            .change-password-section {
                padding: 20px 5px;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
<?php require('includes/header.php'); ?>
<div class="change-password-main">
    <div class="change-password-section">
        <h2><i class="fas fa-key"></i> Change Admin Password</h2>
        <?php if ($message): ?>
            <div class="form-message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <form method="POST" class="change-password-form">
            <div>
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            <div>
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required minlength="6">
            </div>
            <div>
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
            </div>
            <button type="submit" class="change-password-btn">Change Password</button>
        </form>
    </div>
</div>
</body>
</html> 