<?php
require_once 'includes/config.php';
require_once 'includes/session.php';

// Require full 3FA authentication
if (!isLoggedIn() || get3FAStatus() != 3) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $file_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];
    
    // Check if file exists and belongs to user
    $sql = "SELECT * FROM files WHERE file_id = ? AND user_id = ?";
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $file_id, $user_id);
        if(mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if($file = mysqli_fetch_assoc($result)) {
                // Log the download
                $ip = $_SERVER['REMOTE_ADDR'];
                $log_sql = "INSERT INTO file_access_logs (file_id, user_id, access_type, ip_address) VALUES (?, ?, 'read', ?)";
                $log_stmt = mysqli_prepare($conn, $log_sql);
                mysqli_stmt_bind_param($log_stmt, "iis", $file_id, $user_id, $ip);
                mysqli_stmt_execute($log_stmt);
                
                // Send file to user
                $file_path = $file['file_path'];
                if (file_exists($file_path)) {
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="'.basename($file_path).'"');
                    header('Content-Length: ' . filesize($file_path));
                    readfile($file_path);
                    exit;
                }
            }
        }
    }
}

// If we get here, something went wrong
header("Location: dashboard.php");
exit;
?> 