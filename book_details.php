<?php
session_start();
require_once 'config.php';

if (isset($_GET['id'])) {
    $book_id = $_GET['id'];
    $sql = "SELECT * FROM books WHERE id = $book_id";
    $result = $conn->query($sql);
    $book = $result->fetch_assoc();
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
    <header class="top-header">
        <div class="header-container">
            <h1 class="site-title">BookHaven</h1>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="products.php">Browse</a>
                <div class="nav-right">
                    <button onclick="history.back()" class="back-button">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                    <a href="cart.php" class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count"><?php echo isset($_SESSION['total_books']) ? $_SESSION['total_books'] : 0; ?></span>
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="book-details">
            <div class="book-image">
                <img src="<?php echo $book['image_url']; ?>" alt="<?php echo $book['title']; ?>">
            </div>
            <div class="book-info">
                <h1><?php echo $book['title']; ?></h1>
                <p class="author">by <?php echo $book['author']; ?></p>
                <div class="rating">
                    <span class="stars"><?php echo $book['rating']; ?></span>
                </div>
                <p class="price">RM <?php echo number_format($book['price'], 2); ?></p>
                <div class="description">
                    <h3>Description</h3>
                    <p><?php echo $book['description']; ?></p>
                </div>
                <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                    <div class="quantity-selector">
                        <label for="quantity">Quantity:</label>
                        <select name="quantity" id="quantity">
                            <?php for($i = 1; $i <= 10; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>

    <footer class="footer">
        © 2025 BookHaven. All rights reserved.
    </footer>
</body>
</html>
