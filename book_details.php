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
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
