<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Mark order as received
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['mark_received'])) {
    $order_id = $_POST['order_id'];
    $update = $conn->prepare("UPDATE orders SET status = 'Order Completed' WHERE id = ? AND user_id = ?");
    $update->bind_param("ii", $order_id, $user_id);
    $update->execute();

    $_SESSION['message'] = "Order marked as received successfully!";
    header("Location: profile.php");
    exit();
}

// Fetch user data
$sql = "SELECT *, DATE_FORMAT(created_at, '%M %Y') as member_since FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch order history
$sql = "SELECT id, book_id, book_title, quantity, unit_price, total_price, shipping_address, shipping_city, shipping_postal_code, status, created_at 
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
        .leave-review-btn {
            background-color: #3498db;
        }
        .mark-received-btn:hover {
            opacity: 0.9;
        }
        .no-orders {
            font-style: italic;
            color: #888;
        }
        .success-message {
            padding: 10px;
            background: #d4edda;
            color: #155724;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        /* Modal Styling */
        #invoiceModal {
            display: none;
            position: fixed;
            top: 10%;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            z-index: 9999;
            max-width: 500px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        #invoiceModal button {
            margin-top: 10px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="profile-container">
    <div class="profile-header">
        <div class="profile-info">
            <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?></h1>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
        </div>
    </div>

    <div class="profile-content">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="success-message">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

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
                                 <span><?php echo htmlspecialchars($order['book_title']); ?></span>
                                 <span><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                            </div>

                            <div class="order-details">
                                <p><strong>Book Title:</strong> <?php echo htmlspecialchars($order['book_title']); ?></p>
                                <p><strong>Quantity:</strong> <?php echo $order['quantity']; ?></p>
                                <p><strong>Total:</strong> RM <?php echo number_format($order['total_price'], 2); ?></p>
                                <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order['shipping_address']) . ', ' . htmlspecialchars($order['shipping_city']) . ', ' . htmlspecialchars($order['shipping_postal_code']); ?></p>
                                <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>

                                <?php if ($order['status'] !== 'Order Completed'): ?>
                                    <form method="POST">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <button type="submit" name="mark_received" class="mark-received-btn">Mark as Received</button>
                                    </form>
                                <?php else: ?>
                                    <a href="book_details.php?id=<?php echo $order['book_id']; ?>#review-form" class="mark-received-btn leave-review-btn">Leave a Review</a>
                                    <button class="mark-received-btn leave-review-btn" onclick='showInvoice(<?php echo json_encode($order); ?>)'>View Invoice</button>
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

<!-- Invoice Modal -->
<div id="invoiceModal">
    <h3>Invoice</h3>
    <div id="invoiceContent"></div>
    <button onclick="printInvoice()" class="mark-received-btn leave-review-btn">Print</button>
    <button onclick="closeInvoice()" class="mark-received-btn" style="background-color:#e74c3c;">Close</button>
</div>

<?php include 'footer.php'; ?>

<script>
function toggleDetails(header) {
    const details = header.nextElementSibling;
    details.style.display = (details.style.display === 'block') ? 'none' : 'block';
}

function showInvoice(order) {
    const modal = document.getElementById('invoiceModal');
    const content = document.getElementById('invoiceContent');
    content.innerHTML = `
        <p><strong>Order ID:</strong> ${order.id}</p>
        <p><strong>Book Title:</strong> ${order.book_title}</p>
        <p><strong>Quantity:</strong> ${order.quantity}</p>
        <p><strong>Unit Price:</strong> RM ${parseFloat(order.unit_price).toFixed(2)}</p>
        <p><strong>Total Price:</strong> RM ${parseFloat(order.total_price).toFixed(2)}</p>
        <p><strong>Shipping Address:</strong> ${order.shipping_address}, ${order.shipping_city}, ${order.shipping_postal_code}</p>
        <p><strong>Status:</strong> ${order.status}</p>
        <p><strong>Order Date:</strong> ${new Date(order.created_at).toLocaleDateString()}</p>
    `;
    modal.style.display = 'block';
}

function closeInvoice() {
    document.getElementById('invoiceModal').style.display = 'none';
}

function printInvoice() {
    const content = document.getElementById('invoiceContent').innerHTML;
    const printWindow = window.open('', '', 'width=600,height=600');
    printWindow.document.write('<html><head><title>Invoice</title></head><body>');
    printWindow.document.write('<h3>BookHaven Invoice</h3>');
    printWindow.document.write(content);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}
</script>
</body>
</html>
