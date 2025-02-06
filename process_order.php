<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $total_amount = $_POST['total_amount'];
    
    // Insert order into database
    $sql = "INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'completed')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("id", $user_id, $total_amount);
    
    if ($stmt->execute()) {
        $order_id = $conn->insert_id;
        
        // Clear the cart
        $_SESSION['cart'] = [];
        $_SESSION['total_books'] = 0;
        
        // Redirect to review form
        header("Location: review.php?order_id=" . $order_id);
        exit();
    }
}
