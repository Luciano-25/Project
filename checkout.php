<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

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

$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$tax = $subtotal * 0.08;
$total = $subtotal + $tax;

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Autofill shipping info from shipping_info table
$shipping_info = [
    'address' => '',
    'city' => '',
    'postal_code' => ''
];
$shipStmt = $conn->prepare("SELECT address, city, postal_code FROM shipping_info WHERE user_id = ? LIMIT 1");
$shipStmt->bind_param("i", $user_id);
$shipStmt->execute();
$shipResult = $shipStmt->get_result();
if ($shipResult->num_rows > 0) {
    $shipping_info = $shipResult->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BookHaven - Checkout</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .card-icons i {
            font-size: 1.5em;
            opacity: 0.3;
            transition: opacity 0.2s ease-in-out;
            margin-right: 6px;
            color: #888;
        }

        .card-icons i.active {
            opacity: 1;
            color: #007bff;
        }
    </style>
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
                            <input type="text" id="address" name="shipping_address" required value="<?php echo htmlspecialchars($shipping_info['address'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" name="shipping_city" required value="<?php echo htmlspecialchars($shipping_info['city'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="postal">Postal Code</label>
                            <input type="text" id="postal" name="shipping_postal_code" required value="<?php echo htmlspecialchars($shipping_info['postal_code'] ?? ''); ?>">
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
                                <input type="text" id="card_number" name="card_number" maxlength="19" required placeholder="#### #### #### ####">
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
    document.getElementById("card_number").addEventListener("input", function () {
        let raw = this.value.replace(/\D/g, '').slice(0, 16);
        let formatted = raw.replace(/(.{4})/g, '$1 ').trim();
        this.value = formatted;
        highlightCardIcon(raw);
    });

    function highlightCardIcon(cardNumber) {
        const visa = document.querySelector(".fa-cc-visa");
        const mastercard = document.querySelector(".fa-cc-mastercard");
        const amex = document.querySelector(".fa-cc-amex");

        visa.classList.remove("active");
        mastercard.classList.remove("active");
        amex.classList.remove("active");

        if (/^4/.test(cardNumber)) {
            visa.classList.add("active");
            setCVVLength(3);
        } else if (/^5[1-5]/.test(cardNumber) || /^2[2-7]/.test(cardNumber)) {
            mastercard.classList.add("active");
            setCVVLength(3);
        } else if (/^3[47]/.test(cardNumber)) {
            amex.classList.add("active");
            setCVVLength(4);
        } else {
            setCVVLength(3);
        }
    }

    function setCVVLength(length) {
        const cvvInput = document.getElementById("cvv");
        cvvInput.maxLength = length;
        if (cvvInput.value.length > length) {
            cvvInput.value = cvvInput.value.slice(0, length);
        }
    }

    document.getElementById('cvv').addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '');
    });

    document.getElementById('expiry').addEventListener('input', function () {
        let value = this.value.replace(/\D/g, '');
        if (value.length >= 2) value = value.slice(0, 2) + '/' + value.slice(2);
        this.value = value;
        validateExpiry();
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
        if (!validateExpiry()) {
            e.preventDefault();
        }
    });
    </script>
</body>
</html>
