<?php
session_start();
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Handle resend code
if (isset($_POST['resend'])) {
    $code = rand(100000, 999999);
    $_SESSION['reset_code'] = $code;

    $email = $_SESSION['reset_email'];

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
        $message = "A new code has been sent to your email.";
    } catch (Exception $e) {
        $error = "Resend failed: " . $mail->ErrorInfo;
    }
}

// Handle code verification
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify'])) {
    $entered_code = $_POST['code'];

    if ($entered_code == $_SESSION['reset_code']) {
        header("Location: reset_password.php");
        exit();
    } else {
        $error = "Incorrect code. Please try again.";
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
        .btn-row {
            display: flex;
            gap: 10px;
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
            background-color: #95a5a6;
        }
        .resend-btn:hover {
            background-color: #7f8c8d;
        }
        .error, .message {
            background-color: #fcebea;
            color: #cc1f1a;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .message {
            background-color: #eafaf1;
            color: #2e7d32;
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
        <?php if (isset($message)) echo "<div class='message'>$message</div>"; ?>

        <form method="POST" class="edit-form">
            <label for="code">Reset Code</label>
            <input type="text" name="code" id="code" required maxlength="6">

            <div class="btn-row">
                <button type="submit" name="verify" class="edit-profile-btn">
                    <i class="fas fa-check-circle"></i> Verify Code
                </button>

                <button type="submit" name="resend" class="edit-profile-btn resend-btn" id="resendBtn" style="display: none;">
                    <i class="fas fa-sync-alt"></i> Resend Code
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
// Show the resend button after 10 seconds
setTimeout(() => {
    document.getElementById("resendBtn").style.display = "inline-flex";
}, 10000);
</script>

</body>
</html>
