<?php
include 'config.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($full_name) || empty($phone) || empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "❌ Please fill in all fields.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        $error = "❌ Password must be at least 8 characters, include one uppercase, one lowercase, and one number.";
    } elseif ($password !== $confirm_password) {
        $error = "❌ Passwords do not match.";
    } else {
        $hashed_password = md5($password);

        $checkEmail = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $result = $checkEmail->get_result();

        if ($result->num_rows > 0) {
            $error = "❌ Email is already registered.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (full_name, phone, username, email, password, user_type) VALUES (?, ?, ?, ?, ?, 'user')");
            $stmt->bind_param("sssss", $full_name, $phone, $username, $email, $hashed_password);

            if ($stmt->execute()) {
                $success = "✅ Registration successful! Redirecting to login...";
                header("refresh:2;url=login.php");
            } else {
                $error = "❌ Something went wrong. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BookHaven - Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .password-group {
            position: relative;
        }
        .toggle-password-all {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
        }
        .requirements {
            font-size: 0.9em;
            margin-top: 5px;
        }
        .requirements span {
            display: block;
            color: red;
        }
        .requirements span.valid {
            color: green;
        }
    </style>
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
        <?php if ($error): ?>
            <div class="error-message"><?= $error ?></div>
        <?php elseif ($success): ?>
            <div class="success-message"><?= $success ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="login-form" id="registerForm" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group password-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <i class="fas fa-eye toggle-password-all" onclick="togglePasswords()"></i>
                <div class="requirements" id="passwordRequirements">
                    <span id="length">❌ At least 8 characters</span>
                    <span id="uppercase">❌ At least one uppercase letter</span>
                    <span id="lowercase">❌ At least one lowercase letter</span>
                    <span id="number">❌ At least one number</span>
                </div>
            </div>

            <div class="form-group password-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" name="register" id="registerBtn" class="login-btn">Register</button>
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
function togglePasswords() {
    const pw1 = document.getElementById("password");
    const pw2 = document.getElementById("confirm_password");
    const icon = document.querySelector(".toggle-password-all");
    const type = pw1.type === "password" ? "text" : "password";

    pw1.type = type;
    pw2.type = type;

    icon.classList.toggle("fa-eye");
    icon.classList.toggle("fa-eye-slash");
}

const passwordInput = document.getElementById('password');
const confirmPasswordInput = document.getElementById('confirm_password');

function validatePasswordLive() {
    const pw = passwordInput.value;

    document.getElementById("length").className = pw.length >= 8 ? "valid" : "";
    document.getElementById("length").textContent = pw.length >= 8 ? "✅ At least 8 characters" : "❌ At least 8 characters";

    document.getElementById("uppercase").className = /[A-Z]/.test(pw) ? "valid" : "";
    document.getElementById("uppercase").textContent = /[A-Z]/.test(pw) ? "✅ At least one uppercase letter" : "❌ At least one uppercase letter";

    document.getElementById("lowercase").className = /[a-z]/.test(pw) ? "valid" : "";
    document.getElementById("lowercase").textContent = /[a-z]/.test(pw) ? "✅ At least one lowercase letter" : "❌ At least one lowercase letter";

    document.getElementById("number").className = /\d/.test(pw) ? "valid" : "";
    document.getElementById("number").textContent = /\d/.test(pw) ? "✅ At least one number" : "❌ At least one number";
}

passwordInput.addEventListener('input', validatePasswordLive);

function validateForm() {
    const pw = passwordInput.value;
    const cpw = confirmPasswordInput.value;
    const allFields = ['full_name', 'phone', 'username', 'email', 'password', 'confirm_password'];
    for (let field of allFields) {
        if (document.getElementById(field).value.trim() === '') {
            alert("❌ Please fill in all fields.");
            return false;
        }
    }

    const strongPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/.test(pw);
    if (!strongPassword) {
        alert("❌ Password must meet all requirements.");
        return false;
    }

    if (pw !== cpw) {
        alert("❌ Passwords do not match.");
        return false;
    }

    return true;
}
</script>
</body>
</html>
