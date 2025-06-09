<?php 
include '../config.php';

// Initialize variables for search
$customer_name = isset($_GET['customer_name']) ? trim($_GET['customer_name']) : '';
$book_title = isset($_GET['book_title']) ? trim($_GET['book_title']) : '';
$start_date = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
$end_date = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';

// Build WHERE conditions and params
$where = [];
$params = [];
$types = '';

if ($customer_name !== '') {
    $where[] = "users.username LIKE ?";
    $params[] = "%" . $customer_name . "%";
    $types .= 's';
}

if ($book_title !== '') {
    $where[] = "orders.book_title LIKE ?";
    $params[] = "%" . $book_title . "%";
    $types .= 's';
}

if ($start_date !== '') {
    $where[] = "orders.sale_date >= ?";
    $params[] = $start_date . " 00:00:00";
    $types .= 's';
}

if ($end_date !== '') {
    $where[] = "orders.sale_date <= ?";
    $params[] = $end_date . " 23:59:59";
    $types .= 's';
}

$whereSql = '';
if (count($where) > 0) {
    $whereSql = 'WHERE ' . implode(' AND ', $where);
}

// Sales query with joins and filter
$sales_sql = "SELECT 
                orders.id,
                orders.book_id,
                orders.book_title,
                orders.quantity,
                orders.total_amount,
                orders.sale_date,
                orders.shipping_address,
                orders.shipping_city,
                orders.shipping_postal_code,
                users.username,
                IF(books.id IS NULL, 0, 1) AS book_exists
            FROM orders
            LEFT JOIN books ON orders.book_id = books.id
            JOIN users ON orders.user_id = users.id
            $whereSql
            ORDER BY orders.sale_date DESC";

$sales_stmt = $conn->prepare($sales_sql);

if (!empty($params)) {
    $sales_stmt->bind_param($types, ...$params);
}

$sales_stmt->execute();
$sales_result = $sales_stmt->get_result();

// Total sales amount query with joins and filter (FIXED here)
$total_sales_sql = "SELECT SUM(orders.total_amount) AS total_sales 
                    FROM orders 
                    LEFT JOIN books ON orders.book_id = books.id
                    JOIN users ON orders.user_id = users.id
                    $whereSql";

$total_stmt = $conn->prepare($total_sales_sql);

if (!empty($params)) {
    $total_stmt->bind_param($types, ...$params);
}

$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_sales = $total_row['total_sales'] ?? 0;
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
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        label {
            font-weight: 500;
            margin-right: 10px;
        }

        input[type="text"], input[type="date"] {
            padding: 6px 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-right: 15px;
        }

        button.search-btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        button.search-btn:hover {
            background-color: #2980b9;
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

        .deleted-book {
            color: #999;
            font-style: italic;
        }

        .total-sales {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 30px;
            color: #27ae60;
        }
    </style>
</head>
<body>
<?php include 'admin_header.php'; ?>

<div class="container">
    <h2>Sales Report</h2>

    <!-- Search form -->
    <form method="get" action="">
        <label for="customer_name">Customer Name:</label>
        <input type="text" name="customer_name" id="customer_name" value="<?php echo htmlspecialchars($customer_name); ?>" placeholder="Enter customer name">

        <label for="book_title">Book Title:</label>
        <input type="text" name="book_title" id="book_title" value="<?php echo htmlspecialchars($book_title); ?>" placeholder="Enter book title">

        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" id="start_date" value="<?php echo htmlspecialchars($start_date); ?>">

        <label for="end_date">End Date:</label>
        <input type="date" name="end_date" id="end_date" value="<?php echo htmlspecialchars($end_date); ?>">

        <button type="submit" class="search-btn">Search</button>
    </form>

    <div class="total-sales">
        Total Sales Amount: RM <?php echo number_format($total_sales, 2); ?>
    </div>

    <!-- Sales List Table -->
    <table>
        <thead>
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
        </thead>
        <tbody>
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
                <td><?php echo (int)$row['quantity']; ?></td>
                <td><?php echo number_format($row['total_amount'], 2); ?></td>
                <td><?php echo date('d M Y, h:i A', strtotime($row['sale_date'])); ?></td>
                <td><?php echo htmlspecialchars($row['shipping_address']); ?></td>
                <td><?php echo htmlspecialchars($row['shipping_city']); ?></td>
                <td><?php echo htmlspecialchars($row['shipping_postal_code']); ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <button class="print-btn" onclick="window.print()">Print Report</button>
</div>

</body>
</html>
