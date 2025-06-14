<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Get shipping details from form
    $full_name = $_POST['full_name'];
    $email = $_POST['email']; // Optional: not stored
    $phone = $_POST['phone'];
    $shipping_address = $_POST['shipping_address'];
    $shipping_city = $_POST['shipping_city'];
    $shipping_postal_code = $_POST['shipping_postal_code'];

    // Set default order status
    $status = "Order Pending (Estimated arrival in 6 days)";

    foreach ($_SESSION['cart'] as $book_id => $item) {
        // Get book details
        $sql = "SELECT title, price FROM books WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $book = $stmt->get_result()->fetch_assoc();

        $book_title = $book['title'];
        $unit_price = (float)$book['price'];
        $quantity = is_array($item) ? (int)$item['quantity'] : (int)$item;
        $total_price = $unit_price * $quantity;

        // Insert order with correct bind_param types and values
        $sql = "INSERT INTO orders (
            user_id, book_id, book_title, quantity, unit_price, total_price, total_amount,
            status, created_at, sale_date,
            shipping_address, shipping_city, shipping_postal_code,
            full_name, phone
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'iisddddssssss',
            $user_id,
            $book_id,
            $book_title,
            $quantity,
            $unit_price,
            $total_price,
            $total_price, // total_amount
            $status,
            $shipping_address,
            $shipping_city,
            $shipping_postal_code,
            $full_name,
            $phone
        );

        if (!$stmt->execute()) {
            echo "Error inserting order: " . $stmt->error;
            exit();
        }

        // Update book stock
        $update_stock = "UPDATE books SET stock = stock - ? WHERE id = ?";
        $stmt = $conn->prepare($update_stock);
        $stmt->bind_param("ii", $quantity, $book_id);
        $stmt->execute();
    }

    // Clear cart and redirect
    unset($_SESSION['cart']);
    header("Location: order_confirmation.php");
    exit();
}
?>

