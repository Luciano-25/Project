<?php
session_start();
require_once 'config.php'; // for DB connection

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = md5($_POST['password']); // Simple hash for now
    $email = $_SESSION['reset_email'];

    $sql = "UPDATE users SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $new_password, $email);

    if ($stmt->execute()) {
        // Clear session data related to reset
        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_code']);

        // Redirect to success page
        header("Location: success.php");
        exit();
    } else {
        $error = "Error updating password. Please try again.";
    }
}
?>
