<?php
session_start();
require_once 'config.php'; // for DB connection

if (!isset($_SESSION['reset_email'])) {
    // Don't allow direct access without session
    header("Location: forgot_password.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = md5($_POST['password']); // Hash new password
    $email = $_SESSION['reset_email'];

    $sql = "UPDATE users SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $new_password, $email);

    if ($stmt->execute()) {
        // Clear session and redirect
        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_code']);
        header("Location: success.php");
        exit();
    } else {
        echo "Error updating password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <form method="POST">
        <h2>Reset Password</h2>
        <label>New Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>