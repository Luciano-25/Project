<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current user data
$stmt = $conn->prepare("SELECT username, email, password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);
    $current_password_input = $_POST['current_password'];
    $new_password = $_POST['password'];

    if (empty($new_username) || empty($new_email)) {
        $error = "Username and email cannot be empty.";
    } elseif (md5($current_password_input) !== $user['password']) {
        $error = "Current password is incorrect.";
    } elseif (!empty($new_password) && md5($new_password) === $user['password']) {
        $error = "New password cannot be the same as the current password.";
    } elseif (!empty($new_password) && !preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $new_password)) {
        $error = "Password must be at least 8 characters, include an uppercase letter and a number.";
    } else {
        if (!empty($new_password)) {
            $hashed_password = md5($new_password);
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
            $stmt->bind_param("sssi", $new_username, $new_email, $hashed_password, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $new_username, $new_email, $user_id);
        }

        if ($stmt->execute()) {
            header("Location: profile.php?update=success");
            exit();
        } else {
            $error = "Failed to update profile. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile - BookHaven</title>
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
        .error, .success-message {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .error {
            background-color: #fcebea;
            color: #cc1f1a;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
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
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="profile-container">
    <div class="profile-card">
        <div class="profile-header">
            <h1>Edit Profile</h1>
            <p>You can update your username, email, or password here.</p>
        </div>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" class="edit-form">
            <label for="username">Username</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

            <label for="email">Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="current_password">Current Password</label>
            <div class="password-wrapper">
                <input type="password" name="current_password" id="current_password" required>
                <i class="fas fa-eye password-toggle" onclick="togglePassword('current_password', this)"></i>
            </div>

            <label for="password">New Password <small>(leave blank to keep current)</small></label>
            <div class="password-wrapper">
                <input type="password" name="password" id="new_password">
                <i class="fas fa-eye password-toggle" onclick="togglePassword('new_password', this)"></i>
            </div>
            <small id="password-requirements" style="color:red;"></small>

            <button type="submit" class="edit-profile-btn">
                <i class="fas fa-save"></i> Update Profile
            </button>
        </form>

        <a href="profile.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Profile
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

// Real-time password validation
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
