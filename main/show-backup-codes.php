<?php
session_start();

// Check if backup codes exist in session
if (!isset($_SESSION['backup_codes'])) {
    header("Location: login.php");
    exit;
}

$backup_codes = $_SESSION['backup_codes'];
// Clear the backup codes from session after displaying
unset($_SESSION['backup_codes']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Codes - Virtual Reality Cloud Storage</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .backup-codes {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            font-family: monospace;
            font-size: 1.2em;
        }
        .backup-codes code {
            display: block;
            margin: 10px 0;
            color: var(--primary-color);
        }
        .warning {
            color: #dc3545;
            margin: 20px 0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Virtual Reality Cloud Storage</h1>
            <nav>
                <a href="login.php">Continue to Login</a>
            </nav>
        </header>

        <div class="auth-container">
            <h2>Save Your Backup Codes</h2>
            <p class="warning">Important: Save these backup codes in a secure location. They will not be shown again!</p>
            
            <div class="backup-codes">
                <?php foreach($backup_codes as $code): ?>
                    <code><?php echo htmlspecialchars($code); ?></code>
                <?php endforeach; ?>
            </div>
            
            <p>You can use these codes to recover your account if you lose access to your phone or authenticator app.</p>
            
            <div class="links">
                <a href="login.php" class="btn">Continue to Login</a>
            </div>
        </div>
    </div>
</body>
</html> 