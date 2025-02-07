<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Store the message in the database or send email here
    
    // Set success message
    $_SESSION['contact_message'] = "Thank you for contacting us! We'll get back to you soon.";
    
    // Redirect after 3 seconds
    header("refresh:3;url=index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - BookHaven</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="thank-you-container">
        <div class="thank-you-message">
            <i class="fas fa-check-circle"></i>
            <h1>Thank You!</h1>
            <p>Your message has been sent successfully.</p>
            <p>Redirecting to homepage...</p>
        </div>
    </div>
</body>
</html>
