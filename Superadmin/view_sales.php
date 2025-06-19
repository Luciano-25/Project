<?php 
session_start();
require_once '../config.php';

// ✅ Block unauthorized access
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'superadmin'])) {
    header("Location: ../login.php");
    exit();
}

// ✅ Load appropriate header
$header_file = $_SESSION['role'] === 'superadmin' ? 'superadmin_header.php' : 'admin_header.php';
include $header_file;

// Initialize filter values
$search_username = $_GET['username'] ?? '';
$search_book = $_GET['book_title'] ?? '';
$search_date = $_GET['date'] ?? '';
$search_start = $_GET['start_date'] ?? '';
$search_end = $_GET['end_date'] ?? '';

// Build WHERE conditions dynamically
$conditions = [];
$params = [];
$types = '';

if ($search_username) {
    $conditions[] = 'users.username LIKE ?';
    $params[] = "%$search_username%";
    $types .= 's';
}
if ($search_book) {
    $conditions[] = 'orders.book_title LIKE ?';
    $params[] = "%$search_book%";
    $types .= 's';
}
if ($search_date) {
    $conditions[] = 'DATE(orders.created_at) = ?';
    $params[] = $search_date;
    $types .= 's';
}
if ($search_start && $search_end) {
    $conditions[] = 'DATE(orders.created_at) BETWEEN ? AND ?';
    $params[] = $search_start;
    $params[] = $search_end;
    $types .= 'ss';
}

$where_sql = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

// Sales query
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

$stmt = $conn->prepare($sales_sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$sales_result = $stmt->get_result();

// Revenue query
$revenue_sql = "SELECT SUM(orders.total_amount) AS total_revenue
                FROM orders
                JOIN users ON orders.user_id = users.id
                $where_sql";
$rev_stmt = $conn->prepare($revenue_sql);
if ($params) {
    $rev_stmt->bind_param($types, ...$params);
}
$rev_stmt->execute();
$revenue_result = $rev_stmt->get_result();
$revenue_row = $revenue_result->fetch_assoc();
$total_revenue = $revenue_row['total_revenue'] ?? 0.00;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report - BookHaven</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        h2, h3 { margin-top: 30px; margin-bottom: 20px; color: #2c3e50; }
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
        th { background-color: #2c3e50; color: #fff; }
        tr:hover { background-color: #f2f2f2; }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            color: white;
            text-decoration: none;
            margin-top: 10px;
            display: inline-block;
        }
        .print-btn { background-color: #3498db; }
        .print-btn:hover { background-color: #2980b9; }
        .filter-form input {
            padding: 6px;
            margin-right: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .deleted-book {
            color: #999;
            font-style: italic;
        }

        .back-btn {
            background-color: #95a5a6;
            text-decoration: none;
        }

        .back-btn:hover {
            background-color: #7f8c8d;
        }

        /* Print styles */
        @media print {
            body * {
                visibility: hidden;
            }
            .print-area, .print-area * {
                visibility: visible;
            }
            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .print-btn, .filter-form, .back-btn {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📊 Sales Report</h2>

    <!-- Filter Form -->
    <form method="get" class="filter-form">
        <input type="text" name="username" placeholder="Search by customer" value="<?= htmlspecialchars($search_username) ?>">
        <input type="text" name="book_title" placeholder="Search by book title" value="<?= htmlspecialchars($search_book) ?>">

        <label for="date">Specific Date:</label>
        <input type="date" name="date" id="date" value="<?= htmlspecialchars($search_date) ?>">

        <label for="start_date">From:</label>
        <input type="date" name="start_date" id="start_date" value="<?= htmlspecialchars($search_start) ?>">

        <label for="end_date">To:</label>
        <input type="date" name="end_date" id="end_date" value="<?= htmlspecialchars($search_end) ?>">

        <button type="submit" class="btn print-btn">Apply Filters</button>
    </form>

    <div class="print-area">
        <h3>💰 Total Revenue</h3>
        <p><strong>RM <?= number_format($total_revenue, 2); ?></strong></p>

        <h3>🧾 Sales Transactions</h3>
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
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td>
                        <?php
                        if (!$row['book_exists']) {
                            echo "<span class='deleted-book'>Deleted Book (" . htmlspecialchars($row['book_title']) . ")</span>";
                        } else {
                            echo htmlspecialchars($row['book_title']);
                        }
                        ?>
                    </td>
                    <td><?= $row['quantity'] ?></td>
                    <td><?= number_format($row['total_amount'], 2) ?></td>
                    <td><?= date('d M Y, h:i A', strtotime($row['sale_date'])) ?></td>
                    <td><?= htmlspecialchars($row['shipping_address']) ?></td>
                    <td><?= htmlspecialchars($row['shipping_city']) ?></td>
                    <td><?= htmlspecialchars($row['shipping_postal_code']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <button class="btn print-btn" onclick="window.print()">🖨️ Print Report</button>
    <a href="<?= $_SESSION['role'] === 'superadmin' ? 'superadmin_dashboard.php' : 'admin_dashboard.php' ?>" class="btn back-btn">← Back to Dashboard</a>
</div>
</body>
</html>
