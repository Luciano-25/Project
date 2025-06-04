<?php
include '../config.php';

$sql = "SELECT SUM(total_amount) AS total_revenue FROM orders";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_revenue = $row['total_revenue'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Total Revenue</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
        }

        .revenue-box {
            max-width: 500px;
            margin: 100px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            border: 1px solid #ddd;
            text-align: center;
        }

        .revenue-box h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: #444;
        }

        .revenue-box .amount {
            font-size: 32px;
            font-weight: bold;
            color: #2ecc71;
        }
    </style>
</head>
<body>
    <div class="revenue-box">
        <h3>Total Revenue</h3>
        <div class="amount">RM <?php echo number_format($total_revenue, 2); ?></div>
    </div>
</body>
</html>
