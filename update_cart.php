<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];
    
    if (isset($_POST['increase'])) {
        $_SESSION['cart'][$book_id]++;
    } elseif (isset($_POST['decrease'])) {
        $_SESSION['cart'][$book_id]--;
        if ($_SESSION['cart'][$book_id] <= 0) {
            unset($_SESSION['cart'][$book_id]);
        }
    }
}

header('Location: cart.php');
exit();
