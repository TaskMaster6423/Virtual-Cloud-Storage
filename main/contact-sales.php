<?php
$submitted = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted = true;
    // You can process/store the form data here if needed
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Sales - Virtual Reality Cloud Storage</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: #f7f8fa;
        }
        .contact-container {
            max-width: 500px;
            margin: 60px auto;
            background: var(--card-background, #fff);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(76, 0, 130, 0.10);
            padding: 40px 30px 30px 30px;
        }
        .contact-container h2 {
            color: var(--primary-color, #4c0082);
            margin-bottom: 20px;
            text-align: center;
        }
        .contact-form label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-gray, #555);
            font-weight: 500;
        }
        .contact-form input,
        .contact-form textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            background: #fafbfc;
            transition: border 0.2s;
        }
        .contact-form input:focus,
        .contact-form textarea:focus {
            border-color: var(--primary-color, #4c0082);
            outline: none;
        }
        .contact-form textarea {
            min-height: 100px;
            resize: vertical;
        }
        .contact-form .btn {
            width: 100%;
            padding: 14px;
            font-size: 18px;
        }
        .thank-you {
            text-align: center;
            color: var(--primary-color, #4c0082);
            font-size: 1.3em;
            margin-top: 30px;
        }
        @media (max-width: 600px) {
            .contact-container {
                padding: 20px 8px;
            }
        }
    </style>
</head>
<body>
    <div class="contact-container">
        <h2>Contact Sales</h2>
        <?php if ($submitted): ?>
            <div class="thank-you">
                <p>Thank you for your interest!<br>Our sales team will contact you soon.</p>
                <a href="../index.php" class="btn btn-secondary" style="margin-top:20px;">Back to Home</a>
            </div>
        <?php else: ?>
        <form class="contact-form" method="post" action="">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>

            <label for="company">Company</label>
            <input type="text" id="company" name="company">

            <label for="message">Message</label>
            <textarea id="message" name="message" required></textarea>

            <button type="submit" class="btn">Send Message</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html> 