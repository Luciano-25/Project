<?php
session_start();
require_once 'config.php';

// Get cart items
$cart_items = array();
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $sql = "SELECT * FROM books WHERE id IN ($ids)";
    $result = $conn->query($sql);
    while ($book = $result->fetch_assoc()) {
        $book['quantity'] = $_SESSION['cart'][$book['id']];
        $cart_items[] = $book;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookHaven - Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="top-header">
        <div class="header-container">
            <h1 class="site-title">BookHaven</h1>
            <nav class="nav-links">
    <a href="index.php">Home</a>
    <a href="products.php">Browse</a>
    <div class="nav-right">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="profile.php" class="profile-link">
                <i class="fas fa-user"></i>
                <?php echo $_SESSION['username']; ?>
            </a>
        <?php else: ?>
            <a href="login.php" class="login-link">
                <i class="fas fa-sign-in-alt"></i>
                Login
            </a>
        <?php endif; ?>
        <button onclick="history.back()" class="back-button">
            <i class="fas fa-arrow-left"></i> Back
        </button>
    </div>
</nav>

        </div>
    </header>

    <div class="container">
        <div class="cart-container">
            <?php if (empty($cart_items)): ?>
                <div class="empty-cart">
                    <h2>Your cart is empty</h2>
                    <a href="products.php" class="continue-shopping">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="cart-items">
                    <?php 
                    $subtotal = 0;
                    foreach ($cart_items as $item): 
                        $total = $item['price'] * $item['quantity'];
                        $subtotal += $total;
                    ?>
                        <div class="cart-item">
                            <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['title']; ?>">
                            <div class="item-details">
                                <h3><?php echo $item['title']; ?></h3>
                                <p><?php echo $item['author']; ?></p>
                            </div>
                            <form action="update_cart.php" method="POST" class="quantity-controls">
                                <input type="hidden" name="book_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="decrease" class="quantity-btn">-</button>
                                <span><?php echo $item['quantity']; ?></span>
                                <button type="submit" name="increase" class="quantity-btn">+</button>
                            </form>
                            <div class="price">RM <?php echo number_format($item['price'], 2); ?></div>
                            <div class="total">RM <?php echo number_format($total, 2); ?></div>
                            <form action="remove_from_cart.php" method="POST">
                                <input type="hidden" name="book_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="remove-btn">×</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>

                    <div class="cart-summary">
    <h3>Order Summary</h3>
    <div class="summary-item">
        <span>Subtotal</span>
        <span>RM <?php echo number_format($subtotal, 2); ?></span>
    </div>
    <div class="summary-item">
        <span>Tax (8%)</span>
        <span>RM <?php echo number_format($subtotal * 0.08, 2); ?></span>
    </div>
    <div class="summary-total">
        <span>Total</span>
        <span>RM <?php echo number_format($subtotal * 1.08, 2); ?></span>
    </div>
    <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
</div>

                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer class="footer">
        © 2025 BookHaven. All rights reserved.
    </footer>
</body>
</html>
