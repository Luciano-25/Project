<?php
session_start();

// Initialize cart and total_books if they don't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
    $_SESSION['total_books'] = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = $_POST['book_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    $_SESSION['cart'][$book_id] = $quantity;
    $_SESSION['total_books'] = count($_SESSION['cart']);

    // Redirect directly to cart page after adding to cart
    header('Location: cart.php');
    exit();
}
