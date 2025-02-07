<?php
session_start();
require_once 'config.php';

if (isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];
    
    // Get current cart item
    $current_item = $_SESSION['cart'][$book_id];
    $current_quantity = is_array($current_item) ? $current_item['quantity'] : $current_item;

    if (isset($_POST['increase'])) {
        $new_quantity = (int)$current_quantity + 1;
    } elseif (isset($_POST['decrease'])) {
        $new_quantity = (int)$current_quantity > 1 ? (int)$current_quantity - 1 : 1;
    }

    // Update quantity while preserving other item details
    if (is_array($current_item)) {
        $_SESSION['cart'][$book_id]['quantity'] = $new_quantity;
    } else {
        $_SESSION['cart'][$book_id] = $new_quantity;
    }
}

header('Location: cart.php');
exit();
