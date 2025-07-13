<?php 
require('./includes/connection.php');

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
        echo "Username, email, or phone number already exists!";
        $check_stmt->close();
        exit();
    }
    $check_stmt->close();

    // Use prepared statement to prevent SQL injection
    $sql = "INSERT INTO users (userName, Contact, Email, Address, Password) VALUES (?, ?, ?, ?, ?)";

    // Create a prepared statement
    $stmt = $conn->prepare($sql);
                
    // Bind parameters
    $stmt->bind_param("sssss", $username, $phone, $email, $address, $password); 

    // Execute the statement
    if ($stmt->execute()) {
        $stmt->close();
        echo "success";
        exit();
    } else {
        echo "Registration failed! Please try again.";
        $stmt->close();
        exit();
    }
}

// If not POST request, redirect to home page since we now use modal
header('Location: ./index.php');
exit();
