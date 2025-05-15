<?php
require_once 'includes/config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Verify token and get file info
    $sql = "SELECT f.*, fs.expires_at 
            FROM file_shares fs 
            JOIN files f ON fs.file_id = f.file_id 
            WHERE fs.share_token = ? AND fs.expires_at > NOW()";
            
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $token);
        if(mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if($file = mysqli_fetch_assoc($result)) {
                // Log the download
                $ip = $_SERVER['REMOTE_ADDR'];
                $log_sql = "INSERT INTO file_access_logs (file_id, user_id, access_type, ip_address) 
                           VALUES (?, NULL, 'shared_download', ?)";
                $log_stmt = mysqli_prepare($conn, $log_sql);
                mysqli_stmt_bind_param($log_stmt, "is", $file['file_id'], $ip);
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Shared File - Virtual Reality Cloud Storage</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="share-container">
        <h2>Download Shared File</h2>
        <div class="error-message">
            The shared file link is invalid or has expired. Please request a new share link from the file owner.
        </div>
        <div class="back-link" style="margin-top: 20px;">
            <a href="index.php" class="btn">Go to Homepage</a>
        </div>
    </div>
</body>
</html> 