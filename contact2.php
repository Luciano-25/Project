<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookHaven - Contact Us</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<div class="contact-container">
    <div class="contact-header">
        <h1>Contact Us</h1>
        <p>Get in touch with us for any inquiries or support</p>
    </div>

    <div class="contact-content">
        <div class="contact-info">
            <div class="info-item">
                <i class="fas fa-map-marker-alt"></i>
                <div>
                    <h3>Our Location</h3>
                    <p>123 Book Street, Reading Avenue</p>
                    <p>Kuala Lumpur, Malaysia</p>
                </div>
            </div>
            <div class="info-item">
                <i class="fas fa-phone"></i>
                <div>
                    <h3>Phone Number</h3>
                    <p>+60 12-345 6789</p>
                </div>
            </div>
            <div class="info-item">
                <i class="fas fa-envelope"></i>
                <div>
                    <h3>Email Address</h3>
                    <p>support@bookhaven.com</p>
                </div>
            </div>
        </div>

        <form class="contact-form" action="process_contact.php" method="POST">
            <div class="form-group">
                <label for="name">Your Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" required>
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="5" required></textarea>
            </div>
            <button type="submit" class="submit-btn">Send Message</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
