<?php
include '../config.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT 
            users.username,
            orders.book_title,
            orders.quantity,
            orders.created_at,
            orders.shipping_address,
            orders.shipping_city,
            orders.shipping_postal_code
        FROM orders
        JOIN users ON orders.user_id = users.id";

$params = [];
$types = "";
$where = "";

if ($search !== '') {
    $where = " WHERE orders.book_title LIKE ?";
    $params[] = "%" . $search . "%";
    $types .= "s";
}

$query = $sql . $where . " ORDER BY orders.created_at DESC";
$stmt = $conn->prepare($query);

if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Purchase Report - BookHaven</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        h2 {
            margin-bottom: 20px;
            color: #2c3e50;
        }

        form {
            margin-bottom: 25px;
        }

        input[type="text"] {
            padding: 7px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 300px;
        }

        button {
            padding: 7px 15px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #2980b9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #2c3e50;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .container {
            padding: 20px;
        }
    </style>
</head>
<body>
<?php include 'admin_header.php'; ?>
<div class="container">
    <h2>Search Customers by Book</h2>
    <form method="get" action="">
        <input type="text" name="search" placeholder="Enter book title" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Customer</th>
                <th>Book Title</th>
                <th>Quantity</th>
                <th>Purchase Date</th>
                <th>Address</th>
                <th>City</th>
                <th>Postal Code</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td><?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?></td>
                    <td><?php echo htmlspecialchars($row['shipping_address']); ?></td>
                    <td><?php echo htmlspecialchars($row['shipping_city']); ?></td>
                    <td><?php echo htmlspecialchars($row['shipping_postal_code']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No results found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
