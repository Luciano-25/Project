<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Get shipping details from form
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $shipping_address = $_POST['shipping_address'];
    $shipping_city = $_POST['shipping_city'];
    $shipping_postal_code = $_POST['shipping_postal_code'];

    // Save shipping info to users table
    $update_user = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, shipping_address = ?, shipping_city = ?, shipping_postal_code = ? WHERE id = ?");
    $update_user->bind_param("ssssssi", $full_name, $email, $phone, $shipping_address, $shipping_city, $shipping_postal_code, $user_id);
    $update_user->execute();

    // Order status
    $status = "Order Pending (Estimated arrival in 6 days)";

    foreach ($_SESSION['cart'] as $book_id => $item) {
        // Get book details
        $book_stmt = $conn->prepare("SELECT title, price FROM books WHERE id = ?");
        $book_stmt->bind_param("i", $book_id);
        $book_stmt->execute();
        $book = $book_stmt->get_result()->fetch_assoc();

        $book_title = $book['title'];
        $unit_price = (float)$book['price'];
        $quantity = is_array($item) ? (int)$item['quantity'] : (int)$item;
        $total_price = $unit_price * $quantity;

        // Insert order
        $insert_stmt = $conn->prepare("INSERT INTO orders (
            user_id, book_id, book_title, quantity, unit_price, total_price, total_amount,
            status, created_at, sale_date,
            shipping_address, shipping_city, shipping_postal_code,
            full_name, phone
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?, ?, ?, ?, ?)");

        $insert_stmt->bind_param(
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

        if (!$insert_stmt->execute()) {
            echo "Error placing order: " . $insert_stmt->error;
            exit();
        }

        // Reduce stock
        $update_stock = $conn->prepare("UPDATE books SET stock = stock - ? WHERE id = ?");
        $update_stock->bind_param("ii", $quantity, $book_id);
        $update_stock->execute();
    }

    // Clear cart
    unset($_SESSION['cart']);
    header("Location: order_confirmation.php");
    exit();
}
?>
