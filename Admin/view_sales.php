<?php 
include '../config.php';

// Get filter inputs from GET parameters safely
$book_title_filter = isset($_GET['book_title']) ? trim($_GET['book_title']) : '';
$customer_filter = isset($_GET['customer']) ? trim($_GET['customer']) : '';
$specific_date = isset($_GET['specific_date']) ? trim($_GET['specific_date']) : '';
$start_date = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
$end_date = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';

// Build dynamic WHERE clauses based on filters
$whereClauses = [];
$params = [];
$types = '';

// Book Title filter
if ($book_title_filter !== '') {
    $whereClauses[] = "orders.book_title LIKE ?";
    $params[] = "%" . $book_title_filter . "%";
    $types .= 's';
}

// Customer filter (username)
if ($customer_filter !== '') {
    $whereClauses[] = "users.username LIKE ?";
    $params[] = "%" . $customer_filter . "%";
    $types .= 's';
}

// Date filters
if ($specific_date !== '') {
    // Filter by exact date (ignoring time)
    $whereClauses[] = "DATE(orders.sale_date) = ?";
    $params[] = $specific_date;
    $types .= 's';
} else {
    // Filter by date range if both start and end dates provided
    if ($start_date !== '' && $end_date !== '') {
        $whereClauses[] = "DATE(orders.sale_date) BETWEEN ? AND ?";
        $params[] = $start_date;
        $params[] = $end_date;
        $types .= 'ss';
    } elseif ($start_date !== '') {
        $whereClauses[] = "DATE(orders.sale_date) >= ?";
        $params[] = $start_date;
        $types .= 's';
    } elseif ($end_date !== '') {
        $whereClauses[] = "DATE(orders.sale_date) <= ?";
        $params[] = $end_date;
        $types .= 's';
    }
}

// Combine WHERE clauses
$whereSql = '';
if (count($whereClauses) > 0) {
    $whereSql = "WHERE " . implode(" AND ", $whereClauses);
}

// Prepare sales query with filters
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

// Prepare total sales sum query with same filters
$total_sales_sql = "SELECT SUM(total_amount) AS total_sales FROM orders $whereSql";

$sales_stmt = $conn->prepare($sales_sql);
$total_stmt = $conn->prepare($total_sales_sql);

// Bind params dynamically if needed
if (!empty($params)) {
    $sales_stmt->bind_param($types, ...$params);
    $total_stmt->bind_param($types, ...$params);
}

$sales_stmt->execute();
$sales_result = $sales_stmt->get_result();

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
        /* Keep your existing styles and add some for the new form */
        form.filter-form {
            margin-bottom: 30px;
            background: #fff;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        form.filter-form label {
            font-weight: 600;
            margin-right: 8px;
        }
        form.filter-form input[type="text"],
        form.filter-form input[type="date"] {
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            min-width: 180px;
        }
        form.filter-form button {
            background-color: #3498db;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 18px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
            align-self: center;
        }
        form.filter-form button:hover {
            background-color: #2980b9;
        }
        /* Your existing table styles below */
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
        .deleted-book {
            color: #999;
            font-style: italic;
        }
        .total-sales {
            font-size: 1.2em;
            font-weight: 600;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<?php include 'admin_header.php'; ?>

<div class="container">
    <h2>Sales Report</h2>

    <!-- New Search Filter Form -->
    <form method="get" class="filter-form">
        <div>
            <label for="book_title">Book Title:</label>
            <input type="text" id="book_title" name="book_title" value="<?php echo htmlspecialchars($book_title_filter); ?>" placeholder="Search by book title">
        </div>

        <div>
            <label for="customer">Customer:</label>
            <input type="text" id="customer" name="customer" value="<?php echo htmlspecialchars($customer_filter); ?>" placeholder="Search by customer name">
        </div>

        <div>
            <label for="specific_date">Specific Date:</label>
            <input type="date" id="specific_date" name="specific_date" value="<?php echo htmlspecialchars($specific_date); ?>">
        </div>

        <div>
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
        </div>

        <div>
            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
        </div>

        <div>
            <button type="submit">Search</button>
        </div>
    </form>

    <!-- Total Sales Display -->
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
        <?php if ($sales_result->num_rows === 0): ?>
            <tr><td colspan="8" style="text-align:center; font-style: italic; color: #666;">No records found.</td></tr>
        <?php else: ?>
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
        <?php endif; ?>
        </tbody>
    </table>

    <button class="print-btn" onclick="window.print()">Print Report</button>
</div>
</body>
</html>
