<?php
include 'db_connect.php';

// Fetch book details for editing
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM books WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $stock = $_POST['stock'];

    $sql = "UPDATE books SET title=?, author=?, stock=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $title, $author, $stock, $id);

    if ($stmt->execute()) {
        echo "Book updated successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!-- Edit Book Form -->
<form action="edit_book.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $book['id']; ?>">
    
    <label for="title">Book Title:</label>
    <input type="text" id="title" name="title" value="<?php echo $book['title']; ?>" required>

    <label for="author">Author:</label>
    <input type="text" id="author" name="author" value="<?php echo $book['author']; ?>" required>

    <label for="stock">Stock:</label>
    <input type="number" id="stock" name="stock" value="<?php echo $book['stock']; ?>" required>

    <button type="submit">Update Book</button>
</form>
