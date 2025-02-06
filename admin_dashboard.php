<?php
include 'db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['admin_name']; ?>!</h2>
    <nav>
        <a href="manage_books.php">Manage Books</a> | 
        <a href="logout.php">Logout</a>
    </nav>

    <h3>Sales Report</h3>
    <!-- This line inserts the contents of view_sales.php -->
    <?php include 'view_sales.php'; ?>

</body>
</html>
