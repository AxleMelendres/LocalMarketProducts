<?php
session_start();
require_once '../PHP/dbConnection.php';
require_once '../PHP/product.php';

$database = new Database();
$conn = $database->getConnection();

// Handle product deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if product_id is set via POST
    if (isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];

        // Create an instance of the Product class
        $product = new Product($conn);

        // Delete the product by ID
        if ($product->delete($product_id)) {
            echo 'success';  // Respond with success if deletion was successful
        } else {
            echo 'error';  // Respond with error if deletion failed
        }
    } else {
        echo 'Product ID not provided';  // Handle missing product ID
    }
}



// Retrieve the products to display on the page
$product = new Product($conn);
$products = $product->getProductsByVendor($_SESSION['vendor_id']);  // Make sure to set the vendor_id in the session

$conn = null;  // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/delete_product.css">
    <script src="../JS/product.js" defer></script>
    <title>Remove Product</title>
</head>
<body>

    <div class="remove-product">
        <h2>Remove Product</h2>

        <!-- Display products with click to select -->
        <ul id="product-list">
            <?php
            if ($products) {
                foreach ($products as $product) {
                    echo "<li class='product-item' data-product-id='" . htmlspecialchars($product['product_id']) . "'>";
                    echo "<img src='../" . htmlspecialchars($product['product_image']) . "' alt='" . htmlspecialchars($product['product_name']) . "'>";
                    echo "<h4>" . htmlspecialchars($product['product_name']) . "</h4>";
                    echo "<p><strong>Category:</strong> " . htmlspecialchars($product['product_category']) . "</p>";
                    echo "<p><strong>Price:</strong> ₱" . number_format($product['product_price'], 2) . "</p>";
                    echo "<p><strong>Quantity:</strong> " . htmlspecialchars($product['product_quantity']) . "</p>";
                    echo "</li>";
                }
            } else {
                echo "<li>No products available.</li>";
            }
            ?>
        </ul>

        <!-- Button to delete the selected product -->
        <button type="button" id="remove-product-btn" class="btn">Remove Selected Product</button>
    </div>

    <button id="back-button" class="btn">Back</button>

</body>
</html>

