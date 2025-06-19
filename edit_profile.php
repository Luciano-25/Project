<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current user data
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);
    $new_password = $_POST['password'];

    if (empty($new_username) || empty($new_email)) {
        $error = "Username and email cannot be empty.";
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
            $success = "Profile updated successfully!";
            $user['username'] = $new_username;
            $user['email'] = $new_email;
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
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f6f8;
            font-family: Arial, sans-serif;
        }

        .profile-container {
            width: 100%;
            min-height: 100vh;
            padding: 40px 60px;
            box-sizing: border-box;
            background-color: #ffffff;
        }

        .profile-header h1 {
            font-size: 32px;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .profile-header p {
            color: #666;
            font-size: 15px;
            margin-bottom: 30px;
        }

        .edit-form {
            max-width: 600px;
        }

        .edit-form label {
            font-weight: 600;
            color: #34495e;
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
            transition: 0.3s;
        }

        .edit-form input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.2);
        }

        .edit-profile-btn {
            background-color: #3498db;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .edit-profile-btn:hover {
            background-color: #2980b9;
        }

        .back-link {
            display: inline-block;
            margin-top: 30px;
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            transition: color 0.3s;
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

        @media (max-width: 768px) {
            .profile-container {
                padding: 20px;
            }

            .edit-form {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="profile-container">
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

        <label for="password">New Password <small>(leave blank to keep current)</small></label>
        <input type="password" name="password">

        <button type="submit" class="edit-profile-btn">
            <i class="fas fa-save"></i> Update Profile
        </button>
    </form>

    <a href="profile.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to Profile
    </a>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
