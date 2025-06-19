<?php
session_start();
require_once '../config.php';

// Access control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../login.php");
    exit();
}

// Fetch logs
$sql = "SELECT l.*, u.username 
        FROM admin_log l 
        JOIN users u ON l.admin_id = u.id 
        ORDER BY l.log_time DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Activity Logs</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        .container {
            max-width: 1100px;
            margin: auto;
            padding: 20px;
        }
        h2 {
            margin-bottom: 20px;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #2c3e50;
            color: #fff;
            text-align: left;
        }
        tr:hover {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
<?php include 'superadmin_header.php'; ?>

<div class="container">
    <h2>📝 Admin Activity Logs</h2>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Admin Username</th>
                    <th>Action</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($log = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($log['username']) ?></td>
                        <td><?= htmlspecialchars($log['action']) ?></td>
                        <td><?= date('d M Y, h:i A', strtotime($log['log_time'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No log entries found.</p>
    <?php endif; ?>
</div>
</body>
</html>
