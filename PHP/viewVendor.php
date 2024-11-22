<?php
// Include database connection
require_once '../PHP/dbConnection.php';
require_once '../PHP/product.php';

// Connect to the database
$database = new Database();
$conn = $database->getConnection();

// Assuming the vendor's profile is being viewed by the customer
// Retrieve vendor_id from GET or another method if necessary
$vendor_id = isset($_GET['vendor_id']) ? $_GET['vendor_id'] : 0; // Change this logic to your actual method of getting vendor_id

// Prepare the query to fetch products based on the selected vendor_id
$productQuery = "SELECT * FROM products WHERE vendor_id = :vendor_id";

$stmt = $conn->prepare($productQuery);

// Bind the vendor_id parameter
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
    <script src="main.js" defer></script>
</head>
<body>

    <?php require "../HEADER/header.html" ?>
    
    <div class="products-offered">
        <h3>Products</h3>
        <ul class="product-list" id="product-list">
            <?php
            if ($products) {
                foreach ($products as $product) {
                    echo "<li class='product-item'>";
                    // Ensure the image path is relative to the public directory
                    echo "<img src='../" . $product['product_image'] . "' alt='" . $product['product_name'] . "'>";
                    echo "<h4>" . $product['product_name'] . "</h4>";
                    echo "<p><strong>Category:</strong> " . $product['product_category'] . "</p>"; // Display the product category
                    echo "<p><strong>Price:</strong> ₱" . $product['product_price'] . "</p>";
                    echo "<p><strong>Quantity:</strong> " . $product['product_quantity'] . "</p>";
                    echo "<p>" . $product['product_description'] . "</p>";
                    echo "</li>";
                }
            } else {
                echo "<li>No products available for this vendor.</li>";
            }
            ?>
        </ul>
    </div>

</body>
</html>
