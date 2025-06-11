<?php
session_start();
require_once 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get cart items
$cart_items = [];
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $sql = "SELECT * FROM books WHERE id IN ($ids)";
    $result = $conn->query($sql);
    while ($book = $result->fetch_assoc()) {
        $quantity = is_array($_SESSION['cart'][$book['id']]) ? $_SESSION['cart'][$book['id']]['quantity'] : $_SESSION['cart'][$book['id']];
        $book['quantity'] = (int)$quantity;
        $cart_items[] = $book;
    }
}

// Calculate totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$tax = $subtotal * 0.08;
$total = $subtotal + $tax;

// Get user info
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BookHaven - Checkout</title>
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
                    <a href="profile.php" class="profile-link">
                        <i class="fas fa-user"></i> <?php echo $_SESSION['username']; ?>
                    </a>
                    <button onclick="history.back()" class="back-button">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                </div>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="checkout-wrapper">
            <div class="checkout-details">
                <h2>Checkout</h2>
                <form action="process_order.php" method="POST" class="checkout-form" id="checkoutForm">
                    <div class="form-section">
                        <h3>Shipping Information</h3>
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" id="full_name" name="full_name" required value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($user['email']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" required value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="shipping_address" required>
                        </div>
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" name="shipping_city" required>
                        </div>
                        <div class="form-group">
                            <label for="postal">Postal Code</label>
                            <input type="text" id="postal" name="shipping_postal_code" required>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Payment Information</h3>
                        <div class="form-group">
                            <label for="card_name">Name on Card</label>
                            <input type="text" id="card_name" name="card_name" required>
                        </div>
                        <div class="form-group">
                            <label for="card_number">Card Number</label>
                            <div class="card-input-container">
                                <input type="text" id="card_number" name="card_number" maxlength="16" required>
                                <div class="card-icons">
                                    <i class="fab fa-cc-visa"></i>
                                    <i class="fab fa-cc-mastercard"></i>
                                    <i class="fab fa-cc-amex"></i>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="expiry">Expiry Date</label>
                                <input type="text" id="expiry" name="expiry" placeholder="MM/YY" maxlength="5" required>
                                <small id="expiry-error" style="color: red;"></small>
                            </div>
                            <div class="form-group">
                                <label for="cvv">CVV</label>
                                <input type="text" id="cvv" name="cvv" maxlength="3" required>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="total_amount" value="<?php echo $total; ?>">
                    <button type="submit" class="place-order-btn">
                        Place Order - RM <?php echo number_format($total, 2); ?>
                    </button>
                </form>
            </div>

            <div class="order-summary">
                <h3>Order Summary</h3>
                <div class="summary-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="summary-item">
                            <div class="item-info">
                                <span class="item-title"><?php echo htmlspecialchars($item['title']); ?></span>
                                <span class="item-quantity">×<?php echo $item['quantity']; ?></span>
                            </div>
                            <span class="item-price">RM <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="summary-calculations">
                    <div class="summary-subtotal">
                        <span>Subtotal</span>
                        <span>RM <?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="summary-tax">
                        <span>Tax (8%)</span>
                        <span>RM <?php echo number_format($tax, 2); ?></span>
                    </div>
                    <div class="summary-total">
                        <span>Total</span>
                        <span>RM <?php echo number_format($total, 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        © 2025 BookHaven. All rights reserved.
    </footer>

    <script>
    document.getElementById('card_number').addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '');
    });

    document.getElementById('expiry').addEventListener('input', function () {
        let value = this.value.replace(/\D/g, '');
        if (value.length >= 2) value = value.slice(0, 2) + '/' + value.slice(2);
        this.value = value;
        validateExpiry();
    });

    document.getElementById('cvv').addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '');
    });

    function validateExpiry() {
        const input = document.getElementById("expiry").value;
        const error = document.getElementById("expiry-error");
        error.textContent = "";
        const match = input.match(/^(\d{2})\/(\d{2})$/);
        if (!match) return true;
        const month = parseInt(match[1]);
        const year = 2000 + parseInt(match[2]);
        const now = new Date();
        const currentMonth = now.getMonth() + 1;
        const currentYear = now.getFullYear();
        if (month < 1 || month > 12 || year < currentYear || (year === currentYear && month < currentMonth)) {
            error.textContent = "❌ Expired card date.";
            return false;
        }
        return true;
    }

    document.getElementById("checkoutForm").addEventListener("submit", function (e) {
        if (!validateExpiry()) e.preventDefault();
    });
    </script>
</body>
</html>
















