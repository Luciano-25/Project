<?php
session_start();
require_once 'config.php';

// Fetch books from database
$sql = "SELECT * FROM books";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookHaven - Products</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <div class="products-grid">
            <?php while($book = $result->fetch_assoc()): ?>
                <div class="book-card" data-id="<?php echo $book['id']; ?>">
                    <img src="<?php echo $book['image_url']; ?>" alt="<?php echo $book['title']; ?>" class="book-cover">
                    <h3 class="book-title"><?php echo $book['title']; ?></h3>
                    <p class="book-author"><?php echo $book['author']; ?></p>
                    <div class="book-rating"><?php echo $book['rating']; ?></div>
                    <p class="book-price">RM <?php echo number_format($book['price'], 2); ?></p>
                    <form action="add_to_cart.php" method="POST">
                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                        <button type="submit" class="add-to-cart">Add to Cart</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
