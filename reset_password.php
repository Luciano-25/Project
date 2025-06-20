<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];

    if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        $error = "Password must be at least 8 characters, include an uppercase letter and a number.";
    } else {
        $hashed_password = md5($password);
        $email = $_SESSION['reset_email'];

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);

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
    <title>Reset Password - BookHaven</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="profile.css">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
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
            background-color: #f4f6f8;
        }
        .profile-card {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 700px;
        }
        .profile-header h1 {
            font-size: 32px;
            color: #2c3e50;
            margin-bottom: 8px;
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
        .error {
            background-color: #fcebea;
            color: #cc1f1a;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
        }
        .password-wrapper {
            position: relative;
        }
        #password-requirements {
            margin-top: -15px;
            margin-bottom: 15px;
            font-size: 13px;
        }
        .back-link {
            display: inline-block;
            margin-top: 25px;
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
        }
        .back-link i {
            margin-right: 6px;
        }
        .back-link:hover {
            color: #1f6dbb;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="profile-container">
    <div class="profile-card">
        <div class="profile-header">
            <h1>Reset Password</h1>
            <p>Enter your new password below.</p>
        </div>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" class="edit-form">
            <label for="password">New Password</label>
            <div class="password-wrapper">
                <input type="password" name="password" id="new_password" required>
                <i class="fas fa-eye password-toggle" onclick="togglePassword('new_password', this)"></i>
            </div>
            <small id="password-requirements" style="color:red;"></small>

            <button type="submit" class="edit-profile-btn">
                <i class="fas fa-unlock-alt"></i> Reset Password
            </button>
        </form>

        <a href="login.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Login
        </a>
    </div>
</div>

<script>
function togglePassword(id, icon) {
    const input = document.getElementById(id);
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}

document.getElementById('new_password').addEventListener('input', function () {
    const password = this.value;
    const requirements = document.getElementById('password-requirements');
    const regex = /^(?=.*[A-Z])(?=.*\d).{8,}$/;
    if (password.length === 0) {
        requirements.textContent = '';
    } else if (!regex.test(password)) {
        requirements.textContent = 'Password must be at least 8 characters, include an uppercase letter and a number.';
        requirements.style.color = 'red';
    } else {
        requirements.textContent = 'Password meets requirements.';
        requirements.style.color = 'green';
    }
});
</script>

<?php include 'footer.php'; ?>

</body>
</html>
