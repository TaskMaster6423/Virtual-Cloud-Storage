<?php
require_once 'includes/config.php';
require_once 'includes/session.php';

if (!isLoggedIn() || get3FAStatus() != 1) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = trim($_POST['code']);
    $user_id = $_SESSION['user_id'];
    
    // For demo purposes, we'll accept "1234" as the valid code
    if ($code === "1234") {
        $_SESSION['3fa_status'] = 2; // Second factor complete
        
        // Log the successful verification
        $ip = $_SERVER['REMOTE_ADDR'];
        $sql = "INSERT INTO login_attempts (user_id, ip_address, status, factor_used) VALUES (?, ?, 'success', 'phone')";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "is", $user_id, $ip);
        mysqli_stmt_execute($stmt);
        
        header("location: verify-authenticator.php");
        exit;
    } else {
        $error = "Invalid verification code.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Verification - Virtual Reality Cloud Storage</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Virtual Reality Cloud Storage</h1>
            <nav>
                <a href="logout.php">Cancel</a>
                <a href="../index.php">Home</a>
            </nav>
        </header>

        <div class="auth-container">
            <h2>Phone Verification</h2>
            <?php if(!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if(!empty($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <p class="auth-message">Please enter the verification code sent to your phone.</p>
            <p class="auth-message">For demo purposes, use code: 1234</p>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="code">Verification Code</label>
                    <input type="text" id="code" name="code" required maxlength="4" placeholder="Enter 4-digit code" pattern="[0-9]{4}" inputmode="numeric">
                </div>
                
                <button type="submit" class="btn">Verify</button>
            </form>
            
            <div class="links">
                <a href="resend-code.php">Resend Code</a>
                <a href="logout.php">Cancel Login</a>
            </div>
        </div>
    </div>
</body>
</html> 