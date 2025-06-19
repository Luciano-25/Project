<?php
include '../config.php';
include 'superadmin_header.php';

// Get total books
$books_result = $conn->query("SELECT COUNT(*) as total_books FROM books");
$total_books = $books_result->fetch_assoc()['total_books'];

// Get total orders
$orders_result = $conn->query("SELECT COUNT(*) as total_orders FROM orders");
$total_orders = $orders_result->fetch_assoc()['total_orders'];

// Get total customers
$customers_result = $conn->query("SELECT COUNT(*) as total_customers FROM users WHERE role = 'user'");
$total_customers = $customers_result->fetch_assoc()['total_customers'];

// Get total revenue
$revenue_result = $conn->query("SELECT SUM(total_amount) as total_revenue FROM orders");
$total_revenue = $revenue_result->fetch_assoc()['total_revenue'] ?: 0;

// Get recent orders
$recent_orders = $conn->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");

// Low stock books
$low_stock = $conn->query("SELECT * FROM books WHERE stock <= 5 ORDER BY stock ASC LIMIT 5");

// Monthly revenue data for chart
$monthly_data = [];
for ($m = 1; $m <= 12; $m++) {
    $stmt = $conn->prepare("SELECT SUM(total_amount) as total FROM orders WHERE MONTH(sale_date) = ? AND YEAR(sale_date) = YEAR(CURDATE())");
    $stmt->bind_param("i", $m);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $monthly_data[] = $result['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Superadmin Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="admindash.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    a.stat-link {
      text-decoration: none;
      color: inherit;
      display: block;
    }
    a.stat-link:hover {
      background-color: #f0f0f0;
    }
    .section {
      margin-top: 40px;
    }
    .recent-table, .info-list {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    .recent-table th, .recent-table td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: left;
    }
    .info-list li {
      margin: 5px 0;
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
          <p><?= $total_books ?></p>
        </div>
      </div>
    </a>

    <a href="view_sales.php" class="stat-link">
      <div class="stat-card orders">
        <i class="fas fa-shopping-cart"></i>
        <div class="stat-info">
          <h3>Total Orders</h3>
          <p><?= $total_orders ?></p>
        </div>
      </div>
    </a>

    <a href="customer_list.php" class="stat-link">
      <div class="stat-card customers">
        <i class="fas fa-users"></i>
        <div class="stat-info">
          <h3>Total Customers</h3>
          <p><?= $total_customers ?></p>
        </div>
      </div>
    </a>

    <a href="total_revenue.php" class="stat-link">
      <div class="stat-card revenue">
        <i class="fas fa-dollar-sign"></i>
        <div class="stat-info">
          <h3>Total Revenue</h3>
          <p>RM <?= number_format($total_revenue, 2) ?></p>
        </div>
      </div>
    </a>

    <!-- Superadmin-specific: Manage Admins -->
    <a href="manage_admins.php" class="stat-link">
      <div class="stat-card admins">
        <i class="fas fa-user-shield"></i>
        <div class="stat-info">
          <h3>Manage Admins</h3>
          <p>Superadmin Tools</p>
        </div>
      </div>
    </a>

  </div>

  <div class="section">
    <h3>📉 Low Stock Alerts</h3>
    <ul class="info-list">
      <?php while ($b = $low_stock->fetch_assoc()): ?>
        <li><?= htmlspecialchars($b['title']) ?> (<?= $b['stock'] ?> left)</li>
      <?php endwhile; ?>
    </ul>
  </div>

  <div class="section">
    <h3>📦 Recent Orders</h3>
    <table class="recent-table">
      <tr><th>User</th><th>Book</th><th>Total (RM)</th><th>Status</th><th>Date</th></tr>
      <?php while ($row = $recent_orders->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['username']) ?></td>
          <td><?= htmlspecialchars($row['book_title']) ?> ×<?= $row['quantity'] ?></td>
          <td><?= number_format($row['total_price'], 2) ?></td>
          <td><?= htmlspecialchars($row['status']) ?></td>
          <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>

  <div class="section">
    <h3>📊 Monthly Revenue Chart</h3>
    <canvas id="revenueChart" width="600" height="300"></canvas>
    <script>
      const revenueData = <?= json_encode($monthly_data); ?>;
      const ctx = document.getElementById('revenueChart').getContext('2d');
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
          datasets: [{
            label: 'Monthly Revenue (RM)',
            data: revenueData,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: '#3498db',
            borderWidth: 2,
            tension: 0.3,
            fill: true,
            pointRadius: 4,
            pointHoverRadius: 6
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    </script>
  </div>
</div>

</body>
</html>
