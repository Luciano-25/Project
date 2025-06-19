<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the latest order details
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

// If no order is found, redirect or show a message
if (!$order) {
    $_SESSION['error'] = "No recent order found.";
    header("Location: profile.php");
    exit();
}

$review_link = "review.php?book_id=" . $order['book_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - BookHaven</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <div class="confirmation-container">
            <div class="confirmation-message">
                <i class="fas fa-check-circle"></i>
                <h2>Order Confirmed!</h2>
                <p>Thank you for your purchase. Your order has been successfully placed.</p>
            </div>

            <div class="shipping-details">
                <h3>Shipping To:</h3>
                <p><?= htmlspecialchars($order['shipping_address']) ?></p>
                <p><?= htmlspecialchars($order['shipping_city']) ?>, <?= htmlspecialchars($order['shipping_postal_code']) ?></p>
            </div>

            <div class="order-summary">
                <h3>Order Summary</h3>
                <p><strong>Book:</strong> <?= htmlspecialchars($order['book_title']) ?></p>
                <p><strong>Quantity:</strong> <?= $order['quantity'] ?></p>
                <p><strong>Total Price:</strong> RM <?= number_format($order['total_price'], 2) ?></p>
                <p><strong>Status:</strong> <?= ucfirst($order['status']) ?></p>
                <p><strong>Order Date:</strong> <?= date("F j, Y, g:i a", strtotime($order['created_at'])) ?></p>
            </div>

            <div class="review-prompt">
                <?php if ($order['status'] === 'Order Completed'): ?>
                    <p>You can now leave a review for this book!</p>
                    <a href="book_details.php?id=<?= $order['book_id'] ?>" class="review-btn">Leave a Review</a>
                <?php else: ?>
                    <p><strong>Note:</strong> You can only leave a review after you mark your order as <em>"Received"</em> in your <a href="profile.php">order history</a>.</p>
                <?php endif; ?>
            </div>

            <div class="confirmation-actions">
                <a href="profile.php" class="view-order-btn">View Order History</a>
                <a href="products.php" class="continue-shopping-btn">Continue Shopping</a>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
