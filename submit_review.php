<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $rating = $_POST['rating'];
    $review_title = $_POST['review_title'];
    $review_text = $_POST['review_text'];
    $user_id = $_SESSION['user_id'];
    
    // Insert review into database
    $sql = "INSERT INTO reviews (order_id, user_id, rating, title, review_text) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiss", $order_id, $user_id, $rating, $review_title, $review_text);
    
    if ($stmt->execute()) {
        // Redirect to main page after successful submission
        header("Location: index.php");
        exit();
    }
}
