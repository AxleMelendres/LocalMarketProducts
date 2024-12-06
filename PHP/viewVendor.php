<?php
require_once '../PHP/dbConnection.php';
require_once '../PHP/vendorConnection.php'; 
require_once '../DB/productsTB.php';
require_once '../DB/accountTB.php';

$database = new Database();
$conn = $database->getConnection();

session_start();

// Get the logged-in user's role (e.g., 'Seller' or 'Buyer')
$user_role = isset($_SESSION['purpose']) ? $_SESSION['purpose'] : '';

// Get vendor username from URL
$vendor_uname = isset($_GET['username']) ? $_GET['username'] : null;

// Retrieve vendor details
$vendor = new Vendor($conn);
$vendorDetails = $vendor->getVendor($vendor_uname);

// Fetch products offered by the vendor
$product = new Product($conn);
$products = $product->getProductsByVendor($vendorDetails['vendor_id']);

$conn = null; // Close the connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($vendorDetails['vendor_name']) ?> - Vendor Profile</title>
    <link rel="stylesheet" href="../CSS/vendorsprofile.css">
</head>
<body>
    <?php
    // Determine the full script path
    $fullPath = $_SERVER['SCRIPT_NAME'];

        if ($user_role !== 'Seller') {
            // Load Buyer-specific header
            require realpath("../ConnectedBuyer/HEADER/profileheader.html");
        } else if ($user_role !== 'Buyer') {
            require realpath("../ConnectedVendor/HEADER/profileheader.html");
        } else {
            // Load Buyer-specific header
            require realpath("../ConnectedBuyer/HEADER/profileheader.html");
        }
    ?>
    
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
            <ul class="product-list">
            <?php
            if ($products) {
                foreach ($products as $product) {
                    echo "<li class='product-item'>";
                    echo "<img src='../" . htmlspecialchars($product['product_image']) . "' alt='" . htmlspecialchars($product['product_name']) . "'>";
                    echo "<h4>" . htmlspecialchars($product['product_name']) . "</h4>";
                    echo "<p><strong>Category:</strong> " . htmlspecialchars($product['product_category']) . "</p>";
                    echo "<p><strong>Price:</strong> ₱" . number_format($product['product_price'], 2) . "</p>";
                    echo "<p><strong>Quantity:</strong> " . htmlspecialchars($product['product_quantity']) . "</p>";

                    if ($user_role !== 'Seller') {
                        // Display the "Reserve" button for non-sellers (Buyers)
                        echo "<a href='../PHP/productDetails.php?product_id=" . htmlspecialchars($product['product_id']) . "' class='reserve-btn'>View</a>";
                    } else {
                    }
                    
                    echo "</li>";
                }
            } else {
                echo "<li>No products available.</li>";
            }
            ?>
        </ul>
        </div>
    </div>
</body>
</html>
