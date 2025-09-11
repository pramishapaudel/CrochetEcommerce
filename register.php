<?php 
require('./includes/connection.php');

session_start();

if (
    isset($_SESSION['userID']) || isset($_SESSION['Username']) ||
    isset($_SESSION['admUsername']) || isset($_SESSION['adminID'])
) {
    // User is already logged in
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Already Logged In</title><link rel="stylesheet" href="./assets/css/style.css"><style>body{display:flex;align-items:center;justify-content:center;height:100vh;background:#fff;} .already-logged-in-msg{background:#fff3cd;color:#856404;padding:30px 40px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.07);font-size:1.3em;font-family:Arial,sans-serif;text-align:center;} a{color:#C9184A;text-decoration:none;font-weight:600;}</style></head><body><div class="already-logged-in-msg">You are already logged in.<br><br><a href="index.php">Go to Home</a></div></body></html>';
    exit();
}

$register_error = '';
$register_success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if username or email already exists
    $check_sql = "SELECT * FROM users WHERE userName = ? OR Email = ? OR Contact = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("sss", $username, $email, $phone);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $register_error = "Username, email, or phone number already exists!";
        $check_stmt->close();
    } else {
        $check_stmt->close();
        // Use prepared statement to prevent SQL injection
        $sql = "INSERT INTO users (userName, Contact, Email, Address, Password) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $username, $phone, $email, $address, $password); 
        if ($stmt->execute()) {
            $stmt->close();
            $register_success = true;
        } else {
            $register_error = "Registration failed! Please try again.";
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Yarn-Joy Crochet</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        .register-container {
            max-width: 450px;
            margin: 60px auto;
            background: #fff;
            padding: 30px 30px 20px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .register-container h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #C9184A;
            font-family: 'Merriweather', serif;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #C9184A;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1.5px solid #FF758F;
            border-radius: 7px;
            background: #f9f9f9;
            font-size: 1em;
        }
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #C9184A;
        }
        .form-group button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(90deg, #FF758F, #C9184A);
            color: white;
            border: none;
            border-radius: 7px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        .form-group button:hover {
            background: linear-gradient(90deg, #C9184A, #FF758F);
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
        .success-message {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
            font-size: 1.1em;
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
    <div class="register-container">
        <h2>Register</h2>
        <?php if ($register_success): ?>
            <div class="success-message">
                Registration successful! Redirecting to login page...
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = 'login.php';
                }, 2000);
            </script>
        <?php else: ?>
            <?php if ($register_error): ?>
                <div class="error-message"><?php echo htmlspecialchars($register_error); ?></div>
            <?php endif; ?>
            <form method="POST" autocomplete="off">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <textarea id="address" name="address" required></textarea>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required minlength="6">
                </div>
                <div class="form-group">
                    <button type="submit">Register</button>
                </div>
            </form>
            <div class="register-link">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
