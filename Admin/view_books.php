<?php
include '../config.php';

$sql = "SELECT books.id, books.title, books.author, books.price, books.stock, books.image_url 
        FROM books 
        ORDER BY books.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Books - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
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

        <table>
            <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Author</th>
                <th>Price (RM)</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <img src="../<?php echo $row['image_url']; ?>" alt="<?php echo $row['title']; ?>" class="book-thumbnail">
                    </td>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['author']; ?></td>
                    <td>RM <?php echo number_format($row['price'], 2); ?></td>
                    <td class="stock-display"><?php echo $row['stock']; ?></td>
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
