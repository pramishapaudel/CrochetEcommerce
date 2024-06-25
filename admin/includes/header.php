<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav>
        <ul type="none">
            <li><a href="../admin/index.php">Dashboard</a></li>
            <li><a href="../admin/users.php">Users</a></li>
            <li><a href="#">Add Products</a></li>
            <li><a href="../admin/browse.php">Browse</a></li>
            <li><a href="../admin/orders.php">Orders</a></li>
            <li style="float: right;">
            <?php
                session_start();
                if(isset($_SESSION['admUsername'])){
                    echo '<a href="#">'.$_SESSION['admUsername'].'</a>';
                }else{
                    echo '<a href="./login.php">Login/Register</a>';
                }
            ?></li>
        </ul>
    </nav>
</body>
</html>