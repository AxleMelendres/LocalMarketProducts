<?php

require_once '../PHP/dbConnection.php';
require_once '../DB/productsTB.php';

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $productName = htmlspecialchars($_POST['product-name']);
    $productQuantity = intval($_POST['new-product-quantity']);
    $productPrice = floatval($_POST['product-price']);
    $productDescription = htmlspecialchars($_POST['product-description']);
    $productCategory = htmlspecialchars($_POST['product-category']); 

    // Ensure the vendor is logged in and retrieve their vendor_id
    $vendorId = $_SESSION['vendor_id']; // Assuming vendor_id is stored in session upon login.

    if (isset($_FILES['product-image']) && $_FILES['product-image']['error'] == 0) {
        $imageTmpName = $_FILES['product-image']['tmp_name'];
        $imageName = $_FILES['product-image']['name'];
        $imageType = $_FILES['product-image']['type'];
        $imageSize = $_FILES['product-image']['size'];

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($imageType, $allowedTypes)) {

            $uploadDir = '../uploads/';
            $imagePath = $uploadDir . basename($imageName);

            if (move_uploaded_file($imageTmpName, $imagePath)) {

                // Create a new Product object
                $product = new Product($conn);
                $product->product_name = $productName;
                $product->product_image = $imagePath;
                $product->product_quantity = $productQuantity;
                $product->product_price = $productPrice;
                $product->product_description = $productDescription;
                $product->product_category = $productCategory; 
                $product->vendor_id = $vendorId; // Set the vendor_id

                if ($product->create()) {
                    echo "Product added successfully!";
                    header('Location: vendorsprofile.php');
                    exit;
                } else {
                    echo "Error: Could not add product.";
                }
            } else {
                echo "Failed to upload image.";
            }
        } else {
            echo "Invalid image type. Only JPG, PNG, and GIF are allowed.";
        }
    } else {
        echo "No image uploaded or error during upload.";
    }
}
?>