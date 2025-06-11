<?php
session_start();
require_once 'config.php';

if (isset($_GET['id'])) {
    $book_id = $_GET['id'];
    $sql = "SELECT * FROM books WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $book = $stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookHaven - <?php echo $book['title']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
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
                    for($i = 1; $i <= 5; $i++) {
                        echo '<i class="' . ($i <= $rating ? 'fas' : 'far') . ' fa-star"></i>';
                    }
                    ?>
                </div>
                <p class="price">RM <?php echo number_format($book['price'], 2); ?></p>
                <p class="stock-status">
                    <i class="fas fa-box"></i> 
                    Stock Available: <?php echo $book['stock']; ?> units
                </p>
                <div class="description">
                    <h3>Description</h3>
                    <p><?php echo $book['description']; ?></p>
                </div>
                <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                    <?php if($book['stock'] > 0): ?>
                        <div class="quantity-selector">
                            <label for="quantity">Quantity:</label>
                            <select name="quantity" id="quantity">
                                <?php for($i = 1; $i <= 5; $i++): ?>
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

        <!-- ================= Review Submission Form ================ -->
        <?php if (isset($_SESSION['user_id'])): ?>
        <div class="review-form">
            <h3>Write a Review</h3>
            <form action="submit_review.php" method="POST">
                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">

                <label for="rating">Rating:</label>
                <select name="rating" id="rating" required>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>

                <label for="review_title">Title:</label>
                <input type="text" name="review_title" required>

                <label for="review_text">Your Review:</label>
                <textarea name="review_text" rows="4" required></textarea>

                <button type="submit">Submit Review</button>
            </form>
        </div>
        <?php endif; ?>

        <!-- ================= Display All Reviews =================== -->
        <div class="book-reviews">
            <h3>Customer Reviews</h3>
            <?php
            $review_sql = "SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.order_id = ?";
            $review_stmt = $conn->prepare($review_sql);
            $review_stmt->bind_param("i", $book['id']);
            $review_stmt->execute();
            $reviews = $review_stmt->get_result();

            if ($reviews->num_rows > 0):
                while ($review = $reviews->fetch_assoc()):
            ?>
                <div class="review-box">
                    <strong><?php echo htmlspecialchars($review['username']); ?></strong> rated it <?php echo $review['rating']; ?>/5
                    <p><strong><?php echo htmlspecialchars($review['title']); ?></strong></p>
                    <p><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                    <small>Reviewed on <?php echo date('F j, Y', strtotime($review['created_at'])); ?></small>
                </div>
            <?php endwhile; else: ?>
                <p>No reviews yet. Be the first to review!</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
