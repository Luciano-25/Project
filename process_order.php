<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Get shipping details from form
    $full_name = $_POST['full_name'];
    $email = $_POST['email']; // Not stored, just for receipt/display if needed
    $phone = $_POST['phone'];
    $shipping_address = $_POST['shipping_address'];
    $shipping_city = $_POST['shipping_city'];
    $shipping_postal_code = $_POST['shipping_postal_code'];

    // ✅ Update shipping info in users table
    $update_user_sql = "UPDATE users SET full_name = ?, phone = ?, shipping_address = ?, shipping_city = ?, shipping_postal_code = ? WHERE id = ?";
    $stmt = $conn->prepare($update_user_sql);
    $stmt->bind_param("sssssi", $full_name, $phone, $shipping_address, $shipping_city, $shipping_postal_code, $user_id);
    $stmt->execute();

    // Set order status
    $status = "Order Pending (Estimated arrival in 6 days)";

    // Loop through cart and insert orders
    foreach ($_SESSION['cart'] as $book_id => $item) {
        $sql = "SELECT title, price FROM books WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $book = $stmt->get_result()->fetch_assoc();

        $book_title = $book['title'];
        $unit_price = (float)$book['price'];
        $quantity = is_array($item) ? (int)$item['quantity'] : (int)$item;
        $total_price = $unit_price * $quantity;

        $insert_sql = "INSERT INTO orders (
            user_id, book_id, book_title, quantity, unit_price, total_price, total_amount,
            status, created_at, sale_date,
            shipping_address, shipping_city, shipping_postal_code,
            full_name, phone
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param(
            'iisddddssssss',
            $user_id,
            $book_id,
            $book_title,
            $quantity,
            $unit_price,
            $total_price,
            $total_price,
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

        // Update stock
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
