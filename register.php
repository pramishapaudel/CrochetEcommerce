<?php 
require('./includes/connection.php');
require('./includes/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uname = $_POST['name'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Use prepared statement to prevent SQL injection
    $sql = "INSERT INTO users (userName, Contact, Email, Address, Password) VALUES (?, ?, ?, ?, ?)";

    // Create a prepared statement
    $stmt = $conn->prepare($sql);
                
    // Bind parameters
    $stmt->bind_param("sssss", $uname, $contact, $email, $address, $password); 

    // Execute the statement
    if ($stmt->execute()) {
        $stmt->close();
        header('Location: login.php');
        exit();
    } else {
        echo "<script>alert('Registration failed! Please try again.');</script>";
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Account</title>
    <link rel="stylesheet" href="./assets/css/regform.css">
</head>
<body>
    <form action="" method="POST" id="regform">
        <span>Yarn-joy</span><br>
        <h1>Register</h1>
        <p><label for="name">Name:</label>
        <input type="text" name="name" id="name" required></p>

        <p><label for="contact">Contact:</label>
        <input type="text" name="contact" id="contact" required maxlength="10" minlength="10"></p>

        <p><label for="email">Email:</label>
        <input type="email" name="email" id="email" required></p>

        <p><label for="address">Address:</label>
        <input type="text" name="address" id="address" required></p>

        <p><label for="password">Password:</label>
        <input type="password" name="password" id="password" required minlength="8"></p><br>
        <button>Register</button>
    </form>
</body>
</html>