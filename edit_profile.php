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
        $error = "All fields including your current password are required.";
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
        /* [Style omitted for brevity, keep your existing CSS here] */

        .password-strength {
            font-size: 13px;
            margin-top: -15px;
            margin-bottom: 10px;
        }

        .weak {
            color: red;
        }

        .strong {
            color: green;
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
            <input type="password" name="current_password" required>

            <label for="password">New Password <small>(leave blank to keep current)</small></label>
            <input type="password" id="password" name="password" oninput="checkStrength()">
            <div id="strengthMessage" class="password-strength"></div>

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
function checkStrength() {
    const password = document.getElementById("password").value;
    const message = document.getElementById("strengthMessage");

    if (password.length === 0) {
        message.textContent = '';
        return;
    }

    const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

    if (!strongRegex.test(password)) {
        message.textContent = "Password should be at least 8 characters with uppercase, lowercase, and a number.";
        message.className = "password-strength weak";
    } else {
        message.textContent = "Strong password.";
        message.className = "password-strength strong";
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
</script>

</body>
</html>
