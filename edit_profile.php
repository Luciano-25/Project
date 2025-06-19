<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$sql = "SELECT username, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle form submission
$success = "";
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);
    $new_password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    if (!empty($new_username) && !empty($new_email)) {
        if ($new_password) {
            $update_sql = "UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("sssi", $new_username, $new_email, $new_password, $user_id);
        } else {
            $update_sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("ssi", $new_username, $new_email, $user_id);
        }

        if ($stmt->execute()) {
            $success = "Profile updated successfully!";
            $_SESSION['message'] = $success;
            header("Location: profile.php");
            exit();
        } else {
            $error = "Something went wrong. Please try again.";
        }
    } else {
        $error = "Username and Email cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile - BookHaven</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(to right, #f0f0f0, #e6e6e6);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .edit-container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .edit-container h2 {
            margin-bottom: 20px;
            color: #2c3e50;
            font-size: 26px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #000;
        }

        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
            border-color: #3498db;
            outline: none;
        }

        .btn-group {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
        }

        .btn {
            padding: 10px 20px;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
        }

        .btn-save {
            background-color: #2ecc71;
            color: white;
        }

        .btn-save:hover {
            background-color: #27ae60;
        }

        .btn-back {
            background-color: #95a5a6;
            color: white;
        }

        .btn-back:hover {
            background-color: #7f8c8d;
        }

        .message {
            margin-top: 15px;
            font-weight: bold;
            color: green;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

        @media (max-width: 640px) {
            .edit-container {
                margin: 20px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="edit-container">
    <h2><i class="fas fa-user-edit"></i> Edit Your Profile</h2>

    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>

        <div class="form-group">
            <label for="password">New Password <small>(Leave blank to keep current)</small>:</label>
            <input type="password" name="password" id="password">
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Save Changes</button>
            <a href="profile.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Back to Profile</a>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
