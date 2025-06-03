<?php
include '../config.php';
include 'admin_header.php';

// Get total books
$books_query = "SELECT COUNT(*) as total_books FROM books";
$books_result = $conn->query($books_query);
$total_books = $books_result->fetch_assoc()['total_books'];

// Get total orders
$orders_query = "SELECT COUNT(*) as total_orders FROM orders";
$orders_result = $conn->query($orders_query);
$total_orders = $orders_result->fetch_assoc()['total_orders'];

// Get total customers
$customers_query = "SELECT COUNT(*) as total_customers FROM users WHERE role = 'user'";
$customers_result = $conn->query($customers_query);
$total_customers = $customers_result->fetch_assoc()['total_customers'];

// Get total revenue
$revenue_query = "SELECT SUM(total_amount) as total_revenue FROM orders";
$revenue_result = $conn->query($revenue_query);
$total_revenue = $revenue_result->fetch_assoc()['total_revenue'] ?: 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admindash.css">
    <style>
        /* Optional: make the entire card clickable and remove default link styles */
        a.stat-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }
        a.stat-link:hover {
            background-color: #f0f0f0; /* subtle hover effect */
        }
    </style>
</head>
<body>

    <div class="dashboard-container">
        <div class="stats-grid">

            <a href="booklist.php" class="stat-link">
                <div class="stat-card books">
                    <i class="fas fa-book"></i>
                    <div class="stat-info">
                        <h3>Total Books</h3>
                        <p><?php echo $total_books; ?></p>
                    </div>
                </div>
            </a>
            
            <a href="view_sales.php" class="stat-link">
                <div class="stat-card orders">
                    <i class="fas fa-shopping-cart"></i>
                    <div class="stat-info">
                        <h3>Total Orders</h3>
                        <p><?php echo $total_orders; ?></p>
                    </div>
                </div>
            </a>
            
            <a href="customer_list.php" class="stat-link">
                <div class="stat-card customers">
                    <i class="fas fa-users"></i>
                    <div class="stat-info">
                        <h3>Total Customers</h3>
                        <p><?php echo $total_customers; ?></p>
                    </div>
                </div>
            </a>
            
            <a href="total_revenue.php" class="stat-link">
                <div class="stat-card revenue">
                    <i class="fas fa-dollar-sign"></i>
                    <div class="stat-info">
                        <h3>Total Revenue</h3>
                        <p>RM <?php echo number_format($total_revenue, 2); ?></p>
                    </div>
                </div>
            </a>

        </div>
    </div>

</body>
</html>
