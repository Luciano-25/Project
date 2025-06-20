<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Reset Successful - BookHaven</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="profile.css">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            background-color: #f4f6f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .profile-container {
            width: 100%;
            min-height: 100vh;
            padding: 50px;
            box-sizing: border-box;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .profile-card {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }
        .profile-card h2 {
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .profile-card p {
            font-size: 16px;
            margin-bottom: 30px;
        }
        .login-link {
            background-color: #3498db;
            color: white;
            padding: 12px 22px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            display: inline-block;
            transition: background-color 0.3s ease;
        }
        .login-link:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="profile-container">
    <div class="profile-card">
        <h2>Password Changed Successfully!</h2>
        <p>You can now log in using your new password.</p>
        <a href="login.php" class="login-link">Login to BookHaven</a>
    </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
