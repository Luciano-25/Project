<?php
include 'db_connect.php';

$sql = "SELECT sales.id, books.title, sales.quantity, sales.total_price, sales.sale_date 
        FROM sales 
        JOIN books ON sales.book_id = books.id 
        ORDER BY sales.sale_date DESC";

$result = $conn->query($sql);
?>

<h2>Sales Report</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Book Title</th>
        <th>Quantity Sold</th>
        <th>Total Price (RM)</th>
        <th>Sale Date</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['title']; ?></td>
            <td><?php echo $row['quantity']; ?></td>
            <td><?php echo $row['total_price']; ?></td>
            <td><?php echo $row['sale_date']; ?></td>
        </tr>
     <?php include 'total_revenue.php'; ?>

</table>

<button onclick="window.print()">Print Report</button>

