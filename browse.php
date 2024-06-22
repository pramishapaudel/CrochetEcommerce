<?php
    require('./header.php');
    require('./connection.php');
    $select = 'SELECT * FROM products';

    $result = $conn->query($select);

    // Check if the query was successful
    if ($result) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <style>
        .container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px; /* Space between items */
        }
        .product {
            border: 1px solid red;
            height: 300px;
            width: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .product img {
            height: 100px;
            width: 100px;
        }
        .product div {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
            while ($row = $result->fetch_assoc()) {
        ?>
        <div class="product">
            <img src="<?php echo $row['vehicleImg']; ?>" alt="<?php echo $row['vehicleName']; ?>">
            <div>
                <p><?php echo $row['vehicleName']; ?></p>
                <p><?php echo $row['vehicleDes']; ?></p>
                <a href="rent.php?id=<?php echo $row['vehicleID']; ?>"><button>Rent!</button></a>
            </div>
        </div>
        <?php 
            }
        ?>
    </div>
</body>
</html>
<?php
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
?>
