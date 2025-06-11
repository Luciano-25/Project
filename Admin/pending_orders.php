<?php
session_start();
require_once '../config.php';

// Only show orders that are not completed
$sql = "SELECT o.*, u.username 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE o.status != 'Order Completed' 
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);

// Handle admin manual update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $update = $conn->prepare("UPDATE orders SET status = 'Order Completed' WHERE id = ?");
    $update->bind_param("i", $order_id);
    $update->execute();
    header("Location: admin_pending_orders.php");
    exit();
}
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
    </style>
</head>
<body>
<?php include 'admin_header.php'; ?>

<div class="container">
    <h2>All Pending/Undelivered Orders</h2>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Book Title</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Order Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['username']) ?></td>
                        <td><?= htmlspecialchars($order['book_title']) ?></td>
                        <td><?= $order['quantity'] ?></td>
                        <td><?= htmlspecialchars($order['status']) ?></td>
                        <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                        <td>
                            <form method="POST" style="margin:0;">
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
