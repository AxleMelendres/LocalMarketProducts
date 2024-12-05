<?php
session_start();
require_once '../PHP/dbConnection.php';
require_once '../DB/productsTB.php';

$database = new Database();
$conn = $database->getConnection();

// Retrieve products to display on the page
$product = new Product($conn);
$products = $product->getProductsByVendor($_SESSION['vendor_id']);  // Assuming vendor_id is set in the session

// Handle product deletion via AJAX (POST request)
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

        // Return a JSON response based on the result
        if ($successCount > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Selected products deleted successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'An error occurred while deleting the products.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No products selected to delete.']);
    }
    exit(); // End script after responding to AJAX request
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
    <form id="delete-product-form">
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
            <button type="button" id="remove-product-btn" class="btn" style="display: inline-block;">Remove Selected Products</button>
        </div>
    </form>

    <a href="../PHP/vendorsprofile.php">
        <button id="back-button" class="btn">Back</button>
    </a>
    <!-- Back Button -->

    <script>
        document.getElementById('remove-product-btn').addEventListener('click', function() {
            var formData = new FormData(document.getElementById('delete-product-form'));

            // Make AJAX request to delete selected products
            fetch('delete_product.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                    }).then(() => {
                        // Optionally, refresh the page or redirect
                        location.reload();  // Reload the page
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message,
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Something went wrong, please try again.',
                });
            });
        });
    </script>

</body>
</html>
