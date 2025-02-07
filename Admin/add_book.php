<?php
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $rating = $_POST['rating'];
    
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../Images/";
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = 'Images/' . $new_filename;
        }
    }
    
    $sql = "INSERT INTO books (title, author, description, price, stock, image_url, rating) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdisi", $title, $author, $description, $price, $stock, $image_url, $rating);
    
    if ($stmt->execute()) {
        header("Location: view_books.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Book - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <?php include 'admin_header.php'; ?>

    <div class="edit-container">
        <h2>Add New Book</h2>
        <form action="" method="POST" enctype="multipart/form-data" class="edit-form">
            <div class="form-group">
                <label>Book Image:</label>
                <input type="file" name="image" accept="image/*" required>
            </div>
            
            <div class="form-group">
                <label>Title:</label>
                <input type="text" name="title" required>
            </div>
            
            <div class="form-group">
                <label>Author:</label>
                <input type="text" name="author" required>
            </div>
            
            <div class="form-group">
                <label>Description:</label>
                <textarea name="description" rows="5" required></textarea>
            </div>
            
            <div class="form-group">
                <label>Price (RM):</label>
                <input type="number" name="price" step="0.01" required>
            </div>
            
            <div class="form-group">
                <label>Stock:</label>
                <input type="number" name="stock" min="0" required>
            </div>

            <div class="form-group">
                <label>Rating (1-5 stars):</label>
                <input type="number" name="rating" min="1" max="5" step="0.1" required>
            </div>
            
            <div class="button-group">
                <button type="submit" class="save-btn">Add Book</button>
                <a href="view_books.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
