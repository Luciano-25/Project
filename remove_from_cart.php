<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];
    unset($_SESSION['cart'][$book_id]);
}

header('Location: cart.php');
exit();
