<?php
session_start();
require('../includes/connection.php');

if (!isset($_SESSION['admUsername']) || !isset($_SESSION['adminID'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch all users from the database
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Arial, 'Merriweather', serif;
            background: #f8f8f8;
            color: rgb(82, 5, 5);
        }
        .header {
            background: #fff;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            color: #C9184A;
            font-family: 'Merriweather', serif;
            font-size: 2em;
            letter-spacing: 1px;
        }
        .header .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .header .user-info span {
            color: #C9184A;
            font-weight: bold;
        }
        .header .user-info a {
            color: #fff;
            background: linear-gradient(90deg, #FF758F, #C9184A);
            text-decoration: none;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.3s;
            border: none;
            margin-left: 5px;
        }
        .header .user-info a:hover {
            background: linear-gradient(90deg, #C9184A, #FF758F);
        }
        .nav {
            background: #fff;
            padding: 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .nav ul {
            list-style: none;
            display: flex;
            gap: 0;
            margin: 0;
            padding: 0;
        }
        .nav a {
            color: rgb(82, 5, 5);
            text-decoration: none;
            padding: 18px 32px;
            display: block;
            font-family: 'Merriweather', serif;
            font-weight: 700;
            font-size: 1.1em;
            border-bottom: 3px solid transparent;
            transition: background 0.3s, color 0.3s, border-bottom 0.3s;
        }
        .nav a.active, .nav a:hover {
            background: linear-gradient(90deg, #FF758F, #C9184A);
            color: #fff;
            border-bottom: 3px solid #C9184A;
        }
        .main-content {
            padding: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .users-section {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            overflow: hidden;
            margin-bottom: 30px;
        }
        .section-header {
            padding: 20px 30px;
            border-bottom: 1px solid #eee;
            background: #f9f9f9;
        }
        .section-header h2 {
            color: #C9184A;
            font-size: 20px;
            font-family: 'Merriweather', serif;
        }
        .users-table {
            width: 100%;
            border-collapse: collapse;
        }
        .users-table th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 700;
            color: #C9184A;
            border-bottom: 1px solid #eee;
        }
        .users-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        .users-table tr:hover {
            background: #FFF0F5;
        }
        .action-btn {
            padding: 8px 18px;
            background: linear-gradient(90deg, #FF758F, #C9184A);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }
        .action-btn:hover {
            background: linear-gradient(90deg, #C9184A, #FF758F);
            transform: translateY(-2px);
        }
        .search-bar {
            text-align: center;
            margin-bottom: 20px;
        }
        .search-bar input {
            padding: 10px;
            border: 2px solid #C9184A;
            border-radius: 8px;
            width: 350px;
            font-size: 1em;
            margin-top: 10px;
        }
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            .nav ul {
                flex-direction: column;
            }
            .main-content {
                padding: 20px;
            }
            .users-table {
                font-size: 14px;
            }
            .users-table th,
            .users-table td {
                padding: 10px;
            }
            .search-bar input {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Yarn-Joy Admin</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admUsername']); ?></span>
            <a href="profile.php">Profile</a>
            <a href="change_password.php">Change Password</a>
            <a href="includes/logout.php">Logout</a>
        </div>
    </div>
    <div class="nav">
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="orders.php">All Orders</a></li>
            <li><a href="transactions.php">Transactions</a></li>
            <li><a href="users.php" class="active">Users</a></li>
            <li><a href="add_products.php">Add Products</a></li>
            <li><a href="browse.php">Browse Products</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="users-section">
            <div class="section-header">
                <h2>Users</h2>
            </div>
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search users...">
            </div>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="userTable">
                    <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr id='user-<?php echo $row["userId"]; ?>'>
                        <td><?php echo htmlspecialchars($row['userName']); ?></td>
                        <td><?php echo htmlspecialchars($row['Contact']); ?></td>
                        <td><?php echo htmlspecialchars($row['Email']); ?></td>
                        <td><?php echo htmlspecialchars($row['Address']); ?></td>
                        <td>
                            <button class="action-btn" onclick='deleteUser(<?php echo $row["userId"]; ?>)'>Delete</button>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        // Simple search filter
        $(document).ready(function(){
            $("#searchInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#userTable tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
        // Delete user function (AJAX)
        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                $.ajax({
                    url: 'includes/delete_user.php',
                    type: 'POST',
                    data: { userID: userId },
                    success: function(response) {
                        if (response.trim() === 'success') {
                            $('#user-' + userId).remove();
                        } else {
                            alert('Error deleting user: ' + response);
                        }
                    },
                    error: function() {
                        alert('Error deleting user.');
                    }
                });
            }
        }
    </script>
</body>
</html>