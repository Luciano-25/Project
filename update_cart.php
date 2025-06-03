<?php
session_start();
require_once 'config.php';

if (isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];

    // Get the stock for this book
    $stmt = $conn->prepare("SELECT stock FROM books WHERE id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $book = $result->fetch_assoc();
        $stock = (int)$book['stock'];

        // Determine max quantity allowed
        if ($stock < 50) {
            $max_qty = 1;
        } elseif ($stock > 100) {
            $max_qty = 5;
        } else {
            $max_qty = $stock;
        }

        // Get current quantity
        $current_item = $_SESSION['cart'][$book_id];
        $current_quantity = is_array($current_item) ? $current_item['quantity'] : $current_item;

        // Adjust quantity based on action
        if (isset($_POST['increase'])) {
            $new_quantity = $current_quantity + 1;
            if ($new_quantity > $max_qty) {
                $new_quantity = $max_qty;
            }
        } elseif (isset($_POST['decrease'])) {
            $new_quantity = max(1, $current_quantity - 1);
        } else {
            $new_quantity = $current_quantity; // no action
        }

        // Save back to session
        if (is_array($current_item)) {
            $_SESSION['cart'][$book_id]['quantity'] = $new_quantity;
        } else {
            $_SESSION['cart'][$book_id] = $new_quantity;
        }
    }
}

header('Location: cart.php');
exit();
