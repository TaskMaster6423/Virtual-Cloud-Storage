<?php
require_once 'includes/config.php';
require_once 'includes/session.php';

// If user is already logged in and completed 3FA, redirect to dashboard
if (isLoggedIn() && get3FAStatus() == 3) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual Reality Cloud Storage</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Additional styles specific to landing page */
        .hero-section {
            background: var(--primary-color);
            color: var(--light-text);
            padding: 60px 20px;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .hero-section h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        .hero-section p {
            font-size: 1.2em;
            margin-bottom: 30px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .features-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            padding: 20px;
        }

        .feature-card {
            background: var(--card-background);
            padding: 30px;
            border-radius: 10px;
            box-shadow: var(--shadow);
            text-align: center;
            cursor: pointer;
            position: relative;
            transition: box-shadow 0.2s;
            outline: none;
            overflow: visible;
        }

        .feature-card h3 {
            color: var(--primary-color);
            margin-bottom: 0;
        }

        .feature-tray {
            display: block;
            pointer-events: none;
            opacity: 0;
            position: absolute;
            left: 50%;
            top: 100%;
            transform: translateX(-50%) translateY(16px);
            min-width: 320px;
            width: 90%;
            max-width: 420px;
            background: var(--card-background);
            box-shadow: 0 8px 32px rgba(76, 0, 130, 0.15);
            border-radius: 12px;
            padding: 24px 28px;
            z-index: 10;
            transition: opacity 0.3s, transform 0.3s;
        }

        .feature-card:hover,
        .feature-card:focus {
            box-shadow: 0 8px 32px rgba(76, 0, 130, 0.15);
        }

        .feature-card:hover .feature-tray,
        .feature-card:focus .feature-tray {
            opacity: 1;
            pointer-events: auto;
            transform: translateX(-50%) translateY(24px);
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
        }

        .cta-buttons .btn {
            width: auto;
            min-width: 150px;
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 40px 20px;
            }

            .hero-section h1 {
                font-size: 2em;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }

            .cta-buttons .btn {
                width: 100%;
                max-width: 300px;
            }

            .features-section {
                grid-template-columns: 1fr;
            }

            .feature-tray {
                left: 0;
                right: 0;
                min-width: unset;
                max-width: unset;
                width: 100vw;
                transform: translateY(8px);
                border-radius: 0 0 12px 12px;
                padding: 18px 8px;
            }

            .feature-card:hover .feature-tray,
            .feature-card:focus .feature-tray {
                transform: translateY(16px);
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Virtual Reality Cloud Storage</h1>
            <nav>
                <a href="login.php">Login</a>
                <a href="signup.php">Sign Up</a>
            </nav>
        </header>

        <section class="hero-section">
            <h1>Welcome to VR Cloud Storage</h1>
            <p>Experience the future of cloud storage with our cutting-edge virtual reality interface. 
               Store, manage, and share your files in a secure 3D environment with advanced three-factor authentication.</p>
            <div class="cta-buttons">
                <a href="signup.php" class="btn">Get Started</a>
                <a href="login.php" class="btn btn-secondary">Sign In</a>
            </div>
        </section>

        <section class="features-section" id="features-section">
            <div class="feature-card" tabindex="0">
                <h3>Three-Factor Authentication</h3>
                <div class="feature-tray">
                    <strong>Implementation:</strong>
                    <ul>
                        <li><b>Password:</b> User credentials are securely hashed and stored in the database. Login attempts are logged for security.</li>
                        <li><b>Phone Verification:</b> After password login, a code is sent to the user's phone (demo: code is 1234). User must enter this code to proceed.</li>
                        <li><b>Authenticator App:</b> After phone verification, a code from an authenticator app (demo: code is 1234) is required. Only after all three steps is access granted.</li>
                        <li>All steps are tracked in the session and logged in the <code>login_attempts</code> table.</li>
                    </ul>
                </div>
            </div>
            <div class="feature-card" tabindex="0">
                <h3>VR File Management</h3>
                <div class="feature-tray">
                    <strong>Implementation:</strong>
                    <ul>
                        <li>Files are stored in the <code>files</code> table, linked to each user.</li>
                        <li>File metadata (name, type, size, upload date) is managed in the database.</li>
                        <li>Files are displayed in a grid and can be managed (download, share, delete) from the dashboard.</li>
                        <li>Future VR integration can use this structure to render files in a 3D space.</li>
                    </ul>
                </div>
            </div>
            <div class="feature-card" tabindex="0">
                <h3>Secure File Sharing</h3>
                <div class="feature-tray">
                    <strong>Implementation:</strong>
                    <ul>
                        <li>Files can be shared by generating secure links or by specifying recipient users.</li>
                        <li>Access permissions are checked before allowing downloads.</li>
                        <li>All file access is logged for auditing.</li>
                        <li>Encryption is applied to files at rest and in transit (future enhancement).</li>
                    </ul>
                </div>
            </div>
            <div class="feature-card" tabindex="0">
                <h3>500MB File Upload</h3>
                <div class="feature-tray">
                    <strong>Implementation:</strong>
                    <ul>
                        <li>File uploads are handled via the dashboard with a 500MB size limit enforced in PHP.</li>
                        <li>Files are stored in the <code>uploads/</code> directory with unique names to prevent overwriting.</li>
                        <li>File metadata is saved in the <code>files</code> table for management and retrieval.</li>
                        <li>Large file uploads are validated and errors are shown if limits are exceeded.</li>
                    </ul>
                </div>
            </div>
            <div class="feature-card" tabindex="0">
                <h3>Real-time Collaboration</h3>
                <div class="feature-tray">
                    <strong>Implementation:</strong>
                    <ul>
                        <li>Collaboration features are planned for future updates.</li>
                        <li>Current system supports file sharing and access control for multiple users.</li>
                        <li>Real-time notifications and editing will be added using WebSockets or similar technology.</li>
                    </ul>
                </div>
            </div>
            <div class="feature-card" tabindex="0">
                <h3>Cross-Platform Access</h3>
                <div class="feature-tray">
                    <strong>Implementation:</strong>
                    <ul>
                        <li>Web-based dashboard is fully responsive and works on desktop, tablet, and mobile.</li>
                        <li>Future VR and mobile apps can connect to the same backend using secure APIs.</li>
                        <li>All user actions are protected by 3FA and session management for security.</li>
                    </ul>
                </div>
            </div>
        </section>
    </div>
</body>
</html> 