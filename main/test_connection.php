<?php
require_once 'includes/config.php';

echo "Testing database connection...<br>";

if ($conn) {
    echo "Successfully connected to MySQL server!<br>";
    
    // Test database selection
    if (mysqli_select_db($conn, DB_NAME)) {
        echo "Successfully selected database: " . DB_NAME . "<br>";
        
        // Test query
        $result = mysqli_query($conn, "SHOW TABLES");
        if ($result) {
            echo "Tables in database:<br>";
            while ($row = mysqli_fetch_row($result)) {
                echo "- " . $row[0] . "<br>";
            }
        } else {
            echo "Error executing query: " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "Error selecting database: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "Connection failed: " . mysqli_connect_error() . "<br>";
}

echo "<br>Connection details:<br>";
echo "Server: " . DB_SERVER . "<br>";
echo "Username: " . DB_USERNAME . "<br>";
echo "Database: " . DB_NAME . "<br>";

// Close connection
mysqli_close($conn);
?> 