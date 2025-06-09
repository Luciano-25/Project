<?php
// customer_list.php
include '../config.php';
include 'admin_header.php';

// Get search query from GET
$search = $_GET['search'] ?? '';

// Prepare query with search filter if applicable
if ($search) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_type != 'admin' AND (username LIKE ? OR email LIKE ?) ORDER BY created_at DESC");
    $like_search = "%$search%";
    $stmt->bind_param("ss", $like_search, $like_search);
    $stmt->execute();
    $customers = $stmt->get_result();
} else {
    $customers = $conn->query("SELECT * FROM users WHERE user_type != 'admin' ORDER BY created_at DESC");
}
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
        .search-form {
            margin-bottom: 20px;
        }
        .search-input {
            padding: 8px 10px;
            width: 300px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        .search-button {
            padding: 8px 16px;
            border: none;
            background-color: #3498db;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin-left: 8px;
        }
        .search-button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>All Customers</h2>

    <form method="get" class="search-form">
        <input type="text" name="search" class="search-input" placeholder="Search by Username or Email" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="search-button">Search</button>
    </form>

    <table class="styled-table">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($customers->num_rows > 0): ?>
                <?php while ($customer = $customers->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($customer['username']) ?></td>
                        <td><?= htmlspecialchars($customer['email']) ?></td>
                        <td><?= date('d M Y', strtotime($customer['created_at'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align:center;">No customers found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
