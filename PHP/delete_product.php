<?php
session_start();
require_once '../PHP/dbConnection.php';
require_once '../DB/productsTB.php';

$database = new Database();
$conn = $database->getConnection();

// Retrieve products to display on the page
$product = new Product($conn);
$products = $product->getProductsByVendor($_SESSION['vendor_id']);  // Assuming vendor_id is set in the session

// Handle product deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_ids']) && !empty($_POST['product_ids'])) {
        $product_ids = $_POST['product_ids'];

        $product = new Product($conn);
        $successCount = 0;

        // Loop through the selected product IDs and delete them
        foreach ($product_ids as $product_id) {
            if ($product->delete($product_id)) {
                $successCount++;
            }
        }

        // Redirect to the same page with a success or error message
        if ($successCount > 0) {
            if ($successCount > 0) {
                header('Location: delete_product.php?status=success');
                exit();
            } else {
                header('Location: delete_product.php?status=error');
                exit();
            }
        }
    } else {
        header('Location: delete_product.php?status=no_products');
        exit();
    }
}

$conn = null;  // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/delete_product.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Remove Products</title>
</head>
<body>


    <!-- Container for h1 heading -->
    <div class="heading-container">
        <h1>Delete Products</h1> 
    </div>

    <!-- Form to handle deletion -->
    <form action="delete_product.php" method="POST">
        <div class="remove-product">
            <!-- Display products with checkboxes to select for deletion -->
            <ul id="product-list">
                <?php
                if ($products) {
                    foreach ($products as $product) {
                        echo "<li class='product-item'>";
                        echo "<input type='checkbox' name='product_ids[]' value='" . htmlspecialchars($product['product_id']) . "'>";
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

            <!-- Submit button to remove selected products -->
            <button type="submit" id="remove-product-btn" class="btn" style="display: inline-block;">Remove Selected Products</button>
        </div>
    </form>
    <a href="../PHP/vendorsprofile.php">
        <button id="back-button" class="btn">Back</button>
    </a>
    <!-- Back Button -->

    <script>
        // Check if the 'status' parameter exists in the URL
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');

        if (status) {
            if (status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Selected products deleted successfully!',
                });
            } else if (status === 'error') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while deleting the products.',
                });
            } else if (status === 'no_products') {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Products Selected',
                    text: 'Please select products to delete.',
                });
            }
        }
    </script>

</body>

</html>
