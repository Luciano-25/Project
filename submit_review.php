<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $rating = $_POST['rating'];
    $title = $_POST['review_title'];
    $text = $_POST['review_text'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO reviews (order_id, user_id, rating, title, review_text, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiss", $order_id, $user_id, $rating, $title, $text);
    $stmt->execute();

    header("Location: profile.php");
    exit();
}
