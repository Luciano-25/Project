<?php
session_start();
include '../config.php';
include 'admin_header.php';
require_once 'log_helper.php'; // ✅ Include logging

// Handle inline update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $id = $_POST['user_id'];
    $fullname = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("UPDATE users SET full_name=?, email=?, phone=? WHERE id=?");
    $stmt->bind_param("sssi", $fullname, $email, $phone, $id);
    if ($stmt->execute()) {
        log_admin_action($conn, $_SESSION['user_id'], "Updated customer info: ID $id");
    }
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $id = $_POST['user_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        log_admin_action($conn, $_SESSION['user_id'], "Deleted customer: ID $id");
    }
}

// Get search query
$search = $_GET['search'] ?? '';
if ($search) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_type != 'admin' AND (username LIKE ? OR email LIKE ?) ORDER BY created_at DESC");
    $like = "%$search%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $customers = $stmt->get_result();
} else {
    $customers = $conn->query("SELECT * FROM users WHERE user_type != 'admin' ORDER BY created_at DESC");
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer List - Admin</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        .container { max-width: 1200px; margin: auto; padding: 20px; }
        .styled-table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 3px 10px rgba(0,0,0,0.1); border-radius: 10px; overflow: hidden; }
        .styled-table th, .styled-table td { padding: 12px 15px; border-bottom: 1px solid #ddd; text-align: left; }
        .styled-table th { background-color: #2c3e50; color: #fff; }
        .customer-row { cursor: pointer; }
        .details-row { display: none; background: #f9f9f9; }
        .details-cell { padding: 15px; border-top: 1px solid #ccc; }
        .action-buttons button { margin-right: 10px; padding: 6px 10px; border-radius: 4px; border: none; cursor: pointer; }
        .edit-btn { background-color: #f39c12; color: white; }
        .delete-btn { background-color: #e74c3c; color: white; }
        .save-btn { background-color: #27ae60; color: white; }
        .cancel-btn { background-color: #7f8c8d; color: white; }
        .view-orders { margin-top: 10px; background: #eef; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>
<div class="container">
    <h2>All Customers</h2>

    <form method="get" class="search-form" style="margin-bottom: 20px;">
        <input type="text" name="search" class="search-input" placeholder="Search by Username or Email" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="search-button">Search</button>
    </form>

    <table class="styled-table">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($customers->num_rows > 0): ?>
            <?php while ($c = $customers->fetch_assoc()): ?>
                <tr class="customer-row" onclick="toggleDetails('details<?= $c['id']; ?>')">
                    <td><?= htmlspecialchars($c['username']) ?></td>
                    <td><?= htmlspecialchars($c['email']) ?></td>
                    <td><?= date('d M Y', strtotime($c['created_at'])) ?></td>
                    <td class="action-buttons">
                        <button class="edit-btn" onclick="event.stopPropagation(); toggleEdit('form<?= $c['id']; ?>')">Edit</button>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            <input type="hidden" name="user_id" value="<?= $c['id']; ?>">
                            <button type="submit" name="delete_user" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
               <tr id="details<?= $c['id']; ?>" class="details-row">
    <td colspan="4" class="details-cell">
        <div class="user-info">
            <strong>Full Name:</strong> <?= htmlspecialchars($c['full_name'] ?? 'N/A'); ?><br>
            <strong>Email:</strong> <?= htmlspecialchars($c['email']); ?><br>
            <strong>Phone:</strong> <?= htmlspecialchars($c['phone'] ?? 'N/A'); ?><br>
        </div>

        <!-- Edit form (hidden by default) -->
        <form method="POST" id="form<?= $c['id']; ?>" style="display:none; margin-top: 10px;">
            <input type="hidden" name="user_id" value="<?= $c['id']; ?>">
            <label>Full Name: <input type="text" name="full_name" value="<?= htmlspecialchars($c['full_name'] ?? ''); ?>"></label><br>
            <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($c['email']); ?>"></label><br>
            <label>Phone: <input type="text" name="phone" value="<?= htmlspecialchars($c['phone'] ?? ''); ?>"></label><br>
            <button type="submit" name="update_user" class="save-btn">Save</button>
            <button type="button" class="cancel-btn" onclick="toggleEdit('form<?= $c['id']; ?>')">Cancel</button>
        </form>

        <!-- Orders Section -->
        <div class="view-orders" style="margin-top: 10px;">
            <strong>Previous Orders:</strong><br>
            <?php
            $oid = $c['id'];
            $orders = $conn->prepare("SELECT book_title, quantity, total_price, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC");
            $orders->bind_param("i", $oid);
            $orders->execute();
            $results = $orders->get_result();
            if ($results->num_rows > 0) {
                while ($o = $results->fetch_assoc()) {
                    echo "<div>📘 <strong>" . htmlspecialchars($o['book_title']) . "</strong> ×" . $o['quantity'] .
                         " — RM " . number_format($o['total_price'], 2) .
                         " on " . date('d M Y', strtotime($o['created_at'])) . "</div>";
                }
            } else {
                echo "No orders found.";
            }
            ?>
        </div>
    </td>
</tr>

            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4" style="text-align:center;">No customers found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function toggleDetails(id) {
    const row = document.getElementById(id);
    row.style.display = row.style.display === 'table-row' ? 'none' : 'table-row';
}
function toggleEdit(formId) {
    const form = document.getElementById(formId);
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}
</script>
</body>
</html>
