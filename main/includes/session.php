<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Debug: Uncomment to see session values
// echo '<pre>'; print_r($_SESSION); echo '</pre>';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

function get3FAStatus() {
    return isset($_SESSION['3fa_status']) ? $_SESSION['3fa_status'] : 0;
}
?> 