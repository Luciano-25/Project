<?php 
include '../config.php';

// Get filter for revenue grouping
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Prepare revenue SQL based on filter
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

// Execute revenue query
$revenue_result = $conn->query($revenue_sql);

// Get search/filter inputs
$search_book = isset($_GET['search_book']) ? trim($_GET['search_book']) : '';
$search_customer = isset($_GET['search_customer']) ? trim($_GET['search_customer']) : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Build dynamic WHERE conditions for sales query
$where = [];
$params = [];
$types = '';

// Filter by book title (LIKE)
if ($search_book !== '') {
    $where[] = "orders.book_title LIKE ?";
    $params[] = '%' . $search_book . '%';
    $types .= 's';
}

// Filter by customer username (LIKE)
if ($search_customer !== '') {
    $where[] = "users.username LIKE ?";
    $params[] = '%' . $search_customer . '%';
    $types .= 's';
}

// Filter by start date (created_at >= start_date 00:00:00)
if ($start_date !== '') {
    $where[] = "orders.created_at >= ?";
    $params[] = $start_date . " 00:00:00";
    $types .= 's';
}

// Filter by end date (created_at <= end_date 23:59:59)
if ($end_date !== '') {
    $where[] = "orders.created_at <= ?";
    $params[] = $end_date . " 23:59:59";
    $types .= 's';
}

// Combine WHERE clauses
$where_sql = '';
if (count($where) > 0) {
    $where_sql = 'WHERE ' . implode(' AND ', $where);
}

// Sales query with filters
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
            $where_sql
            ORDER BY orders.created_at DESC";

// Prepare and execute statement safely
$stmt = $conn->prepare($sales_sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$sales_result = $stmt->get_result();

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
            background: #f9f9f9;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }

        label {
            font-weight: 500;
            margin-right: 6px;
            white-space: nowrap;
        }

        input[type="text"], input[type="date"], select {
            padding: 6px 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            min-width: 150px;
        }

        button[type="submit"] {
            padding: 8px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
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
    </style>
</head>
<body>
<?php include 'admin_header.php'; ?>

<div class="container">
    <h2>Sales Report</h2>

    <!-- Revenue Filter -->
    <form method="get" action="">
        <label for="filter">View Revenue By:</label>
        <select name="filter" id="filter" onchange="this.form.submit()">
            <option value="all" <?php if($filter === 'all') echo 'selected'; ?>>All Time</option>
            <option value="monthly" <?php if($filter === 'monthly') echo 'selected'; ?>>Monthly</option>
            <option value="yearly" <?php if($filter === 'yearly') echo 'selected'; ?>>Yearly</option>
        </select>

        <!-- Search Inputs -->
        <label for="search_book">Book Title:</label>
        <input type="text" name="search_book" id="search_book" placeholder="Search book title" value="<?php echo htmlspecialchars($search_book); ?>">

        <label for="search_customer">Customer Name:</label>
        <input type="text" name="search_customer" id="search_customer" placeholder="Search customer" value="<?php echo htmlspecialchars($search_customer); ?>">

        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" id="start_date" value="<?php echo htmlspecialchars($start_date); ?>">

        <label for="end_date">End Date:</label>
        <input type="date" name="end_date" id="end_date" value="<?php echo htmlspecialchars($end_date); ?>">

        <button type="submit">Search</button>
    </form>

    <h3><?php echo $label; ?></h3>

    <?php if ($filter === 'all'): ?>
        <?php 
        $row = $revenue_result->fetch_assoc(); 
        $total_revenue = $row['total_revenue'] ?? 0;
        ?>
        <p>Total Revenue: <strong>RM <?php echo number_format($total_revenue, 2); ?></strong></p>
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

    <button class="print-btn" onclick="window.print()">Print Report</button>
</div>
</body>
</html>
