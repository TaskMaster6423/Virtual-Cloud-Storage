<?php
require_once 'includes/config.php';

function checkTable($conn, $tableName) {
    $result = mysqli_query($conn, "DESCRIBE $tableName");
    if (!$result) {
        echo "Table '$tableName' does not exist.<br>";
        return false;
    }
    
    echo "Table '$tableName' structure:<br>";
    echo "--------------------<br>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "{$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Key']}<br>";
    }
    echo "<br>";
    return true;
}

// Check required tables
$tables = ['users', 'login_attempts', 'backup_codes'];

foreach ($tables as $table) {
    checkTable($conn, $table);
}
?> 