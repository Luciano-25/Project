<?php
include 'db_connect.php';

$sql = "SELECT SUM(total_price) AS total_revenue FROM sales";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_revenue = $row['total_revenue'];
?>

<h3>Total Revenue: RM <?php echo number_format($total_revenue, 2); ?></h3>
