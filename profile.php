<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT *, DATE_FORMAT(created_at, '%M %Y') as member_since FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch order history with complete details
$sql = "SELECT o.*, b.title, b.price, o.quantity, o.total_amount, o.status, o.created_at
        FROM orders o
        LEFT JOIN books b ON o.book_id = b.id
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookHaven - Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="home.css">
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
                    <div class="info-item">
                        <span class="label">Username:</span>
                        <span class="value"><?php echo $user['username']; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Email:</span>
                        <span class="value"><?php echo $user['email']; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Member Since:</span>
                        <span class="value"><?php echo date('F Y', strtotime($user['created_at'])); ?></span>
                    </div>
    <div class="profile-actions">
    <a href="edit_profile.php" class="edit-btn">
        <i class="fas fa-edit"></i> Edit Profile
    </a>
    <a href="logout.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</div>

</div>

            </div>

            <div class="profile-section">
                <h2>Order History</h2>
                <div class="orders-list">
                    <?php if ($orders->num_rows > 0): ?>
                        <?php while($order = $orders->fetch_assoc()): ?>
                            <div class="order-card">
                            <div class="order-header">
                                <span class="order-id">Orders</span>
                                <span class="order-date"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                            </div>
                            <div class="order-details">
    <div class="book-info">
        <span class="book-title"><?php echo $order['book_title']; ?></span>
        <span class="quantity">Quantity: <?php echo $order['quantity']; ?></span>
    </div>
    <span class="order-total">Total: RM <?php echo number_format($order['total_amount'], 2); ?></span>
    <span class="order-status"><?php echo ucfirst($order['status']); ?></span>
</div>

                        </div>                        
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="no-orders">No orders yet</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
