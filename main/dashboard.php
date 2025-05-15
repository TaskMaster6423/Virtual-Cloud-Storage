<?php
require_once 'includes/config.php';
require_once 'includes/session.php';

// Require full 3FA authentication
if (!isLoggedIn() || get3FAStatus() != 3) {
    header("Location: login.php");
    exit;
}

// Create uploads directory if it doesn't exist
$upload_dir = __DIR__ . '/uploads';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Get user's files
$user_id = $_SESSION['user_id'];
$files = array();
$sql = "SELECT * FROM files WHERE user_id = ? ORDER BY upload_date DESC";
if($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    if(mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        while($row = mysqli_fetch_assoc($result)) {
            $files[] = $row;
        }
    }
}

// Handle file upload
$upload_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $target_dir = __DIR__ . "/uploads/";
    $file_name = basename($_FILES["file"]["name"]);
    $target_file = $target_dir . $file_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check file size (500MB limit)
    if ($_FILES["file"]["size"] > 500000000) {
        $upload_message = "Sorry, your file is too large.";
    } else {
        // Create a unique filename to prevent overwriting
        $unique_file_name = uniqid() . '_' . $file_name;
        $target_file = $target_dir . $unique_file_name;
        
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $file_size = $_FILES["file"]["size"];
            $relative_path = "uploads/" . $unique_file_name;
            
            $sql = "INSERT INTO files (user_id, file_name, file_path, file_size, file_type, is_encrypted) 
                    VALUES (?, ?, ?, ?, ?, TRUE)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "issss", $user_id, $file_name, $relative_path, $file_size, $file_type);
            
            if (mysqli_stmt_execute($stmt)) {
                $upload_message = "File uploaded successfully.";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                $upload_message = "Error: Failed to save file information to database.";
            }
        } else {
            $upload_message = "Sorry, there was an error uploading your file.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Virtual Reality Cloud Storage</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
            <nav>
                <a href="settings.php">Settings</a>
                <a href="logout.php">Logout</a>
            </nav>
        </header>
        
        <section class="upload-section">
            <h2>Upload Files</h2>
            <?php if(!empty($upload_message)): ?>
                <div class="message <?php echo strpos($upload_message, 'successfully') !== false ? 'success-message' : 'error-message'; ?>">
                    <?php echo $upload_message; ?>
                </div>
            <?php endif; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" class="upload-form">
                <input type="file" name="file" required>
                <button type="submit" class="btn">Upload</button>
            </form>
        </section>
        
        <section class="files-section">
            <h2>Your Files</h2>
            <?php if (empty($files)): ?>
                <p class="no-files">No files uploaded yet.</p>
            <?php else: ?>
                <div class="files-grid">
                    <?php foreach($files as $file): ?>
                        <div class="file-card">
                            <h3><?php echo htmlspecialchars($file['file_name']); ?></h3>
                            <p>Size: <?php echo number_format($file['file_size'] / 1024 / 1024, 2); ?> MB</p>
                            <p>Uploaded: <?php echo date('Y-m-d H:i', strtotime($file['upload_date'])); ?></p>
                            <div class="file-actions">
                                <a href="download.php?id=<?php echo $file['file_id']; ?>" class="btn">Download</a>
                                <a href="share.php?id=<?php echo $file['file_id']; ?>" class="btn btn-secondary">Share</a>
                                <a href="delete.php?id=<?php echo $file['file_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</body>
</html> 