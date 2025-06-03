<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin_header.css">
</head>
<body>
    <header class="top-header">
        <div class="header-container">
            <h1 class="site-title">BookHaven Admin</h1>
            <nav class="nav-links">
                <a href="admin_dashboard.php">Dashboard</a>
                <a href="view_books.php"><i class="fas fa-book"></i> Manage Books</a>
                <a href="view_sales.php"><i class="fas fa-chart-line"></i> Sales Report</a>
                <div class="nav-right">
                    <a href="index_admin.php" class="view-site">
                        <i class="fas fa-external-link-alt"></i>
                        View Main Site
                    </a>
                    <a href="../logout.php" class="logout-link">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </nav>
        </div>
    </header>
</body>
</html>