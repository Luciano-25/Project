<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get current user info
$stmt = $conn->prepare("SELECT username, email, password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);
    $new_password = $_POST['password'];
    $current_password_input = $_POST['current_password'];

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
            echo "<script>
                alert('Profile updated successfully!');
                window.location.href = 'profile.php';
            </script>";
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
        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 40px;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 12px;
            cursor: pointer;
            color: #888;
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

        <form method="POST" class="edit-form">
            <label for="username">Username</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

            <label for="email">Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="current_password">Current Password</label>
            <div class="password-wrapper">
                <input type="password" name="current_password" id="current_password" required>
                <i class="fas fa-eye toggle-password" toggle="#current_password"></i>
            </div>

            <label for="password">New Password <small>(leave blank to keep current)</small></label>
            <div class="password-wrapper">
                <input type="password" name="password" id="new_password">
                <i class="fas fa-eye toggle-password" toggle="#new_password"></i>
            </div>

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
    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', function () {
            const target = document.querySelector(this.getAttribute('toggle'));
            const type = target.getAttribute('type') === 'password' ? 'text' : 'password';
            target.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    });
</script>

</body>
</html>
