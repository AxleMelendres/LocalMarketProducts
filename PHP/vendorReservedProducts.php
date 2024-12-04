<?php
// Start session and include necessary files
require_once '../PHP/dbConnection.php';
require_once '../PHP/vendorConnection.php';
require_once '../DB/productsTB.php';

$database = new Database();
$conn = $database->getConnection();

session_start();

// Check if product_id is set in session or passed as GET parameter
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;

if ($product_id) {
    // If product_id is found, fetch reserved products for that product
    $reservation = new Reservation($conn);
    $reservedProducts = $reservation->getReservedProductsByProduct($product_id);
} else {
    // Handle the case where product_id is not available
    echo "Product ID is missing or invalid.";
}

$conn = null; // Close the connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserved Products</title>
    <link rel="stylesheet" href="../CSS/vendorsprofile.css">
    <script src="../JS/vendorsprofile.js" defer></script>
</head>
<body>
<?php require "../ConnectedVendor/HEADER/profileheader.html"; ?>

<div class="container">
    <div class="reserved-products">
        <h2>Reserved Products</h2>

        <?php if (isset($reservedProducts) && !empty($reservedProducts)): ?>
            <table class="reserved-products-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Buyer</th>
                        <th>Quantity</th>
                        <th>Date Reserved</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservedProducts as $reservation): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reservation['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['buyer_name']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['reserved_quantity']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['reserved_date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No products have been reserved for this product.</p>
        <?php endif; ?>

    </div>
</div>
</body>
</html>
