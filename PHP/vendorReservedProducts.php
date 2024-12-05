<?php
// Start session and include necessary files
require_once '../PHP/dbConnection.php';
require_once '../PHP/vendorConnection.php';
require_once '../DB/productsTB.php';

$database = new Database();
$conn = $database->getConnection();

session_start();

// Get vendor_id from the session (assuming vendor logs in and their ID is stored in session)
$vendor_id = isset($_SESSION['vendor_id']) ? $_SESSION['vendor_id'] : null;

if ($vendor_id) {
    // Fetch reserved products for the logged-in vendor
    $reservation = new Reservation($conn);
    $reservedProducts = $reservation->getReservedProductsByVendor($vendor_id);
} else {
    // Handle the case where vendor_id is not available
    echo "Vendor ID is missing or invalid.";
    $reservedProducts = [];
}

$conn = null; // Close the connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserved Products</title>
    <link rel="stylesheet" href="../CSS/vendorReservedProducts.css">
    <script src="../JS/product.js" defer></script>
</head>
<body>
<h2>Reserved Products</h2>
<div class="container">
    <div class="reserved-products">
        

        <?php if (isset($reservedProducts) && !empty($reservedProducts)): ?>
            <table class="reserved-products-table">
                <thead>
                    <tr>
                        <th>Product Image</th>
                        <th>Product</th>
                        <th>Buyer Image</th>
                        <th>Buyer</th>
                        <th>Quantity</th>
                        <th>Date Reserved</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservedProducts as $reservation): ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($reservation['product_image']); ?>" alt="Product Image" style="width: 100px; height: auto;">
                            </td>
                            <td><?php echo htmlspecialchars($reservation['product_name']); ?></td>
                            <td>
                                <img src="<?php echo htmlspecialchars($reservation['buyer_image']); ?>" alt="Buyer Image" style="width: 50px; height: auto; border-radius: 50%;">
                            </td>
                            <td><?php echo htmlspecialchars($reservation['buyer_name']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['reserved_quantity']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['reserved_date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No products have been reserved for your listings.</p>
        <?php endif; ?>

    </div>
</div>
            
</div>
    <button id="back-button" class="btn">Back</button>
</body>
</html>
