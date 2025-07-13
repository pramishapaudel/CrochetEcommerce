<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>Database Configuration Check</h2>";

// Test different connection configurations
$configs = [
    ['localhost', 'root', '', 'yarn_joy', 3307],
    ['127.0.0.1', 'root', '', 'yarn_joy', 3307],
    ['localhost', 'root', '', '', 3307], // No database specified
    ['127.0.0.1', 'root', '', '', 3307], // No database specified
];

foreach ($configs as $i => $config) {
    list($host, $user, $pass, $db, $port) = $config;
    
    echo "<h3>Test " . ($i + 1) . ": $host:$port, user=$user, db=$db</h3>";
    
    try {
        $conn = new mysqli($host, $user, $pass, $db, $port);
        
        if ($conn->connect_error) {
            echo "<p style='color: red;'>✗ Failed: " . $conn->connect_error . "</p>";
        } else {
            echo "<p style='color: green;'>✓ Connected successfully!</p>";
            
            if (!empty($db)) {
                echo "<p>Database '$db' exists and is accessible.</p>";
            } else {
                echo "<p>Connected to MySQL server. Available databases:</p>";
                $result = $conn->query("SHOW DATABASES");
                while ($row = $result->fetch_assoc()) {
                    echo "<p>- " . $row['Database'] . "</p>";
                }
            }
            
            $conn->close();
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Exception: " . $e->getMessage() . "</p>";
    }
    
    echo "<hr>";
}

echo "<p><a href='index.php'>← Back to Home</a></p>";
?> 