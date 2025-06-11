<?php
session_start();
require_once 'config.php';

$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch book details
$sql = "SELECT * FROM books WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

// Handle review submission
$review_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['rating'])) {
    $user_id = $_SESSION['user_id'];
    $rating = intval($_POST['rating']);
    $title = trim($_POST['title']);
    $text = trim($_POST['review_text']);

    // Check if this user already reviewed this book
    $check = $conn->prepare("SELECT r.id FROM review r JOIN orders o ON r.order_id = o.id WHERE r.user_id = ? AND o.book_id = ?");
    $check->bind_param("ii", $user_id, $book_id);
    $check->execute();
    $alreadyReviewed = $check->get_result()->num_rows > 0;

    if (!$alreadyReviewed) {
        // Find any valid order for this user and book
        $order_check = $conn->prepare("SELECT id FROM orders WHERE user_id = ? AND book_id = ? LIMIT 1");
        $order_check->bind_param("ii", $user_id, $book_id);
        $order_check->execute();
        $order_result = $order_check->get_result();

        if ($order_result->num_rows > 0) {
            $order_id = $order_result->fetch_assoc()['id'];

            // Insert review
            $insert = $conn->prepare("INSERT INTO review (order_id, user_id, rating, title, review_text, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $insert->bind_param("iiiss", $order_id, $user_id, $rating, $title, $text);
            $insert->execute();
        } else {
            $review_error = "You must purchase the book before reviewing.";
        }
    } else {
        $review_error = "You have already reviewed this book.";
    }
}

// Fetch reviews
$reviews_stmt = $conn->prepare("SELECT r.*, u.username FROM reviews r JOIN orders o ON r.order_id = o.id JOIN users u ON r.user_id = u.id WHERE o.book_id = ? ORDER BY r.created_at DESC");
$reviews_stmt->bind_param("i", $book_id);
$reviews_stmt->execute();
$reviews = $reviews_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BookHaven - <?php echo $book['title']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .reviews-section { margin-top: 40px; border-top: 1px solid #ccc; padding-top: 30px; }
        .review-form textarea { width: 100%; min-height: 100px; padding: 10px; }
        .review-form input[type="text"] { width: 100%; padding: 8px; margin-bottom: 10px; }
        .review { border-bottom: 1px solid #eee; margin-bottom: 20px; padding-bottom: 10px; }
        .rating-stars i { color: #f39c12; }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="container">
    <div class="book-details">
        <div class="book-image">
            <img src="<?php echo $book['image_url']; ?>" alt="<?php echo $book['title']; ?>">
        </div>
        <div class="book-info">
            <h1><?php echo $book['title']; ?></h1>
            <p class="author">by <?php echo $book['author']; ?></p>
            <div class="rating">
                <?php
                $rating = $book['rating'];
                for ($i = 1; $i <= 5; $i++) {
                    echo '<i class="' . ($i <= $rating ? 'fas' : 'far') . ' fa-star"></i>';
                }
                ?>
            </div>
            <p class="price">RM <?php echo number_format($book['price'], 2); ?></p>
            <p class="stock-status"><i class="fas fa-box"></i> Stock Available: <?php echo $book['stock']; ?> units</p>
            <div class="description">
                <h3>Description</h3>
                <p><?php echo $book['description']; ?></p>
            </div>
            <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                <?php if ($book['stock'] > 0): ?>
                    <div class="quantity-selector">
                        <label for="quantity">Quantity:</label>
                        <select name="quantity" id="quantity">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                <?php else: ?>
                    <p class="out-of-stock">Currently Out of Stock</p>
                    <button type="submit" class="add-to-cart-btn" disabled>Add to Cart</button>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="reviews-section">
        <h2>Customer Reviews</h2>

        <?php if ($reviews->num_rows > 0): ?>
            <?php while ($r = $reviews->fetch_assoc()): ?>
                <div class="review">
                    <div class="rating-stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fa-star <?php echo ($i <= $r['rating']) ? 'fas' : 'far'; ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <h4><?php echo htmlspecialchars($r['title']); ?> - by <?php echo htmlspecialchars($r['username']); ?></h4>
                    <p><?php echo htmlspecialchars($r['review_text']); ?></p>
                    <small><?php echo date('F j, Y', strtotime($r['created_at'])); ?></small>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No reviews yet. Be the first to leave one!</p>
        <?php endif; ?>

        <!-- Leave a review -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="review-form">
                <h3>Leave a Review</h3>
                <?php if ($review_error): ?>
                    <p style="color: red;"><?php echo $review_error; ?></p>
                <?php endif; ?>
                <form method="POST">
                    <label for="rating">Rating:</label>
                    <select name="rating" required>
                        <option value="">Select</option>
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?> Star<?php echo $i > 1 ? 's' : ''; ?></option>
                        <?php endfor; ?>
                    </select>
                    <input type="text" name="title" placeholder="Review Title" required>
                    <textarea name="review_text" placeholder="Your review..." required></textarea>
                    <button type="submit" class="add-to-cart-btn">Submit Review</button>
                </form>
            </div>
        <?php else: ?>
            <p><a href="login.php">Log in</a> to leave a review.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>


