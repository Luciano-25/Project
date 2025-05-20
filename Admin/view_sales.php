<?php
include '../config.php';

$sql = "SELECT 
            orders.id,
            orders.book_id,
            orders.book_title,
            orders.quantity,
            orders.total_amount,
            orders.created_at AS sale_date,
            users.username,
            books.id AS book_exists,
            (SELECT SUM(total_amount) FROM orders) AS total_revenue
        FROM orders
        LEFT JOIN books ON orders.book_id = books.id
        JOIN users ON orders.user_id = users.id
        ORDER BY orders.created_at DESC";




$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <title>Sales Report - BookHaven</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<?php include 'admin_header.php'; ?>

    <table>
        <tr>
            <th>Customer</th>
            <th>Book Title</th>
            <th>Quantity</th>
            <th>Total Amount (RM)</th>
            <th>Sale Date</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['username']; ?></td>
                <td>
                    <?php
                    if (is_null($row['book_exists'])) {
                        echo "Deleted Book (" . htmlspecialchars($row['book_title']) . ")";
                    } else {
                        echo htmlspecialchars($row['book_title']);
                    }
                    ?>
                </td>


                <td><?php echo $row['quantity']; ?></td>
                <td><?php echo number_format($row['total_amount'], 2); ?></td>
                <td><?php echo date('d M Y, h:i A', strtotime($row['sale_date'])); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <?php include 'total_revenue.php'; ?>

    <button class="print-btn" onclick="window.print()">Print Report</button>
</body>
</html>
