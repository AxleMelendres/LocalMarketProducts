<?php

require_once '../PHP/dbConnection.php';
require_once '../PHP/vendorConnection.php'; // Assuming this contains the Vendor class

$database = new Database();
$conn = $database->getConnection();

session_start();
$vendor_uname = $_SESSION['username'];

$vendor = new Vendor($conn);
$vendorDetails = $vendor->getVendor($vendor_uname); 

// Get vendor_id from vendor details
$vendor_id = $vendorDetails['vendor_id'];

$categoryFilter = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : '';

$productQuery = "SELECT * FROM products";
if ($categoryFilter) {
    $productQuery .= " WHERE product_category = :category";
}

$stmt = $conn->prepare($productQuery);

if ($categoryFilter) {
    $stmt->bindParam(':category', $categoryFilter);
}

$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$conn = null; // Close the connection
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
    <script src="main.js" defer></script>
</head>
<body>

    <?php require "../ConnectedVendor/HEADER/header.html"; ?>
    <div class="container">
        <div class="profile">
            <img src="<?php echo $vendorDetails['vendor_image'] ? htmlspecialchars($vendorDetails['vendor_image']) : 'https://via.placeholder.com/150'; ?>" alt="Vendor Profile Picture">
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($vendorDetails['vendor_name']); ?></h2>
                <p><?php echo $vendorDetails['vendor_description'] ? htmlspecialchars($vendorDetails['vendor_description']) : 'We offer a variety of top quality products.'; ?></p>
            </div>
        </div>

        <div class="contact-info">
            <h3>Contact Information</h3>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($vendorDetails['vendor_email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($vendorDetails['vendor_contact']); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($vendorDetails['vendor_address']); ?></p>
            <p><strong>District:</strong> <?php echo htmlspecialchars($vendorDetails['vendor_district']); ?></p>
        </div>

        <div class="products-offered">
            <h3>Products</h3>
            <ul class="product-list" id="product-list">
                <?php
                if ($products) {
                    foreach ($products as $product) {
                        echo "<li class='product-item'>";
                        echo "<img src='../" . htmlspecialchars($product['product_image']) . "' alt='" . htmlspecialchars($product['product_name']) . "'>";
                        echo "<h4>" . htmlspecialchars($product['product_name']) . "</h4>";
                        echo "<p><strong>Category:</strong> " . htmlspecialchars($product['product_category']) . "</p>";
                        echo "<p><strong>Price:</strong> ₱" . number_format($product['product_price'], 2) . "</p>";
                        echo "<p><strong>Quantity:</strong> " . htmlspecialchars($product['product_quantity']) . "</p>";
                        echo "<p>" . nl2br(htmlspecialchars($product['product_description'])) . "</p>";
                        echo "</li>";
                    }
                } else {
                    echo "<li>No products available.</li>";
                }
                ?>
            </ul>
        </div>

        <div class="actions">
            <button class="btn" id="add-products">Add Products</button>
            <button class="btn" id="edit-products">Edit Products</button>
            <button class="btn" id="delete-products">Delete Products</button>
            <button class="btn" id="edit-profile">Edit Profile</button>
        </div>
    </div>
</body>
</html>
