<?php
include '../config.php';

if (isset($_GET['id'])) {
    $book_id = $_GET['id'];
    $sql = "SELECT * FROM books WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $book = $stmt->get_result()->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $rating = $_POST['rating'];
    echo "Rating value: " . $rating; // Add this temporarily to check the value

    
    if (!empty($_FILES['image']['name'])) {
        // Delete old image
        $old_image_path = "../" . $book['image_url'];
        if (file_exists($old_image_path)) {
            unlink($old_image_path);
        }

        // Upload new image
        $target_dir = "../Images/";
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = 'Images/' . $new_filename;
            
            $sql = "UPDATE books SET 
                    title = ?, 
                    author = ?, 
                    description = ?, 
                    price = ?, 
                    stock = ?,
                    rating = ?,
                    image_url = ?
                    WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssdiiis", $title, $author, $description, $price, $stock, $rating, $image_url, $book_id);
        }
    } else {
        $sql = "UPDATE books SET 
                title = ?, 
                author = ?, 
                description = ?, 
                price = ?, 
                stock = ?,
                rating = ?
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdiii", $title, $author, $description, $price, $stock, $rating, $book_id);
    }
    
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
    <title>Edit Book - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="edit-container">
        <h2>Edit Book Details</h2>
        <form action="" method="POST" enctype="multipart/form-data" class="edit-form">
            <div class="form-group">
                <label>Current Image:</label>
                <img src="../<?php echo $book['image_url']; ?>" alt="Current book image" class="current-image">
            </div>
            
            <div class="form-group">
                <label>New Image (optional):</label>
                <input type="file" name="image" accept="image/*">
            </div>
            
            <div class="form-group">
                <label>Title:</label>
                <input type="text" name="title" value="<?php echo $book['title']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Author:</label>
                <input type="text" name="author" value="<?php echo $book['author']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Description:</label>
                <textarea name="description" rows="5" required><?php echo $book['description']; ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Price (RM):</label>
                <input type="number" name="price" step="0.01" value="<?php echo $book['price']; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Stock:</label>
                <input type="number" name="stock" min="0" value="<?php echo $book['stock']; ?>" required>
            </div>

            <div class="form-group">
                <label>Rating (1-5 stars):</label>
                <input type="number" name="rating" min="1" max="5" step="0.1" value="<?php echo $book['rating']; ?>" required>
            </div>
            
            <div class="button-group">
                <button type="submit" class="save-btn">Save Changes</button>
                <a href="view_books.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
