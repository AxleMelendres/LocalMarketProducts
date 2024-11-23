
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../CSS/view.css">
</head>
<body>
<?php   
        require "../ConnectedBuyer/HEADER/header.html";
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
            <img src="<?php echo htmlspecialchars($productDetails['product_image']); ?>" alt="Product Image">
        </div>

        <!-- Product Info Section -->
        <div class="product-info">
            <h2><?php echo htmlspecialchars($productDetails['product_name']); ?></h2>
            <h4><p class="category">Category: <?php echo htmlspecialchars($productDetails['product_category']); ?></p></h4>
            <p class="product-description"><?php echo htmlspecialchars($productDetails['product_description']); ?></p>
            <p class="price">$<?php echo htmlspecialchars(number_format($productDetails['product_price'], 2)); ?></p>
            <p class="quantity">Available Quantity: <?php echo htmlspecialchars($productDetails['product_quantity']); ?></p>

            <!-- Buttons -->
            <button class="reserve-button" onclick="reserveProduct()">Reserve</button>
            <br>
            <a href="javascript:history.back()" class="back-button"><span>&lt;</span> Go Back</a>
        </div>
    </div>

    <script>
        function reserveProduct() {
            alert("You have reserved this product!");
            // Additional functionality for reservation can go here.
        }
    </script>

    <?php require "../HEADER/footer.html"; ?>
</body>
</html>
