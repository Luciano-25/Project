<?php
// Start session and check if admin is logged in
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

// Include database connection
include '../config.php'; // adjust the path if needed

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Revenue Report - Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        h2 {
            margin-top: 40px;
        }
        table {
            width: 60%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 8px 14px;
            border: 1px solid #ccc;
            text-align: center;
        }
        a {
            text-decoration: none;
            color: blue;
        }
    </style>
</head>
<body>

    <h1>Revenue Report</h1>
    <a href="admin_dashboard.php">← Back to Dashboard</a>

    <?php
    // Monthly Revenue Query
    $monthly_sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, SUM(total_price) AS total_revenue 
                    FROM orders GROUP BY month ORDER BY month DESC";
    $monthly_result = mysqli_query($conn, $monthly_sql);

    // Yearly Revenue Query
    $yearly_sql = "SELECT YEAR(created_at) AS year, SUM(total_price) AS total_revenue 
                   FROM orders GROUP BY year ORDER BY year DESC";
    $yearly_result = mysqli_query($conn, $yearly_sql);
    ?>

    <h2>📅 Monthly Revenue</h2>
    <table>
        <tr>
            <th>Month</th>
            <th>Total Revenue (RM)</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($monthly_result)): ?>
            <tr>
                <td><?= $row['month']; ?></td>
                <td><?= number_format($row['total_revenue'], 2); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <h2>📆 Yearly Revenue</h2>
    <table>
        <tr>
            <th>Year</th>
            <th>Total Revenue (RM)</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($yearly_result)): ?>
            <tr>
                <td><?= $row['year']; ?></td>
                <td><?= number_format($row['total_revenue'], 2); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>
