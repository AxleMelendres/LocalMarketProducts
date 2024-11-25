<?php
// Include database connection
require_once '../PHP/dbConnection.php';
require_once '../PHP/product.php';

// Connect to the database
$database = new Database();
$conn = $database->getConnection();

// Get vendor_id from query parameter
$vendor_id = isset($_GET['vendor_id']) ? $_GET['vendor_id'] : 0; // Handle missing vendor_id gracefully

// Prepare the query to fetch products based on the selected vendor_id
$productQuery = "SELECT * FROM products WHERE vendor_id = :vendor_id";
$stmt = $conn->prepare($productQuery);
$stmt->bindParam(':vendor_id', $vendor_id);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Profile</title>
    <link rel="stylesheet" href="../CSS/vendorsprofile.css">
    <script src="../JS/vendorsprofile.js" defer></script>
    <script src="https://kit.fontawesome.com/89e47c0436.js" crossorigin="anonymous"></script>
</head>
<body>

    <?php require "../HEADER/vendorHeader.html"; ?>

    <div class="vendor-profile">
        <!-- Vendor details section -->
        <div class="vendor-info">
            <div class="vendor-image">
                <img src="<?php echo !empty($vendorDetails['vendor_image']) ? $vendorDetails['vendor_image'] : '../uploads/default_vendor.png'; ?>" alt="Vendor Image">
            </div>
            <div class="vendor-details">
                <h2><?php echo htmlspecialchars($vendorDetails['vendor_username'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><?php echo htmlspecialchars($vendorDetails['vendor_description'] ?? 'No description available', ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        </div>

        <!-- Products offered by the vendor -->
        <div class="products-offered">
            <h3>Products</h3>
            <ul class="product-list" id="product-list">
                <?php
                if ($products) {
                    foreach ($products as $product) {
                        echo "<li class='product-item'>";
                        echo "<img src='../" . $product['product_image'] . "' alt='" . $product['product_name'] . "'>";
                        echo "<h4>" . htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8') . "</h4>";
                        echo "<p><strong>Category:</strong> " . htmlspecialchars($product['product_category'], ENT_QUOTES, 'UTF-8') . "</p>";
                        echo "<p><strong>Price:</strong> ₱" . htmlspecialchars($product['product_price'], ENT_QUOTES, 'UTF-8') . "</p>";
                        echo "<p><strong>Quantity:</strong> " . htmlspecialchars($product['product_quantity'], ENT_QUOTES, 'UTF-8') . "</p>";
                        echo "<p>" . htmlspecialchars($product['product_description'], ENT_QUOTES, 'UTF-8') . "</p>";
                        echo "</li>";
                    }
                } else {
                    echo "<li>No products available for this vendor.</li>";
                }
                ?>
            </ul>
        </div>
    </div>

</body>
</html>
