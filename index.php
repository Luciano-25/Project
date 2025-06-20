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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        .main-footer {
            background-color: #2c3e50;
            color: #ffffff;
            padding: 40px 20px 20px;
            font-family: 'Poppins', sans-serif;
        }

        .footer-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 60px;
            max-width: 1200px;
            margin: 0 auto;
            text-align: left;
        }

        .footer-content.two-column {
            justify-content: center;
        }

        .footer-section {
            flex: 1 1 300px;
            max-width: 500px;
        }

        .footer-section h3 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #ffffff;
        }

        .footer-section p,
        .footer-section ul,
        .footer-section a {
            color: #dcdcdc;
            font-size: 14px;
            line-height: 1.6;
            text-decoration: none;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-section ul li {
            margin-bottom: 8px;
        }

        .footer-section a:hover {
            color: #ffffff;
            text-decoration: underline;
        }

        .footer-bottom {
            text-align: center;
            margin-top: 30px;
            font-size: 13px;
            color: #aaa;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

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
    </section>
</main>

<!-- Footer -->
<footer class="main-footer">
    <div class="footer-content two-column">
        <div class="footer-section">
            <h3>About BookHaven</h3>
            <p>Your premier destination for books of all genres. We're passionate about connecting readers with their next favorite book.</p>
            <a href="about.php">About Us</a>
        </div>

        <div class="footer-section">
            <h3>Customer Service</h3>
            <ul>
                <li><a href="contact2.php">Contact Us</a></li>
                <li><a href="returns.php">Returns Policy</a></li>
                <li><a href="faq.php">FAQ</a></li>
            </ul>
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
