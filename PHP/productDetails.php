<?php
require_once '../PHP/dbConnection.php';
require_once '../DB/productsTB.php';

$database = new Database();
$conn = $database->getConnection();

session_start();

$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null; // Get product ID from URL

// Retrieve product details
$product = new Product($conn);
$productDetails = $product->getProductDetails($product_id); // Only passing product_id

$conn = null; // Close the connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($productDetails['product_name']); ?> - Product Details</title>
    <link rel="stylesheet" href="../CSS/productDetails.css"> <!-- Add your CSS file -->
    <script src="../JS/product.js" defer></script>
</head>
<body>
    <div class="container">
        <div class="product-detail">
            <img src="../<?php echo htmlspecialchars($productDetails['product_image']); ?>" alt="<?php echo htmlspecialchars($productDetails['product_name']); ?>" />
            <div class="product-info">
                <h2><?php echo htmlspecialchars($productDetails['product_name']); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($productDetails['product_description'])); ?></p>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($productDetails['product_category']); ?></p>
                <p><strong>Price:</strong> ₱<?php echo number_format($productDetails['product_price'], 2); ?></p>
                <p><strong>Available Quantity:</strong> <?php echo htmlspecialchars($productDetails['product_quantity']); ?></p>
                
                <!-- Reservation Form -->
                <form method="POST" action="../ConnectedBuyer/reserve_product.php" class="reserve-form" id="reserveForm">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($productDetails['product_id'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($productDetails['product_name'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="product_price" value="<?php echo htmlspecialchars($productDetails['product_price'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="buyer_id" value="<?php echo htmlspecialchars($buyer_id, ENT_QUOTES, 'UTF-8'); ?>">
                    
                    <label for="quantity">Reserve Quantity:</label>
                    <input type="number" name="quantity" id="quantity" min="1" max="<?php echo htmlspecialchars($productDetails['product_quantity'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    
                    <button type="submit" class="reserve-button" id="reserveButton">Reserve</button>
                </form>
            </div>
        </div>
    </div>
    <button id="back-button" class="btn">Back</button>
</body>
</html>
