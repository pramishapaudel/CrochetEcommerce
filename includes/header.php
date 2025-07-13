<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="./index.php">Home</a></li>
            <li><a href="./browse.php">Browse</a></li>
            <li><a href="./orders.php">Orders</a></li>
            <li><a href="./aboutus.php">About Us</a></li>            
            <li class="dropdown">
                <?php
                if (isset($_SESSION['Username'])) {
                    echo '<button class="dropbtn">'.$_SESSION['Username'].'</button>';
                    echo '<div class="dropdown-content">';
                    echo '<a href="./includes/profile.php">Profile</a>';
                    echo '<a href="./includes/logout.php">Log Out</a>';
                    echo '</div>';
                } else {
                    echo '<a href="login.php">Login/Register</a>';
                }
                ?>
            </li>
            <?php
            require_once('./includes/connection.php');
            $cart_count = 0;
            if (isset($_SESSION['userID'])) {
                $user_id = $_SESSION['userID'];
                $cart_sql = "SELECT cart_id FROM cart WHERE user_id=? AND status='active'";
                $stmt = $conn->prepare($cart_sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $stmt->bind_result($cart_id);
                $stmt->fetch();
                $stmt->close();

                if ($cart_id) {
                    $count_sql = "SELECT SUM(quantity) FROM cart_items WHERE cart_id=?";
                    $stmt = $conn->prepare($count_sql);
                    $stmt->bind_param("i", $cart_id);
                    $stmt->execute();
                    $stmt->bind_result($cart_count);
                    $stmt->fetch();
                    $stmt->close();
                    if (!$cart_count) $cart_count = 0;
                }
            }
            ?>
            <li>
                <a href="cart.php" class="cart-link" title="View Cart">
                    <i class="fa fa-shopping-cart"></i>
                    <?php if ($cart_count > 0): ?>
                        <span class="cart-badge"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>
    </nav>
</body>
</html>
