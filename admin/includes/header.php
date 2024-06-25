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
            <li><a href="../admin/add_products.php">Add Products</a></li>
            <li><a href="../admin/browse.php">Browse</a></li>
            <li><a href="../admin/orders.php">Orders</a></li>
            <li class="dropdown">
            <?php
                session_start();
                if(isset($_SESSION['admUsername'])){
                    echo '<button class="dropbtn">'.$_SESSION['admUsername'].'</button>';
                    echo '<div class="dropdown-content">';
                    echo '<a href="../admin/profile.php">Profile</a>';
                    echo '<a href="./includes/logout.php">Log Out</a>';
                    echo '</div>';
                }else{
                    echo '<a href="./login.php">Login/Register</a>';
                }
            ?></li>
        </ul>
    </nav>
</body>
</html>