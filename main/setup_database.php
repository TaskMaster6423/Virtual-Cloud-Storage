<?php
require_once 'includes/config.php';

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS cloud_storage";
if (mysqli_query($conn, $sql)) {
    echo "Database checked/created successfully<br>";
} else {
    echo "Error creating database: " . mysqli_error($conn) . "<br>";
}

// Select the database
mysqli_select_db($conn, "cloud_storage");

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    account_status ENUM('active', 'suspended', 'deleted') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_username (username),
    INDEX idx_email (email)
)";

if (mysqli_query($conn, $sql)) {
    echo "Users table checked/created successfully<br>";
} else {
    echo "Error creating users table: " . mysqli_error($conn) . "<br>";
}

// Create authentication_factors table
$sql = "CREATE TABLE IF NOT EXISTS authentication_factors (
    factor_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    factor_type ENUM('password', 'phone', 'authenticator') NOT NULL,
    factor_value VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_factor (user_id, factor_type)
)";

if (mysqli_query($conn, $sql)) {
    echo "Authentication factors table checked/created successfully<br>";
} else {
    echo "Error creating authentication_factors table: " . mysqli_error($conn) . "<br>";
}

// Create login_attempts table
$sql = "CREATE TABLE IF NOT EXISTS login_attempts (
    attempt_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    status ENUM('success', 'failed') NOT NULL,
    factor_used VARCHAR(20) NOT NULL,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_attempts (user_id)
)";

if (mysqli_query($conn, $sql)) {
    echo "Login attempts table checked/created successfully<br>";
} else {
    echo "Error creating login_attempts table: " . mysqli_error($conn) . "<br>";
}

echo "Database setup complete. Please check for any errors above.<br>";
echo "<a href='signup.php'>Go to Signup</a> | <a href='login.php'>Go to Login</a>";
?> 