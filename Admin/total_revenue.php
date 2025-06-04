<?php
include '../config.php';

$sql = "SELECT SUM(total_amount) AS total_revenue FROM orders";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_revenue = $row['total_revenue'];
?>

<div class="revenue-container">
    <h3>Total Revenue</h3>
    <p>RM <?php echo number_format($total_revenue, 2); ?></p>
</div>
