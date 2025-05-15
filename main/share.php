<?php
require_once 'includes/config.php';
require_once 'includes/session.php';

// Require full 3FA authentication
if (!isLoggedIn() || get3FAStatus() != 3) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';
$file_info = null;

if (isset($_GET['id'])) {
    $file_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];
    
    // Get file information
    $sql = "SELECT * FROM files WHERE file_id = ? AND user_id = ?";
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $file_id, $user_id);
        if(mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $file_info = mysqli_fetch_assoc($result);
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['share_email'])) {
    $share_email = trim($_POST['share_email']);
    
    // Validate email
    if (!filter_var($share_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        // Generate unique share link
        $share_token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+7 days'));
        
        $sql = "INSERT INTO file_shares (file_id, user_id, share_email, share_token, expires_at) 
                VALUES (?, ?, ?, ?, ?)";
        if($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "iisss", $file_id, $user_id, $share_email, $share_token, $expiry);
            if(mysqli_stmt_execute($stmt)) {
                // Log the share
                $ip = $_SERVER['REMOTE_ADDR'];
                $log_sql = "INSERT INTO file_access_logs (file_id, user_id, access_type, ip_address) VALUES (?, ?, 'share', ?)";
                $log_stmt = mysqli_prepare($conn, $log_sql);
                mysqli_stmt_bind_param($log_stmt, "iis", $file_id, $user_id, $ip);
                mysqli_stmt_execute($log_stmt);
                
                // Generate share link
                $share_link = "http://" . $_SERVER['HTTP_HOST'] . 
                             dirname($_SERVER['PHP_SELF']) . 
                             "/download-shared.php?token=" . $share_token;
                
                $success = "File shared successfully! Share link: " . $share_link;
                
                // In a production environment, you would send this link via email
                // to $share_email using a proper email service
            } else {
                $error = "Error sharing file. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share File - Virtual Reality Cloud Storage</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .share-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: var(--card-background);
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .share-form {
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: var(--text-color);
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
        }
        .share-link {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="share-container">
        <h2>Share File</h2>
        <?php if($file_info): ?>
            <div class="file-info">
                <p><strong>File:</strong> <?php echo htmlspecialchars($file_info['file_name']); ?></p>
            </div>
            
            <?php if(!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if(!empty($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form class="share-form" method="post">
                <div class="form-group">
                    <label for="share_email">Share with (email):</label>
                    <input type="email" id="share_email" name="share_email" required>
                </div>
                <button type="submit" class="btn download">Share File</button>
            </form>
            
            <div class="back-link" style="margin-top: 20px;">
                <a href="dashboard.php" class="btn">Back to Dashboard</a>
            </div>
        <?php else: ?>
            <div class="error-message">File not found or you don't have permission to share it.</div>
            <div class="back-link" style="margin-top: 20px;">
                <a href="dashboard.php" class="btn">Back to Dashboard</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 