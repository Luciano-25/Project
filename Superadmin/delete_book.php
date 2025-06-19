<?php
include '../config.php';
include 'log_helper.php'; // ✅ Include logging helper

if (isset($_GET['id'])) {
    $book_id = $_GET['id'];
    
    // Get image path to delete
    $sql = "SELECT image_url FROM books WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();

    // Delete image file
    if ($book['image_url']) {
        $image_path = "../" . $book['image_url'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    // ✅ Do NOT delete orders — let book_id become NULL

    // Delete the book
    $delete_book = "DELETE FROM books WHERE id = ?";
    $stmt = $conn->prepare($delete_book);
    $stmt->bind_param("i", $book_id);
    if ($stmt->execute()) {
        // ✅ Log the action
        log_admin_action($conn, $_SESSION['user_id'], "Deleted book (ID: $book_id)");
    }
}

header("Location: view_books.php");
exit();
