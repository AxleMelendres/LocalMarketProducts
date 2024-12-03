<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../CSS/viewproduct.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 Library -->
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

    // Example buyer_id from session
    session_start();
    $buyer_id = $_SESSION['buyer_id'] ?? 1; // Replace with actual session management logic
?>
<div class="container">

    <div class="product-image">
        <img src="<?php echo htmlspecialchars($productDetails['product_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Product Image">
    </div>

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
                    <a href="../PHP/viewVendor.php?username=<?php echo urlencode($productDetails['vendor_username']); ?>" 
                    title="View vendor profile">
                        <?php echo htmlspecialchars($productDetails['vendor_username'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </div>
                <p><?php echo htmlspecialchars($productDetails['vendor_description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        </div>

        <form method="POST" action="reserve_product.php" class="reserve-form" id="reserveForm">
    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($productDetails['product_id'], ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($productDetails['product_name'], ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="product_price" value="<?php echo htmlspecialchars($productDetails['product_price'], ENT_QUOTES, 'UTF-8'); ?>">
    <label for="quantity">Reserve Quantity:</label>
    <input type="number" name="quantity" id="quantity" min="1" max="<?php echo htmlspecialchars($productDetails['product_quantity'], ENT_QUOTES, 'UTF-8'); ?>" required>
    <button type="submit" class="reserve-button" id="reserveButton">Reserve</button>
</form>


        <br>
        <a href="javascript:history.back()" class="back-button"><span>&lt;</span> Go Back</a>
    </div>
</div>
<?php require "../HEADER/footer.html"; ?>

<script>
    // Attach event listener to the form
    document.getElementById("reserveForm").addEventListener("submit", function(event) {
        event.preventDefault(); // Prevent form submission for the popup

        // SweetAlert Popup
        Swal.fire({
            title: 'Reservation Successful!',
            text: 'Your reservation has been made.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form after the alert
                event.target.submit();
            }
        });
    });
</script>
</body>
</html>
