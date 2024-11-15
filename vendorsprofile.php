<?php
// Include database connection
require_once 'dbConnection.php';
require_once 'product.php';

// Connect to the database
$database = new Database();
$conn = $database->getConnection();

// Get all products from the products table
$productQuery = "SELECT * FROM products";
$stmt = $conn->prepare($productQuery);
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
    <link rel="stylesheet" href="vendorsprofile.css">
    <script src="vendorsprofile.js" defer></script>
</head>
<body>
    <header>
        <h1>Vendor Profile</h1>
    </header>

    <div class="container">
        <div class="profile">
            <img src="https://via.placeholder.com/150" alt="Vendor Profile Picture">
            <div class="profile-info">
                <h2>John Doe</h2>
                <p>We offer a variety of top quality products.</p>
            </div>
        </div>

        <div class="contact-info">
            <h3>Contact Information</h3>
            <p><strong>Email:</strong> john.doe@example.com</p>
            <p><strong>Phone:</strong> +123456789</p>
            <p><strong>Location:</strong> 123 Home St City</p>
            <p><strong>District:</strong> District 1</p>
        </div>

        <div class="products-offered">
            <h3>Products</h3>
            <ul class="product-list" id="product-list">
                <?php

                foreach ($products as $product) {
                    echo "<li class='product-item'>";
                    echo "<img src='" . $product['product_image'] . "' alt='" . $product['product_name'] . "'>";
                    echo "<h4>" . $product['product_name'] . "</h4>";
                    echo "<p><strong>Price:</strong> ₱" . $product['product_price'] . "</p>";
                    echo "<p><strong>Quantity:</strong> " . $product['product_quantity'] . " items</p>";
                    echo "<p>" . $product['product_description'] . "</p>";
                    echo "</li>";
                }
                ?>
            </ul>
        </div>

        <div class="actions">
            <button class="btn" id="add-products">Add Products</button>
            <button class="btn" id="edit-products">Edit Products</button>
            <button class="btn" id="delete-products">Delete Products</button>
        </div>
    </div>
</body>
</html>
