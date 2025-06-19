<?php
session_start();
require_once '../config.php';

// Handle mark as completed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $stmt = $conn->prepare("UPDATE orders SET status = 'Order Completed' WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Order #$order_id marked as completed.";
    } else {
        $_SESSION['error'] = "Failed to update order.";
    }
    header("Location: pending_orders.php");
    exit();
}

// Fetch pending orders
$sql = "SELECT o.*, u.username 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE o.status != 'Order Completed' 
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pending Orders - Admin</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 20px;
        }
        h2 {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ccc;
            vertical-align: top;
        }
        .mark-btn {
            padding: 5px 12px;
            background-color: #2ecc71;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }
        .mark-btn:hover {
            background-color: #27ae60;
        }
        .message-success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px 15px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
        }
        .message-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px 15px;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<?php include 'superadmin_header.php'; ?>

<div class="container">
    <h2>All Pending/Undelivered Orders</h2>

    <!-- Success/Error message -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="message-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php elseif (isset($_SESSION['error'])): ?>
        <div class="message-error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Book Title</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Order Date</th>
                    <th>Shipping Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['username']) ?></td>
                        <td><?= htmlspecialchars($order['book_title']) ?> ×<?= $order['quantity'] ?></td>
                        <td><?= $order['quantity'] ?></td>
                        <td><?= htmlspecialchars($order['status']) ?></td>
                        <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                        <td>
                            <?= htmlspecialchars($order['shipping_address']) ?><br>
                            <?= htmlspecialchars($order['shipping_city']) ?> <?= htmlspecialchars($order['shipping_postal_code']) ?>
                        </td>
                        <td>
                            <form method="POST" action="pending_orders.php" style="margin:0;">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <button type="submit" class="mark-btn">Mark as Completed</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No pending orders found.</p>
    <?php endif; ?>
</div>
</body>
</html>
