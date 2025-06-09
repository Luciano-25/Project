<?php
include '../config.php';

// Handle search input
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT id, title, author, price, stock, image_url, genre FROM books";

if (!empty($search)) {
    $sql .= " WHERE title LIKE ? OR author LIKE ? OR genre LIKE ? ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $like = '%' . $search . '%';
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql .= " ORDER BY id DESC";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Books - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
    <style>
        .genre-badge {
            padding: 4px 10px;
            border-radius: 12px;
            color: white;
            font-size: 0.85em;
            font-weight: bold;
            text-transform: capitalize;
            display: inline-block;
        }

        .genre-badge.fantasy { background-color: #8e44ad; }
        .genre-badge.mystery { background-color: #2c3e50; }
        .genre-badge.science-fiction { background-color: #16a085; }
        .genre-badge.horror { background-color: #c0392b; }
        .genre-badge.romance { background-color: #e91e63; }
        .genre-badge.fiction { background-color: #3498db; }
        .genre-badge.adventure { background-color: #e67e22; }
        .genre-badge.children { background-color: #f39c12; }
        .genre-badge.thriller { background-color: #34495e; }
        .genre-badge.biography { background-color: #27ae60; }
        .genre-badge.history { background-color: #7f8c8d; }
        .genre-badge.cookbook { background-color: #d35400; }

        .search-bar {
            margin-bottom: 20px;
        }

        .search-bar input[type="text"] {
            padding: 8px 14px;
            width: 280px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1em;
        }

        .search-bar button {
            padding: 8px 14px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }

        .search-bar button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
<?php include 'admin_header.php'; ?>

<div class="books-container">
    <div class="header-actions">
        <h2>Manage Books</h2>
        <a href="add_book.php" class="add-book-btn">
            <i class="fas fa-plus"></i> Add New Book
        </a>
    </div>

    <!-- Search Form -->
    <form method="get" class="search-bar">
        <input type="text" name="search" placeholder="Search by title, author, or genre" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <table>
        <tr>
            <th>Image</th>
            <th>Title</th>
            <th>Author</th>
            <th>Price (RM)</th>
            <th>Stock</th>
            <th>Genre</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td>
                    <img src="../<?php echo $row['image_url']; ?>" alt="<?php echo $row['title']; ?>" class="book-thumbnail">
                </td>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['author']); ?></td>
                <td>RM <?php echo number_format($row['price'], 2); ?></td>
                <td class="stock-display"><?php echo $row['stock']; ?></td>
                <td>
                    <span class="genre-badge <?php echo strtolower(str_replace(' ', '-', $row['genre'])); ?>">
                        <?php echo htmlspecialchars($row['genre']); ?>
                    </span>
                </td>
                <td class="actions">
                    <a href="edit_book.php?id=<?php echo $row['id']; ?>" class="edit-btn">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="delete_book.php?id=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this book?');">
                        <i class="fas fa-trash"></i> Delete
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
