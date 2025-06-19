<?php
session_start();
require_once '../config.php';

// Protect this page
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin' || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../login.php");
    exit();
}

// Add new admin
if (isset($_POST['add_admin'])) {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = md5($_POST['password']);

    $stmt = $conn->prepare("INSERT INTO users (full_name, username, email, phone, password, user_type, role) VALUES (?, ?, ?, ?, ?, 'admin', 'admin')");
    $stmt->bind_param("sssss", $full_name, $username, $email, $phone, $password);
    $stmt->execute();
    $success = "New admin added successfully!";
}

// Reset password
if (isset($_POST['reset_password'])) {
    $new_pass = md5($_POST['new_password']);
    $admin_id = $_POST['admin_id'];
    $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
    $stmt->bind_param("si", $new_pass, $admin_id);
    $stmt->execute();
    $success = "Password reset successfully.";
}

// Edit admin info
if (isset($_POST['edit_admin'])) {
    $id = $_POST['admin_id'];
    $full_name = $_POST['edit_full_name'];
    $email = $_POST['edit_email'];
    $phone = $_POST['edit_phone'];
    $stmt = $conn->prepare("UPDATE users SET full_name=?, email=?, phone=? WHERE id=?");
    $stmt->bind_param("sssi", $full_name, $email, $phone, $id);
    $stmt->execute();
    $success = "Admin info updated.";
}

// Delete admin
if (isset($_POST['delete_admin'])) {
    $id = $_POST['admin_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id=? AND role != 'superadmin'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $success = "Admin deleted.";
}

// Get all admins
$admins = $conn->query("SELECT * FROM users WHERE user_type='admin'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Admins</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        .container { max-width: 1100px; margin: auto; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        form { margin-bottom: 20px; }
        input, button { padding: 6px 10px; margin: 5px 0; }
        .btn-danger { background: #e74c3c; color: white; border: none; padding: 6px 10px; }
        .btn-edit { background: #f1c40f; color: black; border: none; }
        .btn-reset { background: #3498db; color: white; border: none; }
        .success { background: #2ecc71; color: white; padding: 10px; margin-bottom: 10px; }
    </style>
</head>
<body>
<?php include 'superadmin_header.php'; ?>

<div class="container">
    <h2>Manage Admins</h2>

    <?php if (isset($success)): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <h3>Add New Admin</h3>
    <form method="POST">
        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Phone" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="add_admin">Add Admin</button>
    </form>

    <h3>All Admins</h3>
    <table>
        <tr>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php while ($admin = $admins->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($admin['full_name']) ?></td>
                <td><?= htmlspecialchars($admin['email']) ?></td>
                <td><?= htmlspecialchars($admin['phone']) ?></td>
                <td><?= $admin['role'] ?></td>
                <td>
                    <form method="POST" style="display:inline-block;">
                        <input type="hidden" name="admin_id" value="<?= $admin['id'] ?>">
                        <input type="text" name="edit_full_name" value="<?= $admin['full_name'] ?>" required>
                        <input type="text" name="edit_email" value="<?= $admin['email'] ?>" required>
                        <input type="text" name="edit_phone" value="<?= $admin['phone'] ?>" required>
                        <button type="submit" name="edit_admin" class="btn-edit">Save</button>
                    </form>
                    <form method="POST" style="display:inline-block;">
                        <input type="hidden" name="admin_id" value="<?= $admin['id'] ?>">
                        <input type="password" name="new_password" placeholder="New Password" required>
                        <button type="submit" name="reset_password" class="btn-reset">Reset</button>
                    </form>
                    <?php if ($admin['role'] !== 'superadmin'): ?>
                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this admin?');" style="display:inline-block;">
                        <input type="hidden" name="admin_id" value="<?= $admin['id'] ?>">
                        <button type="submit" name="delete_admin" class="btn-danger">Delete</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
