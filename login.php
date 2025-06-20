<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
require_once 'config.php'; // your DB config
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if email exists in DB
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $error = "No account found with that email.";
    } else {
        $code = rand(100000, 999999); // generate 6-digit code
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_code'] = $code;

        $mail = new PHPMailer(true);
        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'bookhaven.my@gmail.com';       // ✅ Your Gmail
            $mail->Password = 'eorbzczqttuckmek';             // ✅ Your app password
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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Forgot Password</h2>
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <label for="email">Enter your email address:</label>
            <input type="email" name="email" required>
            <button type="submit">Send Reset Code</button>
        </form>
        <p><a href="login.php">Back to Login</a></p>
    </div>
</body>
</html>
