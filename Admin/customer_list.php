<?php
// customer_list.php
include '../config.php';
include 'admin_header.php';

// Only select users with role 'user'
$customers = $conn->query("SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer List - Admin</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        .styled-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .styled-table th, .styled-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .styled-table th {
            background-color: #2c3e50;
            color: #fff;
        }
        h2 {
            margin-bottom: 20px;
            padding-top: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>All Customers</h2>
    <table class="styled-table">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($customer = $customers->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($customer['username']) ?></td>
                <td><?= htmlspecialchars($customer['email']) ?></td>
                <td><?= date('d M Y', strtotime($customer['created_at'])) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
