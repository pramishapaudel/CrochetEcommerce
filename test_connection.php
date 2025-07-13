<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>Database Connection Test</h2>";

try {
    // Test connection with the same parameters as in connection.php
    $conn = new mysqli('localhost', 'root', '', 'yarn_joy', 3307);
    
    if ($conn->connect_error) {
        echo "<p style='color: red;'>✗ Connection failed: " . $conn->connect_error . "</p>";
    } else {
        echo "<p style='color: green;'>✓ Database connection successful!</p>";
        
        // Check if database exists
        $result = $conn->query("SHOW DATABASES LIKE 'yarn_joy'");
        if ($result->num_rows > 0) {
            echo "<p style='color: green;'>✓ Database 'yarn_joy' exists</p>";
            
            // Check if tables exist
            $tables = ['users', 'product', 'orders', 'admin', 'cart', 'cart_items'];
            foreach ($tables as $table) {
                $table_result = $conn->query("SHOW TABLES LIKE '$table'");
                if ($table_result->num_rows > 0) {
                    echo "<p style='color: green;'>✓ Table '$table' exists</p>";
                } else {
                    echo "<p style='color: orange;'>⚠ Table '$table' does not exist</p>";
                }
            }
        } else {
            echo "<p style='color: red;'>✗ Database 'yarn_joy' does not exist</p>";
            echo "<p>Available databases:</p>";
            $databases = $conn->query("SHOW DATABASES");
            while ($db = $databases->fetch_assoc()) {
                echo "<p>- " . $db['Database'] . "</p>";
            }
        }
        
        $conn->close();
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Exception: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Home</a></p>";
?> 