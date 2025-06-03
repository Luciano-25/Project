<?php
include '../config.php';
include 'admin_header.php';

// Fetch all customers (role = 'user')
$sql = "SELECT id, username, email, created_at FROM users WHERE role = 'user' ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Customer List - Admin</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <div class="container">
        <h2>All Customers</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Registered On</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($customer = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($customer['id']); ?></td>
                        <td><?php echo htmlspecialchars($customer['username']); ?></td>
                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                        <td><?php echo date('d M Y', strtotime($customer['created_at'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4">No customers found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>
