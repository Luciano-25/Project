<?php 
include '../config.php';

// Fetch all sales
$sales_sql = "SELECT 
                orders.id,
                orders.book_id,
                orders.book_title,
                orders.quantity,
                orders.total_amount,
                orders.created_at AS sale_date,
                orders.shipping_address,
                orders.shipping_city,
                orders.shipping_postal_code,
                users.username,
                IF(books.id IS NULL, 0, 1) AS book_exists
            FROM orders
            LEFT JOIN books ON orders.book_id = books.id
            JOIN users ON orders.user_id = users.id
            ORDER BY orders.created_at DESC";

$sales_result = $conn->query($sales_sql);

// Total revenue calculation
$revenue_sql = "SELECT SUM(total_amount) AS total_revenue FROM orders";
$revenue_result = $conn->query($revenue_sql);
$revenue_row = $revenue_result->fetch_assoc();
$total_revenue = $revenue_row['total_revenue'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report - BookHaven</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        h2, h3 {
            margin-top: 30px;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
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
            background-color: #f2f2f2;
        }

        .print-btn, .report-link-btn {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }

        .print-btn:hover, .report-link-btn:hover {
            background-color: #2980b9;
        }

        .report-link-btn {
            background-color: #27ae60;
        }

        .report-link-btn:hover {
            background-color: #1e8449;
        }

        .deleted-book {
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
<?php include 'admin_header.php'; ?>

<div class="container">
    <h2>Sales Report</h2>

    <h3>Total Revenue</h3>
    <p><strong>RM <?php echo number_format($total_revenue, 2); ?></strong></p>

    <h3>Sales Transactions</h3>
    <table>
        <tr>
            <th>Customer</th>
            <th>Book Title</th>
            <th>Quantity</th>
            <th>Total Amount (RM)</th>
            <th>Sale Date</th>
            <th>Shipping Address</th>
            <th>City</th>
            <th>Postal Code</th>
        </tr>
        <?php while ($row = $sales_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td>
                    <?php
                    if (!$row['book_exists']) {
                        echo "<span class='deleted-book'>Deleted Book (" . htmlspecialchars($row['book_title']) . ")</span>";
                    } else {
                        echo htmlspecialchars($row['book_title']);
                    }
                    ?>
                </td>
                <td><?php echo $row['quantity']; ?></td>
                <td><?php echo number_format($row['total_amount'], 2); ?></td>
                <td><?php echo date('d M Y, h:i A', strtotime($row['sale_date'])); ?></td>
                <td><?php echo htmlspecialchars($row['shipping_address']); ?></td>
                <td><?php echo htmlspecialchars($row['shipping_city']); ?></td>
                <td><?php echo htmlspecialchars($row['shipping_postal_code']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <button class="print-btn" onclick="window.print()">Print Report</button><br><br>

    <a href="book_customers.php" class="report-link-btn">View Customers by Book</a>
</div>
</body>
</html>
