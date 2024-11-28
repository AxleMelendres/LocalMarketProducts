<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserved Products</title>
    <link rel="stylesheet" href="../CSS/ReservedProduct.css">
</head>
<body>
    <?php require "../ConnectedBuyer/HEADER/header.html"; ?>

    <div class="container">
        <h1>Reserved Products</h1>
        <div class="product-grid">
    <?php
    require_once "../PHP/dbConnection.php";

    // Initialize database connection
    $database = new Database();
    $conn = $database->getConnection();

    // Fetch reserved products
    $query = "SELECT r.reserved_quantity, r.reserved_date, b.buyer_name, 
                     p.product_name, p.product_price, p.product_image
              FROM reservations r
              JOIN buyer b ON r.buyer_id = b.buyer_id
              JOIN products p ON r.product_id = p.product_id";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="product-card">
                <img src="<?= htmlspecialchars($row['product_image']); ?>" alt="<?= htmlspecialchars($row['product_name']); ?>">
                <h2><?= htmlspecialchars($row['product_name']); ?></h2>
                <p>Buyer: <?= htmlspecialchars($row['buyer_name']); ?></p>
                <p class="price">$<?= htmlspecialchars($row['product_price']); ?></p>
                <p>Quantity: <?= htmlspecialchars($row['reserved_quantity']); ?></p>
                <p>Reserved Date: <?= htmlspecialchars($row['reserved_date']); ?></p>
            </div>
        <?php endwhile;
    } else {
        echo "<p>No reserved products found.</p>";
    }
    ?>
</div>

    </div>

</body>
</html>
