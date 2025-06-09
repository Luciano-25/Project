<?php
include '../config.php';

$search_book = isset($_GET['book']) ? trim($_GET['book']) : '';
$results = [];

if (!empty($search_book)) {
    $stmt = $conn->prepare("
        SELECT orders.book_title, users.username, orders.shipping_address, orders.shipping_city, orders.shipping_postal_code
        FROM orders
        JOIN users ON orders.user_id = users.id
        WHERE orders.book_title LIKE ?
    ");
    $search_term = "%{$search_book}%";
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $results = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customers by Book - BookHaven</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
        }

        .container {
            max-width: 900px;
            margin: auto;
            padding: 20px;
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 25px;
        }

        input[type="text"] {
            padding: 10px;
            width: 60%;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-right: 10px;
        }

        button {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            background-color: #3498db;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #2980b9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #2c3e50;
            color: #fff;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .back-btn {
            display: inline-block;
            margin-top: 30px;
            padding: 10px 20px;
            background-color: #95a5a6;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }

        .back-btn:hover {
            background-color: #7f8c8d;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>View Customers Who Purchased a Book</h2>

    <form method="get">
        <input type="text" name="book" placeholder="Enter book title" value="<?php echo htmlspecialchars($search_book); ?>">
        <button type="submit">Search</button>
    </form>

    <?php if (!empty($search_book)): ?>
        <h3>Results for "<?php echo htmlspecialchars($search_book); ?>"</h3>
        <?php if ($results->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Customer</th>
                    <th>Shipping Address</th>
                    <th>City</th>
                    <th>Postal Code</th>
                </tr>
                <?php while ($row = $results->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['shipping_address']); ?></td>
                        <td><?php echo htmlspecialchars($row['shipping_city']); ?></td>
                        <td><?php echo htmlspecialchars($row['shipping_postal_code']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No customers found for that book.</p>
        <?php endif; ?>
    <?php endif; ?>

    <a href="view_sales.php" class="back-btn">← Back to Sales Report</a>
</div>
</body>
</html>
