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
$order = $stmt->get_result()->fetch_assoc();

// Update the review link to include book ID
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
            <div class="review-prompt">
                <h3>Enjoyed your books?</h3>
                <p>Share your thoughts with other readers!</p>
                <a href="review.php?order_id=<?php echo $order['id']; ?>" class="review-btn">Write a Review</a>
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
