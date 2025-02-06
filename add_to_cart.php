<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = $_POST['book_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
        $_SESSION['total_books'] = 0;
    }
    
    $_SESSION['cart'][$book_id] = $quantity;
    $_SESSION['total_books'] = count($_SESSION['cart']);
    
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
