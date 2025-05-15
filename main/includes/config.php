<?php
// Database credentials for MySQL
$servername = "localhost";
$username = "root";
$password = "Anurag";
$dbname = "cloud_storage";
$port = 3308;

// Attempt to connect to MySQL database
$conn = mysqli_connect($servername, $username, $password, $dbname, $port);

// Check connection
if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?> 