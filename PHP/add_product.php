<?php
session_start();  

require_once '../PHP/dbConnection.php';
require_once '../DB/productsTB.php';
require_once '../PHP/vendorConnection.php';

$database = new Database();
$conn = $database->getConnection();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if vendor_id is set in session
    if (!isset($_SESSION['vendor_id'])) {
        echo "<script>alert('Vendor ID is not set in session. Please log in.');</script>";
        exit;
    }

    $vendor_id = $_SESSION['vendor_id']; // Retrieve vendor_id from session

    // Collect product details from the form
    $productName = htmlspecialchars($_POST['product-name']);
    $productQuantity = intval($_POST['new-product-quantity']);
    $productPrice = floatval($_POST['product-price']);
    $productDescription = htmlspecialchars($_POST['product-description']);
    $productCategory = htmlspecialchars($_POST['product-category']); 

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
                $product->vendor_id = $vendor_id; // Assign vendor_id from session

                // Attempt to create the product in the database
                if ($product->create()) {
                    // Success message with SweetAlert
                    echo "
                    <!DOCTYPE html>
                    <html lang='en'>
                    <head>
                        <meta charset='UTF-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <title>SweetAlert</title>
                        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                    </head>
                    <body>
                    <script>
                    Swal.fire({
                        title: 'Product Added Successfully!',
                        text: 'Your product has been added.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if(result.isConfirmed) {
                            window.location.href = 'vendorsprofile.php';
                        }
                    });
                    </script>
                    </body>
                    </html>";
                    exit;
                } else {
                    // Error message if product creation fails
                    echo "
                    <!DOCTYPE html>
                    <html lang='en'>
                    <head>
                        <meta charset='UTF-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <title>SweetAlert</title>
                        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                    </head>
                    <body>
                    <script>
                    Swal.fire({
                        title: 'Error!',
                        text: 'Could not add product. Please try again later.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    </script>
                    </body>
                    </html>";
                }
            } else {
                // Error if the image upload fails
                echo "
                <!DOCTYPE html>
                <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>SweetAlert</title>
                    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                </head>
                <body>
                <script>
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to upload image.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                </script>
                </body>
                </html>";
            }
        } else {
            // Error if the image type is invalid
            echo "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>SweetAlert</title>
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            </head>
            <body>
            <script>
            Swal.fire({
                title: 'Error!',
                text: 'Invalid image type. Only JPG, PNG, and GIF are allowed.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            </script>
            </body>
            </html>";
        }
    } else {
        // Error if no image was uploaded
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>SweetAlert</title>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
        <script>
        Swal.fire({
            title: 'Error!',
            text: 'No image uploaded or error during upload.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        </script>
        </body>
        </html>";
    }
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
    <script src="../JS/product.js" defer></script>
</head>
<body>
    <header>
        <h1>Add New Product</h1>
    </header>

    <div class="container">
        <form id="product-form" action="add_product.php" method="POST" enctype="multipart/form-data">
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
        
            <!-- Category Dropdown -->
            <div class="input-box">
                <label for="product-category">Product Category:</label>
                <select id="product-category" name="product-category" required>
                    <option value="Meal">Meal</option>
                    <option value="Grocery">Grocery</option>
                    <option value="Clothing">Clothing</option>
                    <option value="Shoes">Shoes</option>
                    <option value="Bags and Accessories">Bags & Accessories</option>
                    <!-- Add more categories as needed -->
                </select>
            </div>
        
            <button type="submit" class="btn">Add Product</button>
        </form>
    </div>

    <button id="back-button" class="btn">Back</button>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.0/dist/sweetalert2.all.min.js"></script>
    <script>
        // Attach an event listener to the form submit button
        document.getElementById('product-form').addEventListener('submit', function(event) {
            event.preventDefault();  // Prevent the default form submission

            // Show SweetAlert confirmation dialog before submitting the form
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to add this product?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, add it',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with form submission if confirmed
                    this.submit();  // Manually submit the form
                }
            });
        });
    </script>
</body>
</html>
