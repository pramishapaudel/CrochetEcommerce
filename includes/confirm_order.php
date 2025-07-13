<?php
// Include config file
require_once "./connection.php";

session_start();

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    echo "Please login to place an order.";
    exit();
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productID = $_POST['productID'];
    $userID = $_POST['userID']; // Use session userID for security
    $orderQuantity = isset($_POST['orderQuantity']) ? (int)$_POST['orderQuantity'] : 1; // Get order quantity from POST
    $fordate = date('Y-m-d');
    $status = "pending";

    // Validate order quantity
    if ($orderQuantity <= 0) {
        echo "Invalid quantity.";
        exit();
    }

    // Validate the productID and userID exist in their respective tables
    $stmt = $conn->prepare('SELECT productQuantity FROM product WHERE productId = ?');
    $stmt->bind_param('i', $productID);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    $stmt = $conn->prepare('SELECT COUNT(*) FROM users WHERE userId = ?');
    $stmt->bind_param('i', $userID);
    $stmt->execute();
    $stmt->bind_result($userCount);
    $stmt->fetch();
    $stmt->close();

    if ($product && $userCount > 0) {
        // Check if enough stock is available
        if ($product['productQuantity'] < $orderQuantity) {
            echo "Insufficient stock available.";
            $conn->close();
            exit();
        }

        // Prepare the SQL statement to prevent SQL injection
        $stmt = $conn->prepare('INSERT INTO orders (productId, userId, orderQuantity, date, status) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('iiiss', $productID, $userID, $orderQuantity, $fordate, $status);

        if ($stmt->execute()) {
            echo "Order Placement Successful";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement and the connection
        $stmt->close();
        $conn->close();
    } else {
        echo "Invalid product ID or user ID.";
    }
} else {
    echo "Invalid request.";
}
?>