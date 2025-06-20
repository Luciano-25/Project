<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify'])) {
    $entered_code = $_POST['code'];

    if ($entered_code == $_SESSION['reset_code']) {
        header("Location: reset_password.php");
        exit();
    } else {
        $error = "Incorrect code. Please try again.";
    }
}

// Handle Resend Code
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['resend'])) {
    $email = $_SESSION['reset_email'] ?? '';

    if ($email) {
        $code = rand(100000, 999999);
        $_SESSION['reset_code'] = $code;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'bookhaven.my@gmail.com';
            $mail->Password = 'eorbzczqttuckmek';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('bookhaven.my@gmail.com', 'BookHaven');
            $mail->addAddress($email);
            $mail->Subject = 'BookHaven - New Password Reset Code';
            $mail->Body = "Your new password reset code is: $code";

            $mail->send();
            $success = "A new code has been sent to your email.";
        } catch (Exception $e) {
            $error = "Email could not be sent. Mailer Error: " . $mail->ErrorInfo;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Code - BookHaven</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            align-items: flex-start;
        }
        .profile-card {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 600px;
        }
        .profile-header h1 {
            font-size: 32px;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .profile-header p {
            color: #000;
            font-size: 15px;
            margin-bottom: 25px;
        }
        .edit-form label {
            font-weight: 600;
            color: #2c3e50;
            display: block;
            margin-bottom: 6px;
        }
        .edit-form input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            background-color: #fafafa;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .edit-form input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 6px rgba(52, 152, 219, 0.3);
            background-color: #fff;
        }
        .edit-profile-btn {
            background-color: #3498db;
            color: white;
            padding: 12px 22px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: background-color 0.3s ease;
        }
        .edit-profile-btn:hover {
            background-color: #2980b9;
        }
        .resend-btn {
            background-color: #bdc3c7;
            color: white;
            padding: 12px 22px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            cursor: not-allowed;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-left: 10px;
        }
        .resend-btn:enabled {
            cursor: pointer;
        }
        .resend-btn:enabled:hover {
            background-color: #2980b9;
        }
        .error, .success {
            background-color: #fcebea;
            color: #cc1f1a;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .success {
            background-color: #eafaf1;
            color: #2d995b;
        }
        #countdown {
            font-size: 14px;
            margin-left: 10px;
            color: #888;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="profile-container">
    <div class="profile-card">
        <div class="profile-header">
            <h1>Verify Code</h1>
            <p>Enter the 6-digit code sent to your email.</p>
        </div>

        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
        <?php if (isset($success)) echo "<div class='success'>$success</div>"; ?>

        <form method="POST" class="edit-form">
            <label for="code">Reset Code</label>
            <input type="text" name="code" id="code" maxlength="6">

            <button type="submit" name="verify" class="edit-profile-btn">
                <i class="fas fa-check-circle"></i> Verify Code
            </button>

            <button type="submit" name="resend" class="resend-btn" id="resendBtn" disabled>
                <i class="fas fa-sync-alt"></i> Resend Code
            </button>

            <span id="countdown">Available in 10s</span>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
    let timer = 10;
    const resendBtn = document.getElementById("resendBtn");
    const countdown = document.getElementById("countdown");

    const interval = setInterval(() => {
        timer--;
        countdown.textContent = "Available in " + timer + "s";

        if (timer <= 0) {
            clearInterval(interval);
            resendBtn.disabled = false;
            resendBtn.style.backgroundColor = "#3498db"; // Turn blue
            countdown.textContent = "";
        }
    }, 1000);
</script>

</body>
</html>
