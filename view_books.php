<?php
include 'db_connect.php';

$sql = "SELECT * FROM books";
$result = $conn->query($sql);
?>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Author</th>
        <th>Stock</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['title']; ?></td>
            <td><?php echo $row['author']; ?></td>
            <td><?php echo $row['stock']; ?></td>
            <td>
                <a href="edit_book.php?id=<?php echo $row['id']; ?>">Edit</a> |
                <a href="delete_book.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
            </td>
        </tr>
    <?php } ?>
</table>
