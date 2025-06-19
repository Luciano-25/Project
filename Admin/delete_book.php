<?php
session_start();
require_once '../config.php';
require_once 'log_helper.php'; // ✅ Include logging helper

if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    // Get image path and title
    $sql = "SELECT image_url, title FROM books WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();

    if ($book) {
        // Delete image file
        if ($book['image_url']) {
            $image_path = "../" . $book['image_url'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        // Delete book record
        $delete_sql = "DELETE FROM books WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $book_id);
        $stmt->execute();

        // ✅ Log deletion
        log_admin_action($conn, $_SESSION['user_id'], "Deleted book: {$book['title']} (ID {$book_id})");
    }
}

header("Location: view_books.php");
exit();
