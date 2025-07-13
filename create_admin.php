<?php
require('./includes/connection.php');

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>Admin Setup Script</h2>";

// Check if admin table exists
$check_table = "SHOW TABLES LIKE 'admin'";
$table_result = $conn->query($check_table);

if ($table_result->num_rows == 0) {
    echo "<p>Admin table does not exist. Creating it now...</p>";
    
    // Create admin table
    $create_table = "CREATE TABLE admin (
        adminId INT AUTO_INCREMENT PRIMARY KEY,
        adminName VARCHAR(100) NOT NULL,
        adminNumber VARCHAR(15) NOT NULL UNIQUE,
        adminPassword VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($create_table)) {
        echo "<p style='color: green;'>✓ Admin table created successfully!</p>";
    } else {
        echo "<p style='color: red;'>✗ Error creating admin table: " . $conn->error . "</p>";
        exit();
    }
} else {
    echo "<p style='color: green;'>✓ Admin table already exists.</p>";
}

// Check if admin users exist
$check_admin = "SELECT COUNT(*) as count FROM admin";
$admin_result = $conn->query($check_admin);
$admin_count = $admin_result->fetch_assoc()['count'];

if ($admin_count == 0) {
    echo "<p>No admin users found. Creating default admin user...</p>";
    
    // Create default admin user
    $admin_name = "admin";
    $admin_phone = "1234567890";
    $admin_password = "admin123"; // You should change this password
    
    $insert_admin = "INSERT INTO admin (adminName, adminNumber, adminPassword) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_admin);
    $stmt->bind_param("sss", $admin_name, $admin_phone, $admin_password);
    
    if ($stmt->execute()) {
        echo "<p style='color: green;'>✓ Default admin user created successfully!</p>";
        echo "<p><strong>Admin Login Credentials:</strong></p>";
        echo "<ul>";
        echo "<li><strong>Username/Phone:</strong> admin</li>";
        echo "<li><strong>Password:</strong> admin123</li>";
        echo "</ul>";
        echo "<p style='color: orange;'>⚠️ Please change the default password after first login!</p>";
    } else {
        echo "<p style='color: red;'>✗ Error creating admin user: " . $stmt->error . "</p>";
    }
    $stmt->close();
} else {
    echo "<p style='color: green;'>✓ Admin users already exist. Found $admin_count admin(s).</p>";
    
    // Show existing admin users
    $show_admins = "SELECT adminId, adminName, adminNumber FROM admin";
    $admins_result = $conn->query($show_admins);
    
    if ($admins_result->num_rows > 0) {
        echo "<p><strong>Existing Admin Users:</strong></p>";
        echo "<ul>";
        while ($admin = $admins_result->fetch_assoc()) {
            echo "<li>ID: " . $admin['adminId'] . " - Name: " . $admin['adminName'] . " - Phone: " . $admin['adminNumber'] . "</li>";
        }
        echo "</ul>";
    }
}

// Test database connection
echo "<h3>Database Connection Test</h3>";
if ($conn->ping()) {
    echo "<p style='color: green;'>✓ Database connection is working.</p>";
} else {
    echo "<p style='color: red;'>✗ Database connection failed.</p>";
}

// Show database info
echo "<h3>Database Information</h3>";
echo "<p><strong>Database:</strong> " . $conn->database . "</p>";
echo "<p><strong>Server:</strong> " . $conn->server_info . "</p>";

$conn->close();

echo "<hr>";
echo "<p><a href='index.php'>← Back to Home</a></p>";
echo "<p><a href='admin/index.php'>→ Go to Admin Panel</a></p>";
?> 