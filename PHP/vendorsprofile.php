<?php
// Include database connection
require_once '../PHP/dbConnection.php';
require_once '../PHP/product.php';

// Connect to the database
$database = new Database();
$conn = $database->getConnection();

// Get the selected category from the search form (if any)
$categoryFilter = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : '';

// Prepare the query to fetch products based on selected category (if provided)
$productQuery = "SELECT * FROM products";
if ($categoryFilter) {
    $productQuery .= " WHERE product_category = :category";
}

$stmt = $conn->prepare($productQuery);

// Bind the category parameter if there's a filter
if ($categoryFilter) {
    $stmt->bindParam(':category', $categoryFilter);
}

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

    <?php  require "../HEADER/header.html" ?>
    <div class="sidebar">
        <div class="info-sidebar">
            <a href="#" class="logo">Market Alchemy</a>
            <i class="fa-solid fa-x closeSidebar"></i>
        </div>
        <hr>
        <div class="social-sidebar">
            <a href="#"><i class="fa-brands fa-facebook"></i> Facebook</a>
            <a href="#"><i class="fa-brands fa-instagram"></i> Instagram</a>
            <a href="#"><i class="fa-brands fa-x-twitter"></i> Twitter</a>
            <a href="#"><i class="fa-brands fa-github"></i> Github</a>
        </div>
        <hr>
        <div class="call">
            <h2>Contact</h2>
            <h5>+63 912 345 678 03</h5>
            <h5>Market Alchemy</h5>
        </div>
    </div>

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
                    // Ensure the image path is relative to the public directory
                    echo "<img src='../" . $product['product_image'] . "' alt='" . $product['product_name'] . "'>";
                    echo "<h4>" . $product['product_name'] . "</h4>";
                    echo "<p><strong>Category:</strong> " . $product['product_category'] . "</p>"; // Display the product category
                    echo "<p><strong>Price:</strong> ₱" . $product['product_price'] . "</p>";
                    echo "<p><strong>Quantity:</strong> " . $product['product_quantity'] . "</p>";
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
