<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Use MD5 hashing to match login system
        $hashed_password = MD5($password);

        // Check if email already exists
        $checkEmail = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $result = $checkEmail->get_result();

        if ($result->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            // Insert new user with user_type
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, user_type) VALUES (?, ?, ?, 'user')");
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                $success = "Registration successful! You can now log in.";
                header("refresh:2;url=login.php");
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookHaven - Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="top-header">
        <div class="header-container">
            <h1 class="site-title">BookHaven</h1>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="products.php">Browse</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="login-container">
            <h2>Create an Account</h2>
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <form action="register.php" method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
    <label for="password">Password</label>
    <div class="password-container">
        <input type="password" id="password" name="password" required>
        <i class="fas fa-eye toggle-password" onclick="togglePassword('password')"></i>
    </div>
</div>
<div class="form-group">
    <label for="confirm_password">Confirm Password</label>
    <div class="password-container">
        <input type="password" id="confirm_password" name="confirm_password" required>
        <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm_password')"></i>
    </div>
</div>
                <button type="submit" name="register" class="login-btn">Register</button>
            </form>
            <div class="register-link">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </div>

    <footer class="footer">
        © 2025 BookHaven. All rights reserved.
    </footer>
    <script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling;
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

</body>
</html>

