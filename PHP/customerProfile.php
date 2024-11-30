<?php
session_start();

// Include required files
require_once '../PHP/dbConnection.php';
require_once '../PHP/customerConnection.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Create a database connection
$database = new Database();
$conn = $database->getConnection();

// Create an instance of the Customer class
$customer = new Customer($conn);

try {
    // Fetch customer details
    $user = $customer->getCustomerDetails($username);

    if (!$user) {
        die("User not found.");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../CSS/customerprofile.css">
</head>
<body>
    <?php require "../ConnectedBuyer/HEADER/profileheader.html"; ?>

    <main class="profile-container">
        <aside class="sidebar">
            <div class="profile-section">
                <!-- Display profile picture -->
                <?php if (!empty($user['buyer_image'])): ?>
                    <img src="<?php echo htmlspecialchars($user['buyer_image']); ?>" alt="Profile Picture" class="profile-img">
                <?php else: ?>
                    <img src="https://via.placeholder.com/100" alt="Profile Picture" class="profile-img">
                <?php endif; ?>
                <h2 class="profile-name"><?php echo htmlspecialchars($user['Full Name']); ?></h2>
                <p class="profile-username">@<?php echo htmlspecialchars($user['Username']); ?></p>
                <a href="accountSettings.php" class="edit-button">Edit Profile</a>
            </div>
            <ul class="sidebar-links">
                <li><a href="#">Order History</a></li>
                <li><a href="../PHP/reservedProduct.php">Reserved Items</a></li>
                <li><a href="../PHP/accountSettings.php">Account Settings</a></li>
                <li><a href="#">Help & Support</a></li>
            </ul>
        </aside>

        <section class="main-content">
            <div class="profile-details">
                <h2>Profile Details</h2>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['Email']); ?></p>
                <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($user['Contact Number']); ?></p>
                <p><strong>Purpose:</strong> <?php echo htmlspecialchars($user['Purpose']); ?></p>
                <p><strong>District:</strong> <?php echo htmlspecialchars($user['District']); ?></p>
            </div>
        </section>
    </main>
</body>
</html>
