<?php
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
            table {
                width: 100%;
                border-collapse: collapse;
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
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Gender</th>
                    <th>DOB</th>
                    <th>Citizenship no</th>
                    <th>License no</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result->fetch_assoc()) {
                ?>
                <tr id="user-<?php echo $row['UserID']; ?>">
                    <td><?php echo $row['Name']; ?></td>
                    <td><?php echo $row['Contact']; ?></td>
                    <td><?php echo $row['Gender']; ?></td>
                    <td><?php echo $row['DOB']; ?></td>
                    <td><?php echo $row['Citizenship_no']; ?></td>
                    <td><?php echo $row['License_no']; ?></td>
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
        </script>
    </body>
    </html>
<?php
} else {
    echo "No users found or error: " . $conn->error;
}

$conn->close();
?>
