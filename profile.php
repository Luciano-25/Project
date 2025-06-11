<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// If customer marked as received
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['mark_received'])) {
    $order_id = $_POST['order_id'];
    $update = $conn->prepare("UPDATE orders SET status = 'Order Completed' WHERE id = ? AND user_id = ?");
    $update->bind_param("ii", $order_id, $user_id);
    $update->execute();
}

// Fetch user data
$sql = "SELECT *, DATE_FORMAT(created_at, '%M %Y') as member_since FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch order history
$sql = "SELECT id, book_title, quantity, unit_price, total_price, shipping_address, shipping_city, shipping_postal_code, status, created_at 
        FROM orders 
        WHERE user_id = ? 
        ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BookHaven - Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="home.css">
    <style>
        .order-card {
            border: 1px solid #ccc;
            border-radius: 10px;
            margin-bottom: 15px;
            background: #fff;
            padding: 15px;
        }

        .order-header {
            font-weight: bold;
            color: #2c3e50;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
        }

        .order-details {
            display: none;
            margin-top: 15px;
            padding-left: 15px;
            border-top: 1px solid #eee;
        }

        .order-details p {
            margin: 5px 0;
        }

        .mark-received-btn {
            margin-top: 10px;
            padding: 6px 14px;
            background-color: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }

        .mark-received-btn:hover {
            background-color: #219150;
        }

        .no-orders {
            font-style: italic;
            color: #888;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="profile-container">
    <div class="profile-header">
        <div class="profile-info">
            <h1>Welcome, <?php echo $user['username']; ?></h1>
            <p><?php echo $user['email']; ?></p>
        </div>
    </div>

    <div class="profile-content">
        <div class="profile-section">
            <h2>Personal Information</h2>
            <div class="info-grid">
                <div class="info-item"><span class="label">Username:</span> <span class="value"><?php echo $user['username']; ?></span></div>
                <div class="info-item"><span class="label">Email:</span> <span class="value"><?php echo $user['email']; ?></span></div>
                <div class="info-item"><span class="label">Member Since:</span> <span class="value"><?php echo date('F Y', strtotime($user['created_at'])); ?></span></div>
                <div class="profile-actions">
                    <a href="edit_profile.php" class="edit-btn"><i class="fas fa-edit"></i> Edit Profile</a>
                    <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>

        <div class="profile-section">
            <h2>Order History</h2>
            <div class="orders-list">
                <?php if ($orders->num_rows > 0): ?>
                    <?php while($order = $orders->fetch_assoc()): ?>
                        <div class="order-card">
                            <div class="order-header" onclick="toggleDetails(this)">
                                <span>Order #<?php echo $order['id']; ?></span>
                                <span><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                            </div>
                            <div class="order-details">
                                <p><strong>Book Title:</strong> <?php echo htmlspecialchars($order['book_title']); ?></p>
                                <p><strong>Quantity:</strong> <?php echo $order['quantity']; ?></p>
                                <p><strong>Unit Price:</strong> RM <?php echo number_format($order['unit_price'], 2); ?></p>
                                <p><strong>Total:</strong> RM <?php echo number_format($order['total_price'], 2); ?></p>
                                <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order['shipping_address']) . ', ' . htmlspecialchars($order['shipping_city']) . ', ' . htmlspecialchars($order['shipping_postal_code']); ?></p>
                                <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
                                <?php if ($order['status'] !== 'Order Completed'): ?>
                                    <form method="POST">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <button type="submit" name="mark_received" class="mark-received-btn">Mark as Received</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-orders">No orders yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
    function toggleDetails(header) {
        const details = header.nextElementSibling;
        details.style.display = (details.style.display === 'block') ? 'none' : 'block';
    }
</script>

</body>
</html>
