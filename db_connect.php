<?php
$host = "localhost"; 
$user = "root";  // Default MySQL user in XAMPP
$pass = "";  // Default password (leave blank if using XAMPP)
$dbname = "online_bookstore";  // Your database name

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
