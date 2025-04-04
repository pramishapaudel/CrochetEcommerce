<?php
require('./includes/header.php');
require('./includes/connection.php'); // Ensure session is started

//Check if 'id' parameter is set in the URL
if (!isset($_GET['id'])) {
    echo "No product ID provided.";
    exit;
}

$pID = $_GET['id'];
$uid = isset($_SESSION['userID']) ? $_SESSION['userID'] : null;
$username = isset($_SESSION['Username']) ? $_SESSION['Username'] : null;

// Prepare a SQL statement to prevent SQL injection
$stmt = $conn->prepare('SELECT * FROM product WHERE productId = ?');
$stmt->bind_param('i', $pID);
$stmt->execute();
$res = $stmt->get_result();
$product = $res->fetch_assoc();


// If product not found, exit
if (!$product) {
    echo "Product not found.";
    exit;
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
        .purchase-form {
            margin-top: 20px;
        }
        button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 1em;
            color: #fff;
            background-color: #28a745;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
    <script>
function confirmPurchase(pID, uID) {
    if (confirm('Confirm Purchase?')) {
        $.ajax({
            type: "POST",
            url: "./includes/confirm_order.php",
            data: { productID: pID, userID: uID },
            success: function(response) {
                alert(response);
                location.reload();
            },
            error: function() {
                alert("Error processing your purchase.");
            }
        });
    }
}
</script>
</head>
<body>
    <main>
        <h1><?php echo htmlspecialchars($product['productName']); ?></h1>
        <div class="product-container">
            <img src="./admin/uploads/<?php echo htmlspecialchars($product['productImage']); ?>" alt="<?php echo htmlspecialchars($product['productName']); ?>">
            <div class="product-details">
                <p><?php echo nl2br(htmlspecialchars($product['productDetails'])); ?></p>
                <p>Price: Rs <?php echo htmlspecialchars($product['productPrice']); ?></p>
                <?php if ($username): ?>
                    <div class="purchase-form">
                        <button onclick="confirmPurchase(<?php echo htmlspecialchars($pID) . ', ' . htmlspecialchars($uid); ?>)">Buy Now</button>
                    </div>
                <?php else: ?>
                    <button onclick="alert('Login First!')">Buy Now</button>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>

<?php
// Close the statements and the connection
$stmt->close();
$conn->close();
?>
