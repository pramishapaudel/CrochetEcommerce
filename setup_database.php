<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>Database Setup Script</h2>";

try {
    // First connect without specifying database
    $conn = new mysqli('localhost', 'root', '', '', 3307);
    
    if ($conn->connect_error) {
        echo "<p style='color: red;'>✗ Connection failed: " . $conn->connect_error . "</p>";
        exit();
    }
    
    echo "<p style='color: green;'>✓ Connected to MySQL server successfully!</p>";
    
    // Read and execute the SQL file
    $sql_file = 'sql/db.sql';
    if (file_exists($sql_file)) {
        echo "<p>Reading SQL file: $sql_file</p>";
        
        $sql_content = file_get_contents($sql_file);
        if ($sql_content === false) {
            echo "<p style='color: red;'>✗ Could not read SQL file</p>";
            exit();
        }
        
        // Split SQL into individual statements
        $statements = array_filter(array_map('trim', explode(';', $sql_content)));
        
        $success_count = 0;
        $error_count = 0;
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                if ($conn->query($statement)) {
                    $success_count++;
                    echo "<p style='color: green;'>✓ Executed: " . substr($statement, 0, 50) . "...</p>";
                } else {
                    $error_count++;
                    echo "<p style='color: orange;'>⚠ Error: " . $conn->error . " in statement: " . substr($statement, 0, 50) . "...</p>";
                }
            }
        }
        
        echo "<p><strong>Summary:</strong> $success_count statements executed successfully, $error_count errors</p>";
        
    } else {
        echo "<p style='color: red;'>✗ SQL file not found: $sql_file</p>";
    }
    
    // Test connection to the yarn_joy database
    $conn->close();
    $conn = new mysqli('localhost', 'root', '', 'yarn_joy', 3307);
    
    if ($conn->connect_error) {
        echo "<p style='color: red;'>✗ Could not connect to yarn_joy database: " . $conn->connect_error . "</p>";
    } else {
        echo "<p style='color: green;'>✓ Successfully connected to yarn_joy database!</p>";
        
        // Check tables
        $tables = ['users', 'admin', 'product', 'cart', 'cart_items', 'orders'];
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            if ($result->num_rows > 0) {
                echo "<p style='color: green;'>✓ Table '$table' exists</p>";
            } else {
                echo "<p style='color: red;'>✗ Table '$table' missing</p>";
            }
        }
        
        // Check admin user
        $admin_result = $conn->query("SELECT COUNT(*) as count FROM admin");
        $admin_count = $admin_result->fetch_assoc()['count'];
        echo "<p><strong>Admin users:</strong> $admin_count</p>";
        
        if ($admin_count > 0) {
            $admin_info = $conn->query("SELECT adminName, adminNumber FROM admin LIMIT 1");
            $admin = $admin_info->fetch_assoc();
            echo "<p><strong>Default admin:</strong> " . $admin['adminName'] . " (Phone: " . $admin['adminNumber'] . ")</p>";
        }
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Exception: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='test_connection.php'>→ Test Connection</a></p>";
echo "<p><a href='index.php'>→ Go to Home</a></p>";
echo "<p><a href='create_admin.php'>→ Create Admin (if needed)</a></p>";
?> 