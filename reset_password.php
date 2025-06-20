<?php
session_start();
require_once 'config.php'; // for DB connection

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];

    // Server-side password check (as backup)
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        $error = "Password must be at least 8 characters, include uppercase, lowercase, and a number.";
    } else {
        $new_password = md5($password);
        $email = $_SESSION['reset_email'];

        $sql = "UPDATE users SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_password, $email);

        if ($stmt->execute()) {
            unset($_SESSION['reset_email']);
            unset($_SESSION['reset_code']);
            header("Location: success.php");
            exit();
        } else {
            $error = "Error updating password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .form-container {
            max-width: 400px;
            margin: 50px auto;
        }
        .error-message {
            color: red;
            margin-bottom: 10px;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 35px;
            cursor: pointer;
        }
        .password-wrapper {
            position: relative;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <form method="POST" onsubmit="return validatePassword()">
            <h2>Reset Password</h2>

            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <label for="password">New Password:</label>
            <div class="password-wrapper">
                <input type="password" id="password" name="password" required>
                <span class="toggle-password" onclick="togglePassword()">👁️</span>
            </div>
            <small>Password must be at least 8 characters, include an uppercase letter, lowercase letter, and a number.</small>

            <br><br>
            <button type="submit">Reset Password</button>
        </form>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        function validatePassword() {
            const password = document.getElementById('password').value;
            const pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

            if (!pattern.test(password)) {
                alert("Password must be at least 8 characters and include uppercase, lowercase, and a number.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
