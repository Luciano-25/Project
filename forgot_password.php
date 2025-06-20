<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $code = rand(100000, 999999); // 6-digit code

    $_SESSION['reset_email'] = $email;
    $_SESSION['reset_code'] = $code;

    $mail = new PHPMailer(true);

    try {
        // SMTP config
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bookhaven.my@gmail.com';              // ✅ Your BookHaven Gmail
        $mail->Password = 'eorbzczqttuckmek';                    // ✅ Your App Password (no spaces!)
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Email content
        $mail->setFrom('bookhaven.my@gmail.com', 'BookHaven');
        $mail->addAddress($email);
        $mail->Subject = 'BookHaven - Password Reset Code';
        $mail->Body = "Your password reset code is: $code";

        $mail->send();
        header("Location: verify_code.php");
        exit();

    } catch (Exception $e) {
        $error = "Email could not be sent. Mailer Error: " . $mail->ErrorInfo;
    }
}
?>
