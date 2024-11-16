<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Market Alchemy</title>
    <link rel="stylesheet" href="../CSS/main.css">
    <script src="https://kit.fontawesome.com/89e47c0436.js" crossorigin="anonymous"></script>
    <script src="../JS/main.js" defer></script>

</head>

<body>
    <header class="header">
        <a href="../PHP/mainn.php" class="logo">Market Alchemy</a>

        <nav>
            <a class="link" href="../PHP/mainn.php">Home</a>
            <a class="link" href="../HTML/about.html">About</a>
        </nav>

        <form class="search-bar" action="../PHP/search.php" method="GET">
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
            <a href="../HTML/loginn.html" style="color: #3a5a40;"><i class="fa-solid fa-user"></i> </a>
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

    <section class="product-section"></section>
    <?php
    // Assuming you have a database of products
    // Example PHP code to fetch products from the database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "dbgroup1"; // Change to your database name

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Adjust SQL query to include 'id' for product links
    $sql = "SELECT id, product_name, product_price, product_image FROM products";
    $result = $conn->query($sql);

    if ($result === false) {
        die("Error in SQL query: " . $conn->error);
    }

    echo "<div class='product-boxes'>";
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='product-box'>";

            // Convert BLOB data to base64 for image display
            $imagePath = "uploads/" . htmlspecialchars($row['product_image']);
            echo "<img src='{$imagePath}' alt='" . htmlspecialchars($row['product_name']) . "' class='product-image'>";
            
            echo "<h3>" . htmlspecialchars($row['product_name']) . "</h3>";
            echo "<p>$" . htmlspecialchars($row['product_price']) . "</p>";
            echo "<a href='view_product.php?id=" . urlencode($row['id']) . "' class='view-button'>View</a>";
            echo "<a href='reserve_product.php?id=" . urlencode($row['id']) . "' class='reserve-button'>Reserve</a>";

            echo "</div>";
        }
    } else {
        echo "<p>No products available.</p>";
    }
    echo "</div>";

    $conn->close();
    ?>
    </section>

    <footer>
        <p>&copy; 2024 BSIT 2102 || Group 1 Final Project</p>
    </footer>
</body>

</html>