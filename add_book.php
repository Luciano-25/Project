<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $stock = $_POST['stock'];

    $sql = "INSERT INTO books (title, author, stock) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $title, $author, $stock);

    if ($stmt->execute()) {
        echo "Book added successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
