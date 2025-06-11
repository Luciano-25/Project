<?php
session_start();
require_once 'config.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'title';
$genre_filter = isset($_GET['genre']) ? $_GET['genre'] : '';

$genre_sql = "SELECT DISTINCT genre FROM books ORDER BY genre ASC";
$genre_result = $conn->query($genre_sql);

// Updated SQL to include average rating
$sql = "SELECT b.*, 
               (SELECT ROUND(AVG(r.rating), 1) 
                FROM reviews r 
                JOIN orders o ON r.order_id = o.id 
                WHERE o.book_id = b.id) AS avg_rating 
        FROM books b WHERE 1";

if ($search) {
    $search_esc = $conn->real_escape_string($search);
    $sql .= " AND (b.title LIKE '%$search_esc%' OR b.author LIKE '%$search_esc%')";
}

if ($genre_filter && $genre_filter !== 'all') {
    $genre_esc = $conn->real_escape_string($genre_filter);
    $sql .= " AND b.genre = '$genre_esc'";
}

switch ($sort) {
    case 'price_low':
        $sql .= " ORDER BY b.price ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY b.price DESC";
        break;
    case 'rating':
        $sql .= " ORDER BY avg_rating DESC";
        break;
    default:
        $sql .= " ORDER BY b.title ASC";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>BookHaven - Browse Books</title>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <link rel="stylesheet" href="styles.css" />
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
                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </a>
                    <?php else: ?>
                    <a href="login.php" class="login-link">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <?php endif; ?>
                    <div class="search-container">
                        <form action="products.php" method="GET" class="search-form">
                            <input
                                type="text"
                                name="search"
                                placeholder="Search books..."
                                value="<?php echo htmlspecialchars($search); ?>"
                                class="search-input"
                            />
                            <button type="submit" class="search-button">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                    <a href="cart.php" class="cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count"><?php echo empty($_SESSION['cart']) ? '0' : count($_SESSION['cart']); ?></span>
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

        <form action="products.php" method="GET" class="filter-form">
            <?php if ($search): ?>
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>" />
            <?php endif; ?>

            <label for="genre">Filter by Genre:</label>
            <select name="genre" id="genre" onchange="this.form.submit()">
                <option value="all" <?php echo ($genre_filter === 'all' || $genre_filter === '') ? 'selected' : ''; ?>>All Genres</option>
                <?php while ($genre = $genre_result->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($genre['genre']); ?>"
                    <?php echo ($genre_filter === $genre['genre']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($genre['genre']); ?>
                </option>
                <?php endwhile; ?>
            </select>

            <label for="sort">Sort By:</label>
            <select name="sort" id="sort" onchange="this.form.submit()">
                <option value="title" <?php echo $sort === 'title' ? 'selected' : ''; ?>>Title</option>
                <option value="price_low" <?php echo $sort === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                <option value="price_high" <?php echo $sort === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                <option value="rating" <?php echo $sort === 'rating' ? 'selected' : ''; ?>>Rating</option>
            </select>
        </form>

        <div class="products-grid">
            <?php while ($book = $result->fetch_assoc()): ?>
            <div class="book-card">
                <a href="book_details.php?id=<?php echo $book['id']; ?>" class="book-link">
                    <div class="book-cover-container">
                        <img
                            src="<?php echo htmlspecialchars($book['image_url']); ?>"
                            alt="<?php echo htmlspecialchars($book['title']); ?>"
                            class="book-cover"
                        />
                        <div class="book-hover"><span>View Details</span></div>
                    </div>
                    <div class="book-info">
                        <h3 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                        <p class="book-author">by <?php echo htmlspecialchars($book['author']); ?></p>
                        <div class="book-rating">
                            <?php
                            $avg = round($book['avg_rating']);
                            for ($i = 1; $i <= 5; $i++) {
                                echo '<i class="fa-star ' . ($i <= $avg ? 'fas' : 'far') . '"></i>';
                            }
                            echo $book['avg_rating'] ? " ({$book['avg_rating']}/5)" : " (No ratings)";
                            ?>
                        </div>
                        <p class="book-price">RM <?php echo number_format($book['price'], 2); ?></p>
                    </div>
                </a>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <footer class="footer">© 2025 BookHaven. All rights reserved.</footer>
</body>
</html>
