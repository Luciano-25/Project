<?php
include '../config.php';
include 'admin_header.php';

// Fetch all books
$sql = "SELECT * FROM books ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Book List - Admin</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <div class="container">
        <h2>All Books in Store</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Price (RM)</th>
                    <th>Rating</th>
                    <th>Stock</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($book = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($book['id']); ?></td>
                        <td><?php echo htmlspecialchars($book['title']); ?></td>
                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                        <td><?php echo number_format($book['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($book['rating']); ?></td>
                        <td><?php echo htmlspecialchars($book['stock']); ?></td>
                        <td><?php echo date('d M Y', strtotime($book['created_at'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7">No books found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</body>
</html>
