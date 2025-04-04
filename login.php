<?php

require('./includes/connection.php');
require('./includes/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = trim($_POST['phone']); // Trim to remove any accidental spaces
    $password = trim($_POST['password']); // Trim to remove any accidental spaces

    if (empty($phone) || empty($password)) {
        echo "<script>alert('Both fields are required!');</script>";
    } else {
        $user_found = false;

        // Check in USERS table first
        $sql = "SELECT * FROM users WHERE Contact = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user_found = true;
            $row = $result->fetch_assoc();
            $hashedPassword = $row['Password'];

            if (password_verify($password, $hashedPassword)) {
                $_SESSION['Username'] = $row['userName'];
                $_SESSION['Phone'] = $row['Contact'];
                $_SESSION['userID'] = $row['userId'];

                header('Location: ./index.php');
                exit();
            } else {
                echo "<script>alert('Incorrect Password!');</script>";
            }
        }
        $stmt->close();

        // If not found in users, check in ADMIN table
        if (!$user_found) {
            $sql1 = "SELECT * FROM admin WHERE adminNumber = ?";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param("s", $phone);
            $stmt1->execute();
            $result1 = $stmt1->get_result();

            if ($result1->num_rows == 1) {
                $row1 = $result1->fetch_assoc();
                $adminPassword = $row1['adminPassword'];

                // Direct comparison since admin passwords are in plaintext in the table
                if ($password === $adminPassword) {
                    $_SESSION['admUsername'] = $row1['adminName'];
                    $_SESSION['admPhone'] = $row1['adminNumber'];
                    $_SESSION['adminID'] = $row1['adminId'];

                    header('Location: ./admin/index.php');
                    exit();
                } else {
                    echo "<script>alert('Incorrect Admin Password!');</script>";
                }
            } else {
                echo "<script>
                    if (confirm('Number not registered! Wanna Register?')) {
                        window.location.replace('register.php');
                    }
                </script>";
            }
            $stmt1->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" id="loginform">
        <h1>Welcome!</h1>
        <h2>Log in</h2>
        
        <label for="phone">Phone:</label>
        <input type="text" name="phone" id="phone" required maxlength="10" minlength="10" value="9766907593">

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required minlength="8" value="newadminpass">

        <button type="submit">Login</button>
    </form>
</body>
</html>