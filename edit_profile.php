<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT username, email, password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);
    $current_password_input = $_POST['current_password'];
    $new_password = $_POST['password'];

    if (empty($new_username) || empty($new_email) || empty($current_password_input)) {
        $error = "Username, email, and current password are required.";
    } elseif (!password_verify($current_password_input, $user['password'])) {
        $error = "Current password is incorrect.";
    } elseif (!empty($new_password) && password_verify($new_password, $user['password'])) {
        $error = "New password cannot be the same as the current password.";
    } else {
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
            $stmt->bind_param("sssi", $new_username, $new_email, $hashed_password, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $new_username, $new_email, $user_id);
        }

        if ($stmt->execute()) {
            $_SESSION['success'] = "Profile updated successfully!";
            header("Location: profile.php");
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

        #password-strength {
            font-size: 13px;
            margin-top: -15px;
            margin-bottom: 10px;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 40px;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }

        @media (max-width: 768px) {
            .profile-container {
                padding: 20px;
            }

            .profile-card {
                padding: 25px;
            }
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

        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" class="edit-form" onsubmit="return validateForm()">
            <label for="username">Username</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

            <label for="email">Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="current_password">Current Password</label>
            <div class="password-wrapper">
                <input type="password" name="current_password" id="current_password" required>
                <i class="fa fa-eye-slash toggle-password" toggle="#current_password"></i>
            </div>

            <label for="password">New Password <small>(leave blank to keep current)</small></label>
            <div class="password-wrapper">
                <input type="password" id="password" name="password" oninput="checkPasswordStrength()">
                <i class="fa fa-eye-slash toggle-password" toggle="#password"></i>
            </div>
            <div id="password-strength"></div>

            <button type="submit" class="edit-profile-btn">
                <i class="fas fa-save"></i> Update Profile
            </button>
        </form>

        <a href="profile.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Profile
        </a>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
function checkPasswordStrength() {
    const password = document.getElementById("password").value;
    const message = document.getElementById("password-strength");

    if (password === '') {
        message.textContent = '';
        return;
    }

    const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

    if (!strongRegex.test(password)) {
        message.textContent = "Password must be at least 8 characters with uppercase, lowercase, and a number.";
        message.style.color = "red";
    } else {
        message.textContent = "Strong password.";
        message.style.color = "green";
    }
}

function validateForm() {
    const password = document.getElementById("password").value;

    if (password.length > 0) {
        const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
        if (!strongRegex.test(password)) {
            alert("Your new password is not strong enough.");
            return false;
        }
    }
    return true;
}

// Toggle password visibility
document.querySelectorAll(".toggle-password").forEach(icon => {
    icon.addEventListener("click", function () {
        const target = document.querySelector(this.getAttribute("toggle"));
        if (target.type === "password") {
            target.type = "text";
            this.classList.remove("fa-eye-slash");
            this.classList.add("fa-eye");
        } else {
            target.type = "password";
            this.classList.remove("fa-eye");
            this.classList.add("fa-eye-slash");
        }
    });
});
</script>

</body>
</html>
