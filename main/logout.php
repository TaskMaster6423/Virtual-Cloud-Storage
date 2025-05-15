<?php
require_once 'includes/config.php';
require_once 'includes/session.php';

// Log the logout
if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $sql = "INSERT INTO login_attempts (user_id, ip_address, status, factor_used) VALUES (?, ?, 'logout', 'all')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "is", $user_id, $ip);
    mysqli_stmt_execute($stmt);
}

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("location: login.php");
exit;
?> 