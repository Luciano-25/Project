<?php
session_start();
require_once 'config.php';

// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
if ($search) {
    $sql = "SELECT * FROM books WHERE title LIKE '%$search%' OR author LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM books";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookHaven - Browse Books</title>
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
                    <div class="search-container">
                        <form action="products.php" method="GET" class="search-form">
                            <input type="text" name="search" placeholder="Search books..." value="<?php echo htmlspecialchars($search); ?>" class="search-input">
                            <button type="submit" class="search-button">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                    <a href="cart.php" class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count"><?php echo isset($_SESSION['total_books']) ? $_SESSION['total_books'] : 0; ?></span>
                    </a>

                </div>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if ($search): ?>
            <div class="search-results">
                <h2>Search results for: "<?php echo htmlspecialchars($search); ?>"</h2>
                <?php if ($result->num_rows === 0): ?>
                    <p>No books found matching your search.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="products-grid">
            <?php while($book = $result->fetch_assoc()): ?>
                <div class="book-card">
                    <a href="book_details.php?id=<?php echo $book['id']; ?>" class="book-link">
                        <div class="book-cover-container">
                            <img src="<?php echo $book['image_url']; ?>" alt="<?php echo $book['title']; ?>" class="book-cover">
                            <div class="book-hover">
                                <span>View Details</span>
                            </div>
                        </div>
                        <div class="book-info">
                            <h3 class="book-title"><?php echo $book['title']; ?></h3>
                            <p class="book-author">by <?php echo $book['author']; ?></p>
                            <div class="book-rating">
                                <?php
                                $rating = $book['rating'];
                                for($i = 1; $i <= 5; $i++) {
                                    if($i <= $rating) {
                                        echo '<i class="fas fa-star"></i>';
                                    } elseif($i - 0.5 <= $rating) {
                                        echo '<i class="fas fa-star-half-alt"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                                ?>
                            </div>
                            <p class="book-price">RM <?php echo number_format($book['price'], 2); ?></p>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <footer class="footer">
        © 2025 BookHaven. All rights reserved.
    </footer>
</body>
</html>
