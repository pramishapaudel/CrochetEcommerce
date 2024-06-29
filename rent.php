<?php
    require('./includes/header.php');
    require('./includes/connection.php');

    // Check if 'id' parameter is set in the URL
    if (isset($_GET['id'])) {
        $vID = $_GET['id'];

        // Prepare a SQL statement to prevent SQL injection
        $stmt = $conn->prepare('SELECT * FROM products WHERE vehicleID = ?');
        $stmt->bind_param('i', $vID);
        $stmt->execute();
        $res = $stmt->get_result();

        $stmt1 = $conn->prepare('SELECT * FROM users WHERE UserID = ".$_SESSION["userID"]."');
        $stmt1->execute();
        $res1 = $stmt1->get_result();
        if ($res1->num_rows > 0) {
            $resuser = $res1->fetch_assoc();
        }

        // Check if the product exists
        if ($res->num_rows > 0) {
            $product = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['vehicleName']); ?></title>
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
        input[type="date"] {
            padding: 10px;
            font-size: 1em;
        }
    </style>
    <script src="./assets/js/script.js"></script>

</head>
<body>
    <main>
        <h1><?php echo htmlspecialchars($product['vehicleName']); ?></h1>
        <div class="product-container">
            <img src="<?php echo htmlspecialchars($product['vehicleImg']); ?>" alt="<?php echo htmlspecialchars($product['vehicleName']); ?>">
            <div class="product-details">
                <p><?php echo nl2br(htmlspecialchars($product['vehicleDes'])); ?></p>
                <p>Price: Rs<?php echo htmlspecialchars($product['price']); ?></p>
                <?php if (isset($_SESSION['Username'])) {
                            if($resuser['status']=='verified'){ ?>
                                <div class="renting-form">
                                    <label for="rentDate">Rent Date:</label>
                                    <input type="date" id="rentDate" name="rentDate" min="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d', strtotime('+2 days')); ?>" required>
                                    <button onclick="confirmOrder(<?php echo htmlspecialchars($vID) . ', ' . htmlspecialchars($_SESSION['userID']); ?>)" id="rent">Rent!</button>
                                </div>
                            <?php } else {
                                echo "alert('verify first')";
                                }}else{?>
                    <button onclick="alert('Login First!')">Rent!</button>
                <?php } ?>
            </div>
        </div>
    </main>
</body>
</html>
<?php
        } else {
            echo "Product not found.";
        }

        // Close the statement and the connection
        $stmt->close();
        $conn->close();
    } else {
        echo "No product ID provided.";
    }
?>
