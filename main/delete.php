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
                // Log the deletion
                $ip = $_SERVER['REMOTE_ADDR'];
                $log_sql = "INSERT INTO file_access_logs (file_id, user_id, access_type, ip_address) VALUES (?, ?, 'delete', ?)";
                $log_stmt = mysqli_prepare($conn, $log_sql);
                mysqli_stmt_bind_param($log_stmt, "iis", $file_id, $user_id, $ip);
                mysqli_stmt_execute($log_stmt);
                
                // Delete the actual file
                $file_path = $file['file_path'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
                
                // Delete from database
                $delete_sql = "DELETE FROM files WHERE file_id = ? AND user_id = ?";
                $delete_stmt = mysqli_prepare($conn, $delete_sql);
                mysqli_stmt_bind_param($delete_stmt, "ii", $file_id, $user_id);
                mysqli_stmt_execute($delete_stmt);
            }
        }
    }
}

// Redirect back to dashboard
header("Location: dashboard.php");
exit;
?> 