<?php
// book_list.php
include '../config.php';
include 'admin_header.php';

$books = $conn->query("SELECT * FROM books ORDER BY created_at DESC");
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
    </style>
</head>
<body>
<div class="container">
    <h2>All Books</h2>
    <table class="styled-table">
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Price (RM)</th>
            <th>Rating</th>
            <th>Stock</th>
            <th>Created At</th>
        </tr>
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
    </table>
</div>
</body>
</html>