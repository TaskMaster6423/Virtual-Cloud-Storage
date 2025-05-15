<?php
require_once 'includes/config.php';
require_once 'includes/session.php';

// If user is already logged in and completed 3FA, redirect to dashboard
if (isLoggedIn() && get3FAStatus() == 3) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$username = '';
$password = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $sql = "SELECT * FROM users WHERE username = ? AND account_status = 'active'";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                if ($user = mysqli_fetch_assoc($result)) {
                    // Check password hash
                    if (password_verify($password, $user['password_hash'])) {
                        // Start session and set initial values
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['3fa_status'] = 1; // First factor complete

                        // Log the successful login attempt
                        $ip = $_SERVER['REMOTE_ADDR'];
                        $sql = "INSERT INTO login_attempts (user_id, ip_address, status, factor_used) VALUES (?, ?, 'success', 'password')";
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, "is", $user['user_id'], $ip);
                        mysqli_stmt_execute($stmt);

                        // Update last login time
                        $sql = "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE user_id = ?";
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, "i", $user['user_id']);
                        mysqli_stmt_execute($stmt);

                        header("Location: verify-phone.php");
                        exit;
                    } else {
                        // Log failed attempt
                        $ip = $_SERVER['REMOTE_ADDR'];
                        $sql = "INSERT INTO login_attempts (user_id, ip_address, status, factor_used) VALUES (?, ?, 'failed', 'password')";
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, "is", $user['user_id'], $ip);
                        mysqli_stmt_execute($stmt);

                        $error = "Invalid username or password.";
                    }
                } else {
                    $error = "Invalid username or password.";
                }
            } else {
                $error = "Oops! Something went wrong. Please try again later.";
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
    <title>Login - Virtual Reality Cloud Storage</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Virtual Reality Cloud Storage</h1>
            <nav>
                <a href="../index.php">Home</a>
                <a href="signup.php">Sign Up</a>
            </nav>
        </header>

        <div class="auth-container">
            <h2>Login</h2>
            <?php if(!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($username); ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
            <div class="links">
                <a href="forgot-password.php">Forgot Password?</a>
                <a href="signup.php">Create Account</a>
            </div>
        </div>
    </div>
</body>
</html> 