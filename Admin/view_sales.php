<?php
include '../config.php';

// Check if filter type is set
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Dynamic SQL for total revenue
switch ($filter) {
    case 'monthly':
        $revenue_sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS period, SUM(total_amount) AS total_revenue FROM orders GROUP BY period ORDER BY period DESC";
        $label = "Monthly Revenue";
        break;
    case 'yearly':
        $revenue_sql = "SELECT YEAR(created_at) AS period, SUM(total_amount) AS total_revenue FROM orders GROUP BY period ORDER BY period DESC";
        $label = "Yearly Revenue";
        break;
    default:
        $revenue_sql = "SELECT SUM(total_amount) AS total_revenue FROM orders";
        $label = "Total Revenue";
}

// Updated sales query to include shipping details
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
                books.id AS book_exists
            FROM orders
            LEFT JOIN books ON orders.book_id = books.id
            JOIN users ON orders.user_id = users.id
            ORDER BY orders.created_at DESC";

$sales_result = $conn->query($sales_sql);

// Revenue query
$revenue_result = $conn->query($revenue_sql);
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

        form {
            margin-bottom: 30px;
        }

        label {
            font-weight: 500;
            margin-right: 10px;
        }

        select {
            padding: 6px 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
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

        .print-btn {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .print-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
<?php include 'admin_header.php'; ?>

<div class="container">
    <h2>Sales Report</h2>

    <!-- Revenue Filter -->
    <form method="get">
        <label for="filter">View Revenue By:</label>
        <select name="filter" id="filter" onchange="this.form.submit()">
            <option value="all" <?php if($filter === 'all') echo 'selected'; ?>>All Time</option>
            <option value="monthly" <?php if($filter === 'monthly') echo 'selected'; ?>>Monthly</option>
            <option value="yearly" <?php if($filter === 'yearly') echo 'selected'; ?>>Yearly</option>
        </select>
    </form>

    <h3><?php echo $label; ?></h3>

    <?php if ($filter === 'all'): ?>
        <?php $row = $revenue_result->fetch_assoc(); ?>
        <p>Total Revenue: <strong>RM <?php echo number_format($row['total_revenue'], 2); ?></strong></p>
    <?php else: ?>
        <table>
            <tr>
                <th><?php echo $filter === 'monthly' ? 'Month' : 'Year'; ?></th>
                <th>Total Revenue (RM)</th>
            </tr>
            <?php while($row = $revenue_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['period']); ?></td>
                    <td><?php echo number_format($row['total_revenue'], 2); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>

    <!-- Sales List Table -->
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
                <td><?php echo htmlspecialchars($row['shipping_address']); ?></td>
                <td><?php echo htmlspecialchars($row['shipping_city']); ?></td>
                <td><?php echo htmlspecialchars($row['shipping_postal_code']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <button class="print-btn" onclick="window.print()">Print Report</button>
</div>
</body>
</html>
