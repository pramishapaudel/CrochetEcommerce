<!-- Modern Admin Header and Navigation with Active Tab Highlighting -->
<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<div class="header">
    <h1>Yarn-Joy Admin</h1>
    <div class="user-info">
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['admUsername'] ?? 'Admin'); ?></span>
        <a href="profile.php">Profile</a>
        <a href="change_password.php">Change Password</a>
        <a href="includes/logout.php">Logout</a>
    </div>
</div>
<div class="nav">
    <ul>
        <li><a href="index.php"<?php if($currentPage == 'index.php') echo ' class="active"'; ?>>Dashboard</a></li>
        <li><a href="orders.php"<?php if($currentPage == 'orders.php') echo ' class="active"'; ?>>All Orders</a></li>
        <li><a href="transactions.php"<?php if($currentPage == 'transactions.php') echo ' class="active"'; ?>>Transactions</a></li>
        <li><a href="users.php"<?php if($currentPage == 'users.php') echo ' class="active"'; ?>>Users</a></li>
        <li><a href="add_products.php"<?php if($currentPage == 'add_products.php') echo ' class="active"'; ?>>Add Products</a></li>
        <li><a href="browse.php"<?php if($currentPage == 'browse.php') echo ' class="active"'; ?>>Browse Products</a></li>
    </ul>
</div>