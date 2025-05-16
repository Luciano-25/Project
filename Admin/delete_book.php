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
    
    // Delete related orders
    $delete_orders = "DELETE FROM orders WHERE book_id = ?";
    $stmt = $conn->prepare($delete_orders);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    
    // Delete the book
    $delete_book = "DELETE FROM books WHERE id = ?";
    $stmt = $conn->prepare($delete_book);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
}

header("Location: view_books.php");
exit();