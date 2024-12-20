<?php
session_start();

require_once '../PHP/vendorConnection.php';
require_once '../PHP/dbConnection.php';
require_once '../DB/productsTB.php';

$database = new Database();
$conn = $database->getConnection();

if (!isset($_SESSION['vendor_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Vendor ID is not set in session. Please log in.']);
    exit;
}

$vendor_id = $_SESSION['vendor_id']; 

// Create Product object
$product = new Product($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    // Collect product details from the form
    $productId = $_POST['product_id'];
    $productName = htmlspecialchars($_POST['product-name']);
    $productQuantity = intval($_POST['product-quantity']);
    $productPrice = floatval($_POST['product-price']);
    $productDescription = htmlspecialchars($_POST['product-description']);
    $productCategory = htmlspecialchars($_POST['product-category']); 

    // Check if a new image is uploaded
    $imagePath = $_POST['current-image']; // Default to the existing image if no new image is uploaded
    if (isset($_FILES['product-image']) && $_FILES['product-image']['error'] == 0) {
        $imageTmpName = $_FILES['product-image']['tmp_name'];
        $imageName = $_FILES['product-image']['name'];
        $imageType = $_FILES['product-image']['type'];
        $imageSize = $_FILES['product-image']['size'];

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($imageType, $allowedTypes)) {
            $uploadDir = '../uploads/';
            $imagePath = $uploadDir . basename($imageName); // Update image path if new image is uploaded

            if (!move_uploaded_file($imageTmpName, $imagePath)) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to upload image.']);
                exit;
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid image type. Only JPG, PNG, and GIF are allowed.']);
            exit;
        }
    }

    // Update the product
    $product->product_name = $productName;
    $product->product_image = $imagePath;
    $product->product_quantity = $productQuantity;
    $product->product_price = $productPrice;
    $product->product_description = $productDescription;
    $product->product_category = $productCategory;

    if ($product->update($productId)) {
        echo json_encode(['status' => 'success', 'message' => 'Product updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Could not update product. Please try again.']);
    }
    exit; // Ensure the script stops after sending the response
}

// If a product ID is set in the URL, fetch that product's details
if (isset($_GET['product_id']) && !empty($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Fetch the product details for editing
    $productDetails = $product->getProductDetails($product_id, $vendor_id);

    if (!$productDetails) {
        die("Product not found or you do not have permission to edit this product.");
    }
} else {
    $productDetails = null;
}

// Retrieve all products for the current vendor
$products = $product->getProductsByVendor($vendor_id); // Get all products for the vendor
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Products</title>
    <link rel="stylesheet" href="../CSS/edit_product.css">
    <script src="../JS/product.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="heading-container">
        <h1>Edit Products</h1> 
    </div>
<div class="container">
    <h2>Manage Your Products</h2>

    <!-- Check if a product is being edited (i.e., `product_id` is set in the URL) -->
    <?php if (isset($product_id) && !empty($product_id)): ?>
        <!-- If Editing a Product -->
        <h2>Edit Product: <?php echo htmlspecialchars($productDetails['product_name']); ?></h2>

        <form id="edit-product-form" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <input type="hidden" name="current-image" value="<?php echo htmlspecialchars($productDetails['product_image']); ?>">

            <div class="input-box">
                <label for="product-name">Product Name:</label>
                <input type="text" name="product-name" id="product-name" value="<?php echo htmlspecialchars($productDetails['product_name']); ?>" required>
            </div>

            <div class="input-box">
                <label for="product-image">Product Image:</label>
                <input type="file" name="product-image" id="product-image">
                <img src="../<?php echo htmlspecialchars($productDetails['product_image']); ?>" alt="Current Product Image" width="100">
            </div>

            <div class="input-box">
                <label for="product-quantity">Quantity:</label>
                <input type="number" name="product-quantity" id="product-quantity" value="<?php echo htmlspecialchars($productDetails['product_quantity']); ?>" required>
            </div>

            <div class="input-box">
                <label for="product-price">Price:</label>
                <input type="text" name="product-price" id="product-price" value="<?php echo htmlspecialchars($productDetails['product_price']); ?>" required>
            </div>

            <div class="input-box">
                <label for="product-description">Description:</label>
                <textarea name="product-description" id="product-description" required><?php echo htmlspecialchars($productDetails['product_description']); ?></textarea>
            </div>

            <div class="input-box">
                <label for="product-category">Category:</label>
                <select name="product-category" id="product-category" required>
                    <option value="Meal" <?php if ($productDetails['product_category'] == 'Meal') echo 'selected'; ?>>Meal</option>
                    <option value="Grocery" <?php if ($productDetails['product_category'] == 'Grocery') echo 'selected'; ?>>Grocery</option>
                    <option value="Clothing" <?php if ($productDetails['product_category'] == 'Clothing') echo 'selected'; ?>>Clothing</option>
                    <option value="Shoes" <?php if ($productDetails['product_category'] == 'Shoes') echo 'selected'; ?>>Shoes</option>
                    <option value="Bags and Accessories" <?php if ($productDetails['product_category'] == 'Bags and Accessories') echo 'selected'; ?>>Bags & Accessories</option>
                </select>
            </div>

            <button type="submit" class="btn">Update Product</button>
        </form>
    <?php else: ?>
        <ul id="product-list">
        <?php
        if ($products) {
            foreach ($products as $prod) {
                echo "<li class='product-item' data-id='" . $prod['product_id'] . "'>";
                echo "<img src='../" . htmlspecialchars($prod['product_image']) . "' alt='" . htmlspecialchars($prod['product_name']) . "'>";
                echo "<div>";
                echo "<h4>" . htmlspecialchars($prod['product_name']) . "</h4>";
                echo "<p><strong>Category:</strong> " . htmlspecialchars($prod['product_category']) . "</p>";
                echo "<p><strong>Price:</strong> ₱" . number_format($prod['product_price'], 2) . "</p>";
                echo "<p><strong>Quantity:</strong> " . htmlspecialchars($prod['product_quantity']) . "</p>";
                echo "<p>" . nl2br(htmlspecialchars($prod['product_description'])) . "</p>";
                echo "</div>";
                echo "<a href='edit_product.php?product_id=" . $prod['product_id'] . "' class='edit-btn'>Edit</a>";
                echo "</li>";
            }
        } else {
            echo "<p>No products found.</p>";
        }
        ?>
        </ul>
    <?php endif; ?>
</div>
    <button id="back-button" class="btn">Back</button>

<script>
$(document).ready(function() {
    $('#edit-product-form').on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: 'edit_product.php', 
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data) {
                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Product Updated!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'edit_product.php'; // Reload page after success
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while updating the product.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    $('#back-button').click(function() {
        window.location.href = 'vendorsprofile.php'; // Redirect to vendor profile page
    });
});
</script>

</body>
</html>
