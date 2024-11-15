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
    <script src="https://kit.fontawesome.com/89e47c0436.js" crossorigin="anonymous"></script>
    <script src="main.js" defer></script>
</head>
<body>

    <header class="header">
        <a href="#" class="logo">Market Alchemy</a>

        <nav>
            <a class="link" href="mainn.php">Home</a>
            <a class="link" href="about.html">About</a>
        </nav>

        <form class="search-bar" action="search.php" method="GET">
            <input type="text" name="query" placeholder="Search...">
            <button type="submit">Search</button>

            <select name="district" id="district">
            <option value="">Select</option>
                <option value="South District">South District</option>
                <option value="North District">North District</option>
                <option value="West District">West District</option>
                <option value="East District">East District</option>
                <option value="Urban District">Urban District</option>
            </select>

            <select name="category" id="category">
                <option value="">Select Category</option>
                <option value="electronics">Electronics</option>
                <option value="clothing">Clothing</option>
                <option value="books">Books</option>
                <!-- Add more categories as needed -->
            </select>
        </form>

        <div class="icons">
            <a href="login.html" style="color: #3a5a40;"><i class="fa-solid fa-user"></i> </a>
            <div class="sidebarMenu">
                <i class="fa-solid fa-bars"></i>
            </div>
        </div>

    </header>

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
                    echo "<img src='" . $product['product_image'] . "' alt='" . $product['product_name'] . "'>";
                    echo "<h4>" . $product['product_name'] . "</h4>";
                    echo "<p><strong>Price:</strong> ₱" . $product['product_price'] . "</p>";
                    echo "<p><strong>Quantity:</strong> " . $product['product_quantity']  ;
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
