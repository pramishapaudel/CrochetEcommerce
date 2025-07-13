<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

require('./includes/connection.php');

echo "<h2>Admin Users Check</h2>";

try {
    // Check if admin table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'admin'");
    if ($table_check->num_rows == 0) {
        echo "<p style='color: red;'>✗ Admin table does not exist!</p>";
        exit();
    }
    
    echo "<p style='color: green;'>✓ Admin table exists</p>";
    
    // Get all admin users
    $admin_query = "SELECT adminId, adminName, adminNumber, adminPassword FROM admin";
    $result = $conn->query($admin_query);
    
    if ($result->num_rows == 0) {
        echo "<p style='color: orange;'>⚠ No admin users found in the database</p>";
    } else {
        echo "<p style='color: green;'>✓ Found " . $result->num_rows . " admin user(s):</p>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Phone</th><th>Password</th></tr>";
        
        while ($admin = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $admin['adminId'] . "</td>";
            echo "<td>" . htmlspecialchars($admin['adminName']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['adminNumber']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['adminPassword']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Test the specific login credentials you're trying
    echo "<h3>Testing Your Login Credentials</h3>";
    $test_phone = "9777777777";
    $test_password = "17171717";
    
    $test_query = "SELECT * FROM admin WHERE adminNumber = ?";
    $stmt = $conn->prepare($test_query);
    $stmt->bind_param("s", $test_phone);
    $stmt->execute();
    $test_result = $stmt->get_result();
    
    if ($test_result->num_rows == 0) {
        echo "<p style='color: red;'>✗ No admin found with phone number: $test_phone</p>";
    } else {
        $admin = $test_result->fetch_assoc();
        echo "<p style='color: green;'>✓ Found admin with phone: $test_phone</p>";
        echo "<p>Admin Name: " . htmlspecialchars($admin['adminName']) . "</p>";
        echo "<p>Stored Password: " . htmlspecialchars($admin['adminPassword']) . "</p>";
        
        if ($admin['adminPassword'] === $test_password) {
            echo "<p style='color: green;'>✓ Password matches!</p>";
        } else {
            echo "<p style='color: red;'>✗ Password does not match!</p>";
            echo "<p>You entered: $test_password</p>";
            echo "<p>Stored: " . htmlspecialchars($admin['adminPassword']) . "</p>";
        }
    }
    $stmt->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

$conn->close();

echo "<hr>";
echo "<p><a href='index.php'>← Back to Home</a></p>";
?> 