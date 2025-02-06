<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = $_GET['order_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookHaven - Review Order</title>
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
                        <i class="fas fa-user"></i>
                        <?php echo $_SESSION['username']; ?>
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="review-container">
            <h2>Thank You for Your Purchase!</h2>
            <p class="review-intro">We'd love to hear about your experience.</p>
            
            <form action="submit_review.php" method="POST" class="review-form">
                <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                
                <div class="rating-section">
                    <h3>Rate your experience</h3>
                    <div class="star-rating">
                        <?php for($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" required>
                            <label for="star<?php echo $i; ?>"><i class="fas fa-star"></i></label>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="review_title">Review Title</label>
                    <input type="text" id="review_title" name="review_title" required>
                </div>

                <div class="form-group">
                    <label for="review_text">Your Review</label>
                    <textarea id="review_text" name="review_text" rows="5" required></textarea>
                </div>

                <div class="button-group">
                    <a href="products.php" class="submit-review-btn">Submit Review</a>
                    <a href="products.php" class="skip-review-btn">Skip Review</a>
                </div>
            </form>
        </div>
    </div>

    <footer class="footer">
        © 2025 BookHaven. All rights reserved.
    </footer>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star-rating label');
    
    stars.forEach((star, index) => {
        star.addEventListener('mouseover', () => {
            // Highlight stars on hover
            for(let i = stars.length - 1; i >= index; i--) {
                stars[i].style.color = '#f1c40f';
            }
        });
        
        star.addEventListener('mouseout', () => {
            // Reset stars unless they're selected
            stars.forEach(s => {
                if (!s.previousElementSibling.checked) {
                    s.style.color = '#ddd';
                }
            });
        });
        
        star.addEventListener('click', () => {
            // Keep stars highlighted after selection
            for(let i = stars.length - 1; i >= index; i--) {
                stars[i].style.color = '#f1c40f';
            }
            for(let i = index - 1; i >= 0; i--) {
                stars[i].style.color = '#ddd';
            }
        });
    });
});
</script>

</body>
</html>
