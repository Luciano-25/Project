<?php
include '../config.php';

if (isset($_GET['id'])) {
    $book_id = $_GET['id'];
    
    // First get the image path to delete the file
    $sql = "SELECT image_url FROM books WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
    
    // Delete the image file
    if ($book['image_url']) {
        $image_path = "../" . $book['image_url'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    // Delete the book record
    $sql = "DELETE FROM books WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
}

// Redirect back to the books list
header("Location: view_books.php");
exit();
