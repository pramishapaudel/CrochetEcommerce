<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="./index.php">Home</a></li>
            <li><a href="./browse.php">Browse</a></li>
            <li><a href="./orders.php">Orders</a></li>
            <li><a href="#">About Us</a></li>
            <li class="dropdown">
                <?php
                if (isset($_SESSION['Username'])) {
                    echo '<button class="dropbtn">'.$_SESSION['Username'].'</button>';
                    echo '<div class="dropdown-content">';
                    echo '<a href="./includes/profile.php">Profile</a>';
                    echo '<a href="./includes/logout.php">Log Out</a>';
                    echo '</div>';
                } else {
                    echo '<a href="./login.php">Login/Register</a>';
                }
                ?>
            </li>
        </ul>
    </nav>
</body>
</html>
