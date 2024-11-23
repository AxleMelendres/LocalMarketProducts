<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../CSS/view.css">
</head>
<body>
<?php   
        require "../ConnectedVendor/HEADER/header.html";
        require_once "../PHP/dbConnection.php"; 
        
        // Initialize database connection
        $database = new Database();
        $conn = $database->getConnection();

        // Initialize Product class and fetch product details
        require_once "../DB/productsTB.php";    
        $product = new Product($conn);
        $productDetails = $product->view();
    ?>

<div class="container">
    <div class="product-image">
        <img src="<?php echo htmlspecialchars($productDetails['product_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Product Image">
    </div>

    <!-- Product Info Section -->
    <div class="product-info">
        <h2><?php echo htmlspecialchars($productDetails['product_name'], ENT_QUOTES, 'UTF-8'); ?></h2>
        <h4><p class="category">Category: <?php echo htmlspecialchars($productDetails['product_category'], ENT_QUOTES, 'UTF-8'); ?></p></h4>
        <p class="product-description"><?php echo htmlspecialchars($productDetails['product_description'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p class="price">$<?php echo htmlspecialchars(number_format($productDetails['product_price'], 2), ENT_QUOTES, 'UTF-8'); ?></p>
        <p class="quantity">Available Quantity: <?php echo htmlspecialchars($productDetails['product_quantity'], ENT_QUOTES, 'UTF-8'); ?></p>

        <!-- Vendor Information -->
        <div class="vendor-info">
            <div class="vendor-image">
                <img src="<?php echo !empty($productDetails['vendor_image']) 
                                ? htmlspecialchars($productDetails['vendor_image'], ENT_QUOTES, 'UTF-8') 
                                : '../uploads/default_vendor.png'; ?>" 
                    alt="Vendor Image">
            </div>
            <div class="vendor-details">
                <div class="vendor-username">
                    <p><?php echo htmlspecialchars($productDetails['vendor_username'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                    <p><?php echo htmlspecialchars($productDetails['vendor_description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>

        <!-- Buttons -->
        <button class="reserve-button" onclick="reserveProduct()">Reserve</button>
        <br>
        <a href="javascript:history.back()" class="back-button"><span>&lt;</span> Go Back</a>
    </div>
</div>

<script>
    function reserveProduct() {
        alert("You have reserved this product!");
    }
</script>

<?php require "../HEADER/footer.html"; ?>
</body>
</html>
