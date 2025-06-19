<?php include 'header.php'; ?>
<link rel="stylesheet" href="styles.css">

<style>
    .form-container {
        max-width: 800px;
        margin: 40px auto;
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .form-title {
        font-size: 2rem;
        color: #2c3e50;
        margin-bottom: 20px;
        text-align: center;
        border-bottom: 2px solid #3498db;
        padding-bottom: 10px;
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

    .static-section p, .static-section li, .static-section h3, .static-section ol {
        color: #333;
        line-height: 1.6;
    }

    .static-section h3 {
        margin-top: 20px;
        color: #2c3e50;
        border-left: 4px solid #3498db;
        padding-left: 10px;
    }

    .static-section ul, .static-section ol {
        padding-left: 20px;
        margin-top: 10px;
    }

    .static-section ul li::marker {
        color: #3498db;
    }

    .static-section ol li {
        margin-bottom: 10px;
    }
</style>

<div class="form-container">
    <a href="index.php" class="back-link">&larr; Back to Home</a>
    <h1 class="form-title">Returns Policy</h1>

    <div class="static-section">
        <p>We want you to be fully satisfied with your purchase. If for any reason you're not happy, here's our return process:</p>

        <h3>Eligibility for Returns:</h3>
        <ul>
            <li>Items must be returned within 7 days of delivery.</li>
            <li>Books must be unused, in original condition.</li>
            <li>Damaged or wrong items are eligible for free returns or replacements.</li>
        </ul>

        <h3>How to Return:</h3>
        <ol>
            <li>Email us at <strong>support@bookhaven.com</strong> with your order ID and reason for return.</li>
            <li>We’ll respond with return instructions within 24–48 hours.</li>
            <li>Pack the item securely and ship it back to our return address.</li>
        </ol>

        <h3>Refunds:</h3>
        <p>Once we receive and inspect the returned item, refunds will be processed within 5–7 business days to your original payment method.</p>
    </div>
</div>

<?php include 'footer.php'; ?>
