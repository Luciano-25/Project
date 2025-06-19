<?php include 'header.php'; ?>
<link rel="stylesheet" href="styles.css">

<style>
    .faq-container {
        max-width: 800px;
        margin: 40px auto;
        padding: 30px;
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .faq-title {
        font-size: 2rem;
        margin-bottom: 20px;
        color: #2c3e50;
        text-align: center;
        border-bottom: 2px solid #3498db;
        padding-bottom: 10px;
    }

    .faq-question {
        font-weight: bold;
        margin-top: 20px;
        color: #2c3e50;
    }

    .faq-answer {
        margin-top: 5px;
        color: #444;
        line-height: 1.6;
    }

    .back-link {
        display: inline-block;
        margin-bottom: 20px;
        color: #3498db;
        text-decoration: none;
        font-weight: bold;
        transition: color 0.2s ease-in-out;
    }

    .back-link:hover {
        color: #21618c;
    }
</style>

<div class="faq-container">
    <a href="index.php" class="back-link">&larr; Back to Home</a>
    <h1 class="faq-title">Frequently Asked Questions</h1>

    <div class="faq-item">
        <p class="faq-question">1. How can I place an order?</p>
        <p class="faq-answer">Browse our books, add them to the cart, and complete the checkout process.</p>
    </div>

    <div class="faq-item">
        <p class="faq-question">2. What payment methods are accepted?</p>
        <p class="faq-answer">We accept major credit/debit cards and online banking.</p>
    </div>

    <div class="faq-item">
        <p class="faq-question">3. How long is the delivery time?</p>
        <p class="faq-answer">Usually 4-6 business days within Peninsular Malaysia, and 5–7 for East Malaysia.</p>
    </div>

    <div class="faq-item">
        <p class="faq-question">4. Can I return a product?</p>
        <p class="faq-answer">Yes, please refer to our <a href="returns.php">Returns Policy</a> for details.</p>
    </div>

    <div class="faq-item">
        <p class="faq-question">5. Do I need an account to purchase?</p>
        <p class="faq-answer">Yes.</p>
    </div>

    <div class="faq-item">
        <p class="faq-question">6. Can I cancel my order?</p>
        <p class="faq-answer">Yes, but only before it's shipped. Contact us quickly to cancel.</p>
    </div>

    <div class="faq-item">
        <p class="faq-question">7. What if the book is damaged?</p>
        <p class="faq-answer">Contact us with a photo within 3 days for assistance or replacement.</p>
    </div>
</div>

<?php include 'footer.php'; ?>
