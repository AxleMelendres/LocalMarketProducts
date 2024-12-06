<?php
// Start PHP script block
session_start();
require_once '../PHP/dbConnection.php';
require_once '../DB/productsTB.php';
require_once '../PHP/vendorConnection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json'); // Ensure JSON response
    $database = new Database();
    $conn = $database->getConnection();

    if (!isset($_SESSION['vendor_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Vendor ID is not set in session. Please log in.']);
        exit;
    }

    $vendor_id = $_SESSION['vendor_id'];

    // Collect form data
    $productName = htmlspecialchars($_POST['product-name']);
    $productQuantity = intval($_POST['new-product-quantity']);
    $productPrice = floatval($_POST['product-price']);
    $productDescription = htmlspecialchars($_POST['product-description']);
    $productCategory = htmlspecialchars($_POST['product-category']);

    if (isset($_FILES['product-image']) && $_FILES['product-image']['error'] == 0) {
        $imageTmpName = $_FILES['product-image']['tmp_name'];
        $imageName = $_FILES['product-image']['name'];
        $imageType = $_FILES['product-image']['type'];

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($imageType, $allowedTypes)) {
            $uploadDir = '../uploads/';
            $imagePath = $uploadDir . basename($imageName);

            if (move_uploaded_file($imageTmpName, $imagePath)) {
                $product = new Product($conn);
                $product->product_name = $productName;
                $product->product_image = $imagePath;
                $product->product_quantity = $productQuantity;
                $product->product_price = $productPrice;
                $product->product_description = $productDescription;
                $product->product_category = $productCategory;
                $product->vendor_id = $vendor_id;

                if ($product->create()) {
                    echo json_encode(['status' => 'success', 'message' => 'Product added successfully!']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Could not add product. Please try again later.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to upload image.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid image type. Only JPG, PNG, and GIF are allowed.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No image uploaded or error during upload.']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.0/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/add_product.css">
</head>

<body>
    <header>
        <h1>Add New Product</h1>
    </header>

    <div class="container">
        <form id="product-form" enctype="multipart/form-data">
            <div class="input-box">
                <label for="product-name">Product Name:</label>
                <input type="text" id="product-name" name="product-name" required>
            </div>

            <div class="input-box">
                <label for="product-image">Product Image:</label>
                <input type="file" id="product-image" name="product-image" accept="image/*" required>
            </div>

            <div class="input-box">
                <label for="new-product-quantity">Product Quantity:</label>
                <input type="number" id="new-product-quantity" name="new-product-quantity" required min="1">
            </div>

            <div class="input-box">
                <label for="product-price">Product Price:</label>
                <input type="number" id="product-price" name="product-price" required min="0" step="0.01">
            </div>

            <div class="input-box">
                <label for="product-description">Product Description:</label>
                <textarea id="product-description" name="product-description" required></textarea>
            </div>

            <div class="input-box">
                <label for="product-category">Product Category:</label>
                <select id="product-category" name="product-category" required>
                    <option value="Meal">Meal</option>
                    <option value="Grocery">Grocery</option>
                    <option value="Clothing">Clothing</option>
                    <option value="Shoes">Shoes</option>
                    <option value="Bags and Accessories">Bags & Accessories</option>
                </select>
            </div>

            <button type="submit" class="btn">Add Product</button>
        </form>
    </div>

    <a href="../PHP/vendorsprofile.php">
        <button id="back-button" class="btn">Back</button>
    </a>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.0/dist/sweetalert2.all.min.js"></script>
    <script>
        document.getElementById('product-form').addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent default form submission

            const formData = new FormData(this); // Collect form data

            // Use fetch for AJAX request
            fetch('', { // Empty action submits to the same PHP file
                method: 'POST',
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = 'vendorsprofile.php'; // Redirect on success
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong. Please try again later.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    console.error('Error:', error);
                });
        });
    </script>
</body>

</html>