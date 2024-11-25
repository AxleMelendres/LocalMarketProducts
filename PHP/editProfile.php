<?php
session_start();
require_once '../PHP/dbConnection.php';
require_once '../PHP/vendorConnection.php'; 

if (!isset($_SESSION['username']) || $_SESSION['purpose'] !== 'Seller') {
    header("Location: vendorsprofile.php");
    exit;
}

$database = new Database();
$conn = $database->getConnection();

$vendor_uname = $_SESSION['username']; 
$vendor = new Vendor($conn);
$vendorDetails = $vendor->getVendor($vendor_uname);

// Update vendor profile when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch form data using isset() to avoid undefined index errors
    $vendor_name = isset($_POST['vendor_name']) ? $_POST['vendor_name'] : '';
    $vendor_description = isset($_POST['vendor_description']) ? $_POST['vendor_description'] : '';
    $vendor_email = isset($_POST['vendor_email']) ? $_POST['vendor_email'] : '';
    $vendor_contact = isset($_POST['vendor_contact']) ? $_POST['vendor_contact'] : '';
    $vendor_address = isset($_POST['vendor_address']) ? $_POST['vendor_address'] : '';
    $vendor_district = isset($_POST['vendor_district']) ? $_POST['vendor_district'] : '';

    // Set existing image as the default value
    $profile_image = $vendorDetails['vendor_image']; 
    
    // Check if a new image was uploaded
    if (isset($_FILES['vendor_image']['name']) && !empty($_FILES['vendor_image']['name'])) {
        $image_name = $_FILES['vendor_image']['name'];
        $image_tmp_name = $_FILES['vendor_image']['tmp_name'];
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($image_ext, $allowed_exts)) {
            // Generate a unique image name and define the upload path
            $new_image_name = uniqid('vendor_') . '.' . $image_ext;
            $image_upload_path = "../uploads/" . $new_image_name;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($image_tmp_name, $image_upload_path)) {
                // Update the profile image path
                $profile_image = $image_upload_path;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Invalid image format. Please upload JPG, JPEG, PNG, or GIF.";
        }
    }

    // Update the vendor details in the database
    if (!isset($error)) {  // Check if there's no error before executing the update query
        $updateQuery = "UPDATE vendor SET vendor_name = :vendor_name, vendor_description = :vendor_description,
                        vendor_email = :vendor_email, vendor_contact = :vendor_contact,
                        vendor_address = :vendor_address, vendor_district = :vendor_district,
                        vendor_image = :vendor_image WHERE vendor_username = :vendor_uname";

        $stmt = $conn->prepare($updateQuery);
        $stmt->bindParam(':vendor_name', $vendor_name);
        $stmt->bindParam(':vendor_description', $vendor_description);
        $stmt->bindParam(':vendor_email', $vendor_email);
        $stmt->bindParam(':vendor_contact', $vendor_contact);
        $stmt->bindParam(':vendor_address', $vendor_address);
        $stmt->bindParam(':vendor_district', $vendor_district);
        $stmt->bindParam(':vendor_image', $profile_image);
        $stmt->bindParam(':vendor_uname', $vendor_uname);

        if ($stmt->execute()) {
            header("Location: vendorsprofile.php");
            exit(); 
        } else {
            $error = "Failed to update profile. Please try again.";
        }
    }
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Vendor Profile</title>
    <link rel="stylesheet" href="../CSS/editProfile.css"> 
    <script src="../JS/product.js" defer></script>
    <script src="https://kit.fontawesome.com/89e47c0436.js" crossorigin="anonymous"></script>
</head>
<body>

<?php require "../HEADER/header.html"; ?>

<div class="container">
    <h2>Edit Profile</h2>
    
    <!-- Display success or error message -->
    <?php if (isset($success) && $success != '') { echo "<div class='success'>$success</div>"; } ?>
    <?php if (isset($error) && $error != '') { echo "<div class='error'>$error</div>"; } ?>

    <!-- Vendor Edit Form -->
    <form action="editProfile.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="vendor_name">Name</label>
            <input type="text" id="vendor_name" name="vendor_name" value="<?php echo htmlspecialchars($vendorDetails['vendor_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="vendor_description">Description</label>
            <textarea id="vendor_description" name="vendor_description" required><?php echo htmlspecialchars($vendorDetails['vendor_description']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="vendor_email">Email</label>
            <input type="email" id="vendor_email" name="vendor_email" value="<?php echo htmlspecialchars($vendorDetails['vendor_email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="vendor_contact">Phone</label>
            <input type="text" id="vendor_contact" name="vendor_contact" value="<?php echo htmlspecialchars($vendorDetails['vendor_contact']); ?>" required>
        </div>
        <div class="form-group">
            <label for="vendor_address">Location</label>
            <input type="text" id="vendor_address" name="vendor_address" value="<?php echo htmlspecialchars($vendorDetails['vendor_address']); ?>" required>
        </div>
        <div class="form-group">
            <label for="vendor_district">District</label>
            <select id="vendor_district" name="vendor_district" required>
                <option value="South District" <?php echo ($vendorDetails['vendor_district'] == 'South District') ? 'selected' : ''; ?>>South District</option>
                <option value="Urban District" <?php echo ($vendorDetails['vendor_district'] == 'Urban District') ? 'selected' : ''; ?>>Urban District</option>
                <option value="West District" <?php echo ($vendorDetails['vendor_district'] == 'West District') ? 'selected' : ''; ?>>West District</option>
                <option value="North District" <?php echo ($vendorDetails['vendor_district'] == 'North District') ? 'selected' : ''; ?>>North District</option>
                <option value="East District" <?php echo ($vendorDetails['vendor_district'] == 'East District') ? 'selected' : ''; ?>>East District</option>
            </select>
        </div>
        <div class="form-group">
            <label for="vendor_image">Profile Image</label>
            <input type="file" id="vendor_image" name="vendor_image">
        </div>
        <button type="submit" class="btn">Update Profile</button>
    </form>
</div>
    <button id="back-button" class="btn">Back</button>
</body>
</html>
