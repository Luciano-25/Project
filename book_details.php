<?php
session_start();
require_once 'config.php';

$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sort = $_GET['sort'] ?? 'newest';
$sort_sql = match($sort) {
    'oldest' => 'r.created_at ASC',
    'highest' => 'r.rating DESC',
    'lowest' => 'r.rating ASC',
    default => 'r.created_at DESC'
};

// Fetch book details
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

$user_id = $_SESSION['user_id'] ?? null;
$review_error = '';
$existing_review = null;

// Check if user already reviewed
if ($user_id) {
    $check = $conn->prepare("SELECT r.* FROM reviews r JOIN orders o ON r.order_id = o.id WHERE r.user_id = ? AND o.book_id = ? LIMIT 1");
    $check->bind_param("ii", $user_id, $book_id);
    $check->execute();
    $existing_review = $check->get_result()->fetch_assoc();
}

// Submit or update review
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating'], $_SESSION['user_id'])) {
    $rating = intval($_POST['rating']);
    $title = trim($_POST['title']);
    $text = trim($_POST['review_text']);

    // Only allow if order is completed
    $order_check = $conn->prepare("SELECT id FROM orders WHERE user_id = ? AND book_id = ? AND status = 'Order Completed' LIMIT 1");
    $order_check->bind_param("ii", $user_id, $book_id);
    $order_check->execute();
    $order_result = $order_check->get_result();

    if ($order_result->num_rows > 0) {
        $order_id = $order_result->fetch_assoc()['id'];

        if ($existing_review) {
            $update = $conn->prepare("UPDATE reviews SET rating=?, title=?, review_text=?, created_at=NOW() WHERE id=?");
            $update->bind_param("issi", $rating, $title, $text, $existing_review['id']);
            $update->execute();
        } else {
            $insert = $conn->prepare("INSERT INTO reviews (order_id, user_id, rating, title, review_text, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $insert->bind_param("iiiss", $order_id, $user_id, $rating, $title, $text);
            $insert->execute();
        }

        header("Location: book_details.php?id=$book_id&sort=$sort");
        exit();
    } else {
        $review_error = "You must mark your order as received before leaving a review.";
    }
}

// Get average rating
$avg_stmt = $conn->prepare("SELECT AVG(r.rating) as avg_rating FROM reviews r JOIN orders o ON r.order_id = o.id WHERE o.book_id = ?");
$avg_stmt->bind_param("i", $book_id);
$avg_stmt->execute();
$avg_rating = round($avg_stmt->get_result()->fetch_assoc()['avg_rating'] ?? 0, 1);

// Fetch reviews
$reviews_stmt = $conn->prepare("SELECT r.*, u.username FROM reviews r JOIN orders o ON r.order_id = o.id JOIN users u ON r.user_id = u.id WHERE o.book_id = ? ORDER BY $sort_sql");
$reviews_stmt->bind_param("i", $book_id);
$reviews_stmt->execute();
$reviews = $reviews_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BookHaven - <?php echo htmlspecialchars($book['title']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .reviews-section { margin-top: 40px; border-top: 1px solid #ccc; padding-top: 30px; }
        .review { border-bottom: 1px solid #eee; margin-bottom: 20px; padding-bottom: 10px; }
        .review h4 { margin: 5px 0; font-size: 18px; }
        .rating-stars i { color: #f39c12; }
        .rating-stars-input { display: flex; flex-direction: row-reverse; gap: 5px; justify-content: flex-start; }
        .rating-stars-input input { display: none; }
        .rating-stars-input label {
            font-size: 25px;
            color: #ccc;
            cursor: pointer;
        }
        .rating-stars-input input:checked ~ label,
        .rating-stars-input label:hover,
        .rating-stars-input label:hover ~ label {
            color: #f39c12;
        }
        .review-form input, .review-form textarea {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .submit-review-btn {
            margin-top: 15px;
            padding: 10px 20px;
            background: #27ae60;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .sort-form { margin-bottom: 20px; }
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
            <p><strong>Average Rating:</strong>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fa-star <?php echo ($i <= round($avg_rating)) ? 'fas' : 'far'; ?>"></i>
                <?php endfor; ?> (<?php echo $avg_rating; ?>/5)
            </p>
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
                    <button class="add-to-cart-btn" disabled>Add to Cart</button>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Review Section -->
    <div class="reviews-section">
        <h2>Customer Reviews</h2>

        <!-- Sort Form -->
        <form class="sort-form" method="GET">
            <input type="hidden" name="id" value="<?php echo $book_id; ?>">
            <label for="sort">Sort by:</label>
            <select name="sort" onchange="this.form.submit()">
                <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Newest</option>
                <option value="oldest" <?= $sort == 'oldest' ? 'selected' : '' ?>>Oldest</option>
                <option value="highest" <?= $sort == 'highest' ? 'selected' : '' ?>>Highest Rated</option>
                <option value="lowest" <?= $sort == 'lowest' ? 'selected' : '' ?>>Lowest Rated</option>
            </select>
        </form>

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
            <p>No reviews yet. Be the first to write one!</p>
        <?php endif; ?>

        <!-- Review Form -->
        <?php if ($user_id): ?>
            <div class="review-form">
                <h3><?php echo $existing_review ? 'Edit Your Review' : 'Leave a Review'; ?></h3>
                <?php if ($review_error): ?><p style="color: red;"><?php echo $review_error; ?></p><?php endif; ?>
                <form method="POST">
                    <div class="rating-stars-input">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" <?= $existing_review && $existing_review['rating'] == $i ? 'checked' : '' ?>>
                            <label for="star<?php echo $i; ?>">&#9733;</label>
                        <?php endfor; ?>
                    </div>
                    <input type="text" name="title" placeholder="Review Title" required value="<?php echo htmlspecialchars($existing_review['title'] ?? ''); ?>">
                    <textarea name="review_text" placeholder="Your review..." required><?php echo htmlspecialchars($existing_review['review_text'] ?? ''); ?></textarea>
                    <button type="submit" class="submit-review-btn">
                        <?php echo $existing_review ? 'Update Review' : 'Submit Review'; ?>
                    </button>
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


