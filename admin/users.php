<?php
    // Connect to the database and include the header
    require('../includes/connection.php');
    require('./includes/header.php');

    // Fetch all users from the database
    $sql = "SELECT * FROM users";
    $result = $conn->query($sql);

    // Check if the query was successful
    if ($result && $result->num_rows > 0) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - View Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        .search-bar {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            margin: 20px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            padding: 5px 10px;
            color: #fff;
            background-color: #f44336;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <h1>Users</h1>
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Search users...">
    </div>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Contact</th>
                <th>Gender</th>
                <th>DOB</th>
                <th>Citizenship No</th>
                <th>License No</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="userTable">
            <?php
            while ($row = $result->fetch_assoc()) {
            ?>
            <tr id="user-<?php echo $row['UserID']; ?>">
                <td><?php echo htmlspecialchars($row['Name']); ?></td>
                <td><?php echo htmlspecialchars($row['Contact']); ?></td>
                <td><?php echo htmlspecialchars($row['Gender']); ?></td>
                <td><?php echo htmlspecialchars($row['DOB']); ?></td>
                <td><?php echo htmlspecialchars($row['Citizenship_no']); ?></td>
                <td><?php echo htmlspecialchars($row['License_no']); ?></td>
                <td><button onclick="deleteUser(<?php echo $row['UserID']; ?>)">Delete</button></td>
            </tr>
            <?php 
            }
            ?>
        </tbody>
    </table>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        function deleteUser(userID) {
            if(confirm("Are you sure you want to delete this user?")) {
                $.ajax({
                    url: 'delete_user.php',
                    type: 'POST',
                    data: { userID: userID },
                    success: function(response) {
                        if(response === 'success') {
                            $('#user-' + userID).remove();
                        } else {
                            alert('Error deleting user.');
                        }
                    },
                    error: function() {
                        alert('Error deleting user.');
                    }
                });
            }
        }

        $(document).ready(function(){
            $('#searchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('#userTable tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });
        });
    </script>
</body>
</html>
<?php
} else {
    echo "<p>No users found or error: " . htmlspecialchars($conn->error) . "</p>";
}

$conn->close();
?>