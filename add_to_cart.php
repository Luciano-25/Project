<?php
session_start();
require_once 'config.php'; // Ensure this connects to the database

// Initialize cart and total_books if they don't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
    $_SESSION['total_books'] = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = $_POST['book_id'];
    $quantity_to_add = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // Get the book stock from database
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
            // Between 50 and 100 — allow up to the stock limit
            $max_qty = $stock;
        }

        // If book is already in cart, increase quantity
        if (isset($_SESSION['cart'][$book_id])) {
            $_SESSION['cart'][$book_id] += $quantity_to_add;
        } else {
            $_SESSION['cart'][$book_id] = $quantity_to_add;
        }

        // Cap the quantity to max allowed
        if ($_SESSION['cart'][$book_id] > $max_qty) {
            $_SESSION['cart'][$book_id] = $max_qty;
        }

        // Update total distinct books
        $_SESSION['total_books'] = count($_SESSION['cart']);

        // Redirect to cart
        header('Location: cart.php');
        exit();
    } else {
        // Book not found, go back to products page
        header('Location: products.php');
        exit();
    }
}
?>
