<?php
session_start(); // Ensure the session is started

// Check if buyer_id is available in the session
if (!isset($_SESSION['buyer_id'])) {
    echo "<p>Please log in to view your reserved products.</p>";
    exit;
}

require_once "../PHP/dbConnection.php";

// Fetch reserved products for the logged-in buyer
$buyer_id = $_SESSION['buyer_id'];  // Get buyer_id from session
$database = new Database();
$conn = $database->getConnection();

$query = "
    SELECT r.reserved_quantity, r.reserved_date, b.buyer_name, 
           p.product_name, p.product_price, p.product_image
    FROM reservations r
    JOIN buyer b ON r.buyer_id = b.buyer_id
    JOIN products p ON r.product_id = p.product_id
    WHERE r.buyer_id = :buyer_id
";

$stmt = $conn->prepare($query);
$stmt->bindParam(':buyer_id', $buyer_id, PDO::PARAM_INT);  // Bind the buyer_id parameter
$stmt->execute();

$reservedProducts = [];
if ($stmt->rowCount() > 0) {
    $reservedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserved Products</title>
    <link rel="stylesheet" href="../CSS/reservedProduct.css">
</head>
<body>
    <?php require "../ConnectedBuyer/HEADER/header.html"; ?>

    <div class="container">
        <h1>Reserved Products</h1>
        <div class="product-grid">
            <?php if (empty($reservedProducts)): ?>
                <p>No reserved products found.</p>
            <?php else: ?>
                <?php foreach ($reservedProducts as $product): ?>
                    <?php
                    // Calculate the total price
                    $total_price = $product['product_price'] * $product['reserved_quantity'];
                    ?>
                    <div class="product-card">
                        <img src="<?= htmlspecialchars($product['product_image']); ?>" 
                             alt="<?= htmlspecialchars($product['product_name']); ?>">
                        <h2><?= htmlspecialchars($product['product_name']); ?></h2>
                        <p class="">Buyer: <?= htmlspecialchars($product['buyer_name']); ?></p>
                        <p class="price">Total Price: ₱<?= number_format($total_price, 2); ?></p>
                        <p class="">Quantity: <?= htmlspecialchars($product['reserved_quantity']); ?></p>
                        <p class="">Reserved Date: <?= htmlspecialchars($product['reserved_date']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

