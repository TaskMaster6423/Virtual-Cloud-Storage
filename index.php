<?php
session_start();
require_once 'main/includes/config.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['3fa_status']) && $_SESSION['3fa_status'] == 3;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual Reality Cloud Storage</title>
    <style>
        :root {
            --primary-color: #89CFF0;
            --dark-bg: #1a1c25;
            --text-light: #fff;
            --text-gray: #a0a0a0;
            --header-bg: rgba(26, 28, 37, 0.95);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        html, body {
            height: 100%;
            background-color: var(--dark-bg);
            color: var(--text-light);
            line-height: 1.6;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            position: relative;
        }

        /* Header Styles */
        header {
            background: var(--header-bg);
            padding: 15px 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .logo {
            font-size: 20px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .nav-links {
            display: flex;
            gap: 25px;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-light);
            text-decoration: none;
            font-size: 15px;
            padding: 8px 15px;
            border-radius: 4px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .nav-links a:not(.btn):hover {
            color: var(--primary-color);
        }

        /* Button Styles */
        .btn {
            padding: 8px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
            text-align: center;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--primary-color);
            color: var(--dark-bg);
            border: none;
        }

        .btn-primary:hover {
            background: #7ab8d6;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: transparent;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }

        .btn-secondary:hover {
            background: rgba(137, 207, 240, 0.1);
            transform: translateY(-1px);
        }

        /* Hero Section */
        .hero {
            padding-top: 80px;
            min-height: calc(100vh - 70px);
            display: flex;
            align-items: center;
            position: relative;
        }

        .hero-content {
            max-width: 550px;
            position: relative;
            z-index: 1;
        }

        h1 {
            font-size: 48px;
            font-weight: 600;
            margin-bottom: 20px;
            line-height: 1.2;
            color: var(--text-light);
        }

        .hero p {
            font-size: 18px;
            color: var(--text-gray);
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .cta-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        /* Features Section */
        .features {
            padding: 80px 0;
            background: rgba(137, 207, 240, 0.02);
        }

        .section-title {
            font-size: 32px;
            margin-bottom: 40px;
            text-align: center;
            color: var(--text-light);
        }

        /* Why Choose Section */
        .why-choose {
            padding: 60px 0;
        }

        .why-choose h2 {
            font-size: 32px;
            margin-bottom: 30px;
            color: var(--text-light);
        }

        .feature-item {
            margin-bottom: 25px;
            padding: 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            background: rgba(137, 207, 240, 0.05);
        }

        .feature-item h3 {
            color: var(--primary-color);
            font-size: 20px;
            margin-bottom: 8px;
        }

        .feature-item p {
            color: var(--text-gray);
            font-size: 15px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            nav {
                padding: 0 15px;
            }
            
            .nav-links {
                display: none;
            }
            
            h1 {
                font-size: 36px;
            }
            
            .hero-content {
                padding: 0 15px;
                text-align: center;
            }

            .cta-buttons {
                justify-content: center;
            }

            .feature-item {
                text-align: center;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .container {
                padding: 0 30px;
            }
            
            .hero-content {
                max-width: 500px;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="index.php" class="logo">Virtual Reality Cloud Storage</a>
            <div class="nav-links">
                <a href="main/dashboard.php">Dashboard</a>
                <a href="#features">Features</a>
                <a href="#pricing">Pricing</a>
                <?php if($isLoggedIn): ?>
                    <a href="main/dashboard.php" class="btn btn-primary">Dashboard</a>
                    <a href="main/logout.php">Log Out</a>
                <?php else: ?>
                    <a href="main/login.php">Log In</a>
                    <a href="main/signup.php" class="btn btn-primary">Sign Up for Free</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Visualize Your Digital Universe</h1>
                <p>Experience your files like never before. Explore, organize, and access your data through an intuitive and captivating visual interface.</p>
                <div class="cta-buttons">
                    <?php if($isLoggedIn): ?>
                        <a href="main/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                        <a href="main/upload.php" class="btn btn-secondary">Upload Files</a>
                    <?php else: ?>
                        <a href="main/signup.php" class="btn btn-primary">Sign Up for Free</a>
                        <a href="main/login.php" class="btn btn-secondary">Log In</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="why-choose">
        <div class="container">
            <h2>Why Choose VR Cloud Storage?</h2>
            
            <div class="feature-item">
                <h3>3-Factor Authentication</h3>
                <p>Military-grade security with our unique 3FA system.</p>
            </div>
            
            <div class="feature-item">
                <h3>VR File Navigation</h3>
                <p>Browse your files in an immersive 3D environment.</p>
            </div>
            
            <div class="feature-item">
                <h3>Unlimited Storage</h3>
                <p>Store as much as you want with our unlimited plans.</p>
            </div>
        </div>
    </section>

    <section id="features" class="features">
        <div class="container">
            <h2 class="section-title">More Than Just Storage</h2>
            <p style="text-align: center; color: var(--text-gray); margin-bottom: 40px;">
                We believe managing your digital life should be intuitive and even enjoyable. Our unique visual approach helps you understand and interact with your files in a new way.
            </p>
        </div>
    </section>
</body>
</html> 