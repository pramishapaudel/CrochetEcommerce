<?php
session_start();
require('./includes/connection.php');

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $login_error = "Both fields are required!";
    } else {
        // 1. Check in ADMIN table first
        $sql1 = "SELECT * FROM admin WHERE adminName = ? OR adminNumber = ?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("ss", $username, $username);
        $stmt1->execute();
        $result1 = $stmt1->get_result();

        if ($result1->num_rows == 1) {
            $row1 = $result1->fetch_assoc();
            $adminPassword = $row1['adminPassword'];
            if ($password === $adminPassword) {
                $_SESSION['admUsername'] = $row1['adminName'];
                $_SESSION['admPhone'] = $row1['adminNumber'];
                $_SESSION['adminID'] = $row1['adminId'];
                header('Location: admin/index.php');
                exit();
            } else {
                $login_error = "Incorrect Admin Password!";
            }
        } else {
            // 2. If not found in admin, check in USERS table
            $sql = "SELECT * FROM users WHERE userName = ? OR Contact = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $hashedPassword = $row['Password'];
                if (password_verify($password, $hashedPassword)) {
                    $_SESSION['Username'] = $row['userName'];
                    $_SESSION['Phone'] = $row['Contact'];
                    $_SESSION['userID'] = $row['userId'];
                    header('Location: index.php');
                    exit();
                } else {
                    $login_error = "Incorrect Password!";
                }
            } else {
                $login_error = "User not found! Please register first.";
            }
            $stmt->close();
        }
        $stmt1->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Yarn-Joy Crochet</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 60px auto;
            background: #fff;
            padding: 30px 30px 20px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-group button {
            width: 100%;
            padding: 12px;
            background-color: #e75480;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #c7436b;
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
        .register-link {
            text-align: center;
            margin-top: 15px;
        }
        .register-link a {
            color: #e75480;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<?php include './includes/header.php'; ?>
    <div class="login-container">
        <h2>Login</h2>
        <?php if ($login_error): ?>
            <div class="error-message"><?php echo htmlspecialchars($login_error); ?></div>
        <?php endif; ?>
        <form method="POST" autocomplete="off">
            <div class="form-group">
                <label for="username">Username or Phone:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <button type="submit">Login</button>
            </div>
        </form>
        <div class="register-link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>
</body>
</html>
