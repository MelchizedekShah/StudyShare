<?php
// Prevent direct access to the file
if (!defined('SECURE_ACCESS')) {
    exit('Direct access not permitted');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #212121;
            color: #ecf0f1;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 70%;
            margin: 50px auto;
            padding: 70px;
            background: #2e373f;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }

        .header {
            background: #212121;
            color: #d1ddde;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .content {
            padding: 20px;
        }

        .button {
            display: block;
            width: fit-content;
            margin: 20px auto;
            padding: 8px 15px;
            background-color: #801212f0;
            color: #ecf0f1;
            border: 1px solid #212121;
            border-radius: 4px;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s;
        }

        .button:hover {
            background-color: #95a6b0;
            text-decoration: none;
            color: #ecf0f1;
        }

        a {
            color: #a1adb4;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
            color: #2980b9;
        }

        .footer {
            text-align: center;
            color: #9faab5;
            font-size: 12px;
            padding: 20px;
            border-top: 1px solid #212121;
        }

        @media (max-width: 768px) {
            .container {
                max-width: 95%;
                padding: 20px;
                margin: 20px auto;
            }

            .button {
                width: 100%;
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to StudyShare!</h1>
        </div>
        <div class="content">
            <h2>Verify Your Email Address</h2>
            <p>Thank you for joining StudyShare. Please verify your email address by clicking the button below:</p>
            <a href="<?php echo htmlspecialchars($verificationLink); ?>" class="button">Verify Email Address</a>
            <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
            <p><a href="<?php echo htmlspecialchars($verificationLink); ?>"><?php echo htmlspecialchars($verificationLink); ?></a></p>
            <p>This verification link will expire in 24 hours for security reasons.</p>
        </div>
        <div class="footer">
            <p>If you didn't create an account with StudyShare, please ignore this email.</p>
            <p>© <?php echo date('Y'); ?> StudyShare. All rights reserved.</p>
        </div>
    </div>
</body>
</html>