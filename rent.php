<?php
require('./includes/header.php');
require('./includes/connection.php'); // Ensure session is started

// Check if 'id' parameter is set in the URL
if (!isset($_GET['id'])) {
    echo "No product ID provided.";
    exit;
}

$vID = $_GET['id'];
$uid = isset($_SESSION['userID']) ? $_SESSION['userID'] : null;
$username = isset($_SESSION['Username']) ? $_SESSION['Username'] : null;

// Prepare a SQL statement to prevent SQL injection
$stmt = $conn->prepare('SELECT * FROM product WHERE productId = ?');
$stmt->bind_param('i', $vID);
$stmt->execute();
$res = $stmt->get_result();
$product = $res->fetch_assoc();

// If product not found, exit
if (!$product) {
    echo "Product not found.";
    exit;
}

// Fetch user details only if logged in
if ($uid) {
    $stmt1 = $conn->prepare('SELECT * FROM users WHERE userId = ?');
    $stmt1->bind_param('i', $uid);
    $stmt1->execute();
    $res1 = $stmt1->get_result();
    $resuser = $res1->fetch_assoc();
    
    // Check if the user has active orders
    $stmt2 = $conn->prepare('SELECT COUNT(*) FROM orders WHERE userId = ? AND status != "returned"');
    $stmt2->bind_param('i', $uid);
    $stmt2->execute();
    $stmt2->bind_result($activeOrders);
    $stmt2->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['productName']); ?></title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        main {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .product-container {
            display: flex;
            flex-direction: row;
            gap: 100px;
        }
        img {
            max-width: 300px;
            height: auto;
        }
        .product-details {
            flex: 1;
        }
        h1 {
            font-size: 2em;
            margin-bottom: 20px;
        }
        p {
            font-size: 1.1em;
            line-height: 1.6;
        }
        .renting-form {
            margin-top: 20px;
        }
        button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 1em;
            color: #fff;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        button:disabled {
            background-color: gray;
            cursor: not-allowed;
        }
        input[type="date"] {
            padding: 10px;
            font-size: 1em;
        }
    </style>
    <script>
function confirmOrder(vID, uID) {
    if (confirm('Confirm Rent Order?')) {
        const rentDate = document.getElementById('rentDate').value;
        
        if (rentDate) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "includes/confirm_order.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            alert("haha aayo");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        alert(xhr.responseText); // Show success/error message
                        location.reload(); // Reload to reflect changes
                    } else {
                        alert("Error: " + xhr.responseText);
                    }
                }
            };

            // Send data to the server
            xhr.send(`vehicleID=${vID}&userID=${uID}&rentDate=${rentDate}`);
        } else {
            alert("Please enter an appropriate date.");
        }
    }
}
</script>
</head>
<body>
    <main>
        <h1><?php echo htmlspecialchars($product['productName']); ?></h1>
        <div class="product-container">
            <img src="<?php echo htmlspecialchars($product['productImage']); ?>" alt="<?php echo htmlspecialchars($product['productName']); ?>">
            <div class="product-details">
                <p><?php echo nl2br(htmlspecialchars($product['productDetails'])); ?></p>
                <p>Price: Rs <?php echo htmlspecialchars($product['productPrice']); ?></p>

                <?php if ($username): ?>
                    
                        <div class="renting-form">
                            <label for="rentDate">Rent Date:</label>
                            <input type="date" id="rentDate" name="rentDate" min="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d', strtotime('+2 days')); ?>" required>
                            <button onclick="confirmOrder(<?php echo htmlspecialchars($vID) . ', ' . htmlspecialchars($uid); ?>)" id="rent">Rent!</button>
                        </div>
                <?php else: ?>
                    <button onclick="alert('Login First!')">Rent!</button>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>

<?php
// Close the statements and the connection
$stmt->close();
if ($uid) {
    $stmt1->close();
    $stmt2->close();
}
$conn->close();
?>
