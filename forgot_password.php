<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Start session
session_start();

// Include PHPMailer
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $code = rand(100000, 999999); // 6-digit code

    // Save code and email to session temporarily
    $_SESSION['reset_email'] = $email;
    $_SESSION['reset_code'] = $code;

    $mail = new PHPMailer(true);

    try {
        // SMTP config
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email@gmail.com';         // 🔁 Replace with your Gmail
        $mail->Password = 'your_app_password';            // 🔁 Replace with App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Email content
        $mail->setFrom('your_email@gmail.com', 'BookHaven');
        $mail->addAddress($email);
        $mail->Subject = 'BookHaven - Password Reset Code';
        $mail->Body = "Your password reset code is: $code";

        $mail->send();
        header("Location: verify_code.php"); // Move to next step
        exit();

    } catch (Exception $e) {
        $error = "Email could not be sent. Mailer Error: " . $mail->ErrorInfo;
    }
}
?>

<!-- HTML BELOW -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <form method="POST">
        <h2>Forgot Password</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <label>Email:</label>
        <input type="email" name="email" required>
        <button type="submit">Send Code</button>
    </form>
</body>
</html>
