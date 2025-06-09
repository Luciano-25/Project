<?php
// book_list.php
include '../config.php';
include 'admin_header.php';

// Get the search query from GET request
$search = $_GET['search'] ?? '';

// Prepare the SQL query with search filter if applicable
if ($search) {
    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? ORDER BY created_at DESC");
    $like_search = "%$search%";
    $stmt->bind_param("ss", $like_search, $like_search);
    $stmt->execute();
    $books = $stmt->get_result();
} else {
    // No search filter, get all books
    $books = $conn->query("SELECT * FROM books ORDER BY created_at DESC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book List - Admin</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        .styled-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .styled-table th, .styled-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .styled-table th {
            background-color: #2c3e50;
            color: #fff;
        }
        h2 {
            margin-bottom: 20px;
        }
        .search-form {
            margin-bottom: 20px;
        }
        .search-input {
            padding: 8px 10px;
            width: 300px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        .search-button {
            padding: 8px 16px;
            border: none;
            background-color: #3498db;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin-left: 8px;
        }
        .search-button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>All Books</h2>

    <form method="get" class="search-form">
        <input type="text" name="search" class="search-input" placeholder="Search by Title or Author" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="search-button">Search</button>
    </form>

    <table class="styled-table">
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Price (RM)</th>
            <th>Rating</th>
            <th>Stock</th>
            <th>Created At</th>
        </tr>
        <?php if ($books->num_rows > 0): ?>
            <?php while ($book = $books->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($book['title']) ?></td>
                    <td><?= htmlspecialchars($book['author']) ?></td>
                    <td><?= number_format($book['price'], 2) ?></td>
                    <td><?= htmlspecialchars($book['rating']) ?></td>
                    <td><?= $book['stock'] ?></td>
                    <td><?= date('d M Y', strtotime($book['created_at'])) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align:center;">No books found.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>
