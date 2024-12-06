<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & Support - Local Market</title>
    <link rel="stylesheet" href="../CSS/helpSupport.css">
    <script src="https://kit.fontawesome.com/89e47c0436.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php  require "../HEADER/customerHeader.html" ?>
    <main class="support-container">
        <h2>Help & Support</h2>

        <section class="faq-section">
            <h3>Frequently Asked Questions</h3>
            <div class="faq">
                <h4>1. How do I purchase local products?</h4>
                <p>To purchase products, browse through our catalog, add items to your cart, and proceed to checkout. We support multiple payment methods for your convenience.</p>
            </div>
            <div class="faq">
                <h4>2. What payment methods are accepted?</h4>
                <p>We accept major credit/debit cards, online wallets, and cash on delivery (COD) for certain locations.</p>
            </div>
            <div class="faq">
                <h4>3. Can I return or exchange a product?</h4>
                <p>Yes, you can return or exchange products within 7 days of delivery. Please ensure the product is in its original condition and packaging.</p>
            </div>
        </section>

        <section class="contact-section">
            <h3>Contact Us</h3>
            <p>If you need further assistance, feel free to reach out:</p>
            <ul>
                <li><i class="fas fa-envelope"></i> Email: support@localmarket.com</li>
                <li><i class="fas fa-phone"></i> Phone: +123-456-7890</li>
                <li><i class="fas fa-map-marker-alt"></i> Address: 123 Local Market Street, Hometown</li>
            </ul>
        </section>

        <section class="feedback-section">
            <h3>Feedback</h3>
            <p>We value your feedback to improve our service. Let us know how we’re doing:</p>
            <form method="POST" action="#">
                <textarea name="feedback" rows="4" placeholder="Write your feedback here..." required></textarea>
                <button type="submit" class="submit-button">Submit Feedback</button>
            </form>
        </section>

        
        <div class="back-button-container">
            <a href="../PHP/customerProfile.php" class="back-button">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </main>
</body>
</html>
