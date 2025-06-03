<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // NEW: Get shipping details from form
    $shipping_address = $_POST['shipping_address'];
    $shipping_city = $_POST['shipping_city'];
    $shipping_postal_code = $_POST['shipping_postal_code'];
    
    foreach ($_SESSION['cart'] as $book_id => $item) {
        // Get book details including price
        $sql = "SELECT title, price FROM books WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $book = $stmt->get_result()->fetch_assoc();
        
        $quantity = is_array($item) ? $item['quantity'] : $item;
        $total_amount = $book['price'] * $quantity;

        // NEW: Insert with additional shipping fields
        $sql = "INSERT INTO orders (
            user_id, book_id, book_title, quantity, total_amount, total_price,
            status, created_at, sale_date, shipping_address, shipping_city, shipping_postal_code
        ) VALUES (?, ?, ?, ?, ?, ?, 'successful', NOW(), NOW(), ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "iisiddsss",
            $user_id, $book_id, $book['title'], $quantity, $total_amount, $total_amount,
            $shipping_address, $shipping_city, $shipping_postal_code
        );
        $stmt->execute();
        
        // Update stock count here
        $update_stock = "UPDATE books SET stock = stock - ? WHERE id = ?";
        $stmt = $conn->prepare($update_stock);
        $stmt->bind_param("ii", $quantity, $book_id);
        $stmt->execute();
    }
    
    unset($_SESSION['cart']);
    header("Location: order_confirmation.php");
    exit();
}
?>
