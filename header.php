<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
    <nav>
        <ul type="none">
            <li><a href="./index.php">Home</a></li>
            <li><a href="./browse.php">Browse</a></li>
            <li><a href="./orders.php">Orders</a></li>
            <li><a href="#">About Us</a></li>
            <li style="float: right;">
            <?php
                session_start();
                if(isset($_SESSION['Username'])){
                    echo '<a href="">'.$_SESSION['Username'].'</a>';
                }else{
                    echo '<a href="./login.php">Login/Register</a>';
                }
            ?></li>
        </ul>
    </nav>
</body>
</html>