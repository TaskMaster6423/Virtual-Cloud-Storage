<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/session.php';

// If user is already logged in and completed 3FA, redirect to dashboard
if (isLoggedIn() && get3FAStatus() == 3) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = trim($_POST['phone']);
    
    // Validate input
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($phone)) {
        $error = "Please fill in all fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        // Check if username exists
        $sql = "SELECT user_id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            throw new Exception("Prepare failed (username check): " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "This username is already taken.";
        } else {
            // Check if email exists
            $sql = "SELECT user_id FROM users WHERE email = ?";
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                throw new Exception("Prepare failed (email check): " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $error = "This email is already registered.";
            } else {
                // Begin transaction
                mysqli_begin_transaction($conn);
                try {
                    // Insert user
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $status = 'active';
                    $sql = "INSERT INTO users (username, email, password_hash, phone_number, account_status) VALUES (?, ?, ?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $sql);
                    if (!$stmt) {
                        throw new Exception("Prepare failed (user insert): " . mysqli_error($conn));
                    }
                    mysqli_stmt_bind_param($stmt, "sssss", $username, $email, $hashed_password, $phone, $status);
                    
                    if (!mysqli_stmt_execute($stmt)) {
                        throw new Exception("Error creating user account: " . ($stmt instanceof mysqli_stmt ? mysqli_stmt_error($stmt) : mysqli_error($conn)));
                    }
                    
                    $user_id = mysqli_insert_id($conn);
                    
                    // Generate and store backup codes
                    $backup_codes = array();
                    for ($i = 0; $i < 5; $i++) {
                        $backup_codes[] = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);
                    }
                    
                    foreach ($backup_codes as $code) {
                        $hashed_code = password_hash($code, PASSWORD_DEFAULT);
                        $sql = "INSERT INTO backup_codes (user_id, code_hash) VALUES (?, ?)";
                        $stmt = mysqli_prepare($conn, $sql);
                        if (!$stmt) {
                            throw new Exception("Prepare failed (backup code insert): " . mysqli_error($conn));
                        }
                        mysqli_stmt_bind_param($stmt, "is", $user_id, $hashed_code);
                        if (!mysqli_stmt_execute($stmt)) {
                            throw new Exception("Error generating backup codes: " . ($stmt instanceof mysqli_stmt ? mysqli_stmt_error($stmt) : mysqli_error($conn)));
                        }
                    }
                    
                    mysqli_commit($conn);
                    $success = "Account created successfully! Please login to continue.";
                    
                    // Store backup codes in session for display
                    $_SESSION['backup_codes'] = $backup_codes;
                    
                    // Redirect to backup codes display page
                    // header("Location: show-backup-codes.php");
                    // exit;
                    
                } catch (Exception $e) {
                    mysqli_rollback($conn);
                    $error = "Database error: " . $e->getMessage();
                    echo "<pre>EXCEPTION: " . $e->getMessage() . "</pre>";
                    echo "<pre>MYSQL ERROR: " . mysqli_error($conn) . "</pre>";
                    if (isset($stmt) && $stmt instanceof mysqli_stmt) {
                        echo "<pre>STMT ERROR: " . mysqli_stmt_error($stmt) . "</pre>";
                    }
                }
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
    <title>Sign Up - Virtual Reality Cloud Storage</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Virtual Reality Cloud Storage</h1>
            <nav>
                <a href="../index.php">Home</a>
                <a href="login.php">Login</a>
            </nav>
        </header>

        <div class="auth-container">
            <h2>Create Account</h2>
            <?php if(!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if(!empty($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required placeholder="e.g., +1234567890" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required minlength="8">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
                </div>
                
                <button type="submit" class="btn">Create Account</button>
            </form>
            
            <div class="links">
                <a href="login.php">Already have an account? Login</a>
            </div>
        </div>
    </div>
</body>
</html> 