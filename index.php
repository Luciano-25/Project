<?php
session_start();
require_once 'config.php';

// Fetch featured books (newest 4 books)
$sql_new = "SELECT * FROM books ORDER BY id DESC LIMIT 4";
$new_arrivals = $conn->query($sql_new);

// Fetch bestsellers (top 4 rated books)
$sql_best = "SELECT * FROM books ORDER BY rating DESC LIMIT 4";
$bestsellers = $conn->query($sql_best);

// Fetch staff picks (random 4 books)
$sql_staff = "SELECT * FROM books ORDER BY RAND() LIMIT 4";
$staff_picks = $conn->query($sql_staff);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookHaven - Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="home.css">
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
                    <a href="cart.php" class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count"><?php echo empty($_SESSION['cart']) ? '0' : count($_SESSION['cart']); ?></span>
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h1>Discover Your Next Great Read</h1>
                <p>Explore our vast collection of books for every reader</p>
                <a href="products.php" class="cta-button">Browse Books</a>
            </div>
        </section>

        <section class="featured-collections">
    <div class="collection-section">
        <div class="section-header">
            <h2>New Arrivals</h2>
            <a href="products.php?category=new" class="view-all">View All →</a>
        </div>
        <button class="scroll-btn scroll-left" onclick="scroll('new-arrivals', -300)">
            <i class="fas fa-chevron-left"></i>
        </button>
        <div class="book-grid" id="new-arrivals">
            <?php while($book = $new_arrivals->fetch_assoc()): ?>
                <div class="book-card">
                    <a href="book_details.php?id=<?php echo $book['id']; ?>">
                        <img src="<?php echo $book['image_url']; ?>" alt="<?php echo $book['title']; ?>">
                        <div class="book-info">
                            <h3 class="book-title"><?php echo $book['title']; ?></h3>
                            <p class="book-author">by <?php echo $book['author']; ?></p>
                            <p class="book-price">RM <?php echo number_format($book['price'], 2); ?></p>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
        <button class="scroll-btn scroll-right" onclick="scroll('new-arrivals', 300)">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
    
    <!-- Repeat for Bestsellers and Staff Picks sections -->
</section>



    <!-- Enhanced Footer -->
    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>About BookHaven</h3>
                <p>Your premier destination for books of all genres. We're passionate about connecting readers with their next favorite book.</p>
                <a href="about.php" class="footer-link">Learn More</a>
            </div>
            
            <div class="footer-section">
                <h3>Customer Service</h3>
                <ul>
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="shipping.php">Shipping Information</a></li>
                    <li><a href="returns.php">Returns Policy</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Connect With Us</h3>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-pinterest"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 BookHaven. All rights reserved.</p>
        </div>
    </footer>
    <script>
function scroll(elementId, amount) {
    const container = document.getElementById(elementId);
    container.scrollBy({
        left: amount,
        behavior: 'smooth'
    });
}
</script>

</body>
</html>
