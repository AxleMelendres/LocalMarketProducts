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

    // Fetch the latest reservation
    $latestReservation = $customer->getLatestReservation($user['buyer_id']);

    if (!$latestReservation) {
        $latestReservation = null; // No reservation found
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Profile</title>
    <link rel="stylesheet" href="../CSS/customerprofile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php require "../ConnectedBuyer/HEADER/profileheader.html"; ?>

    <main class="profile-container">
        <aside class="sidebar">
            <div class="profile-section">
                <?php if (!empty($user['buyer_image'])): ?>
                    <img src="<?php echo htmlspecialchars($user['buyer_image']); ?>" alt="Profile Picture" class="profile-img">
                <?php else: ?>
                    <img src="https://via.placeholder.com/100" alt="Profile Picture" class="profile-img">
                <?php endif; ?>
                <h2 class="profile-name"><?php echo htmlspecialchars($user['full_name']); ?></h2>
                <p class="profile-username">@<?php echo htmlspecialchars($user['Username']); ?></p>
                <a href="accountSettings.php" class="edit-button">Edit Profile</a>
            </div>
            <ul class="sidebar-links">
                <li><a href="#">Order History</a></li>
                <li><a href="../PHP/reservedProduct.php">Reserved Items</a></li>
                <li><a href="../PHP/accountSettings.php">Account Settings</a></li>
                <li><a href="../PHP/HelpandSupport.php">Help & Support</a></li>
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

            <div class="latest-reservation">
            <div class="latest-reservation">
    <h2>Latest Reserved Product</h2>
    <?php if ($latestReservation): ?>
        <div class="reservation-details">
            <div class="product-info">
                <div class="product-details">
                    <p><strong>Product Name:</strong> <?php echo htmlspecialchars($latestReservation['product_name']); ?></p>
                    <p><strong>Price:</strong> ₱<?php echo htmlspecialchars($latestReservation['product_price']); ?></p>
                    <p><strong>Quantity Reserved:</strong> <?php echo htmlspecialchars($latestReservation['reserved_quantity']); ?></p>
                    <p><strong>Total Price:</strong> ₱<?php echo htmlspecialchars($latestReservation['total_price']); ?></p>

                    <?php
                    $reservedDateTime = new DateTime($latestReservation['reserved_date']);
                    $formattedDateTime = $reservedDateTime->format('F j, Y, g:i a');
                    ?>
                    <p><strong>Reserved Date: </strong> <?php echo $formattedDateTime; ?></p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <p>You haven't reserved any products yet.</p>
    <?php endif; ?>
</div>


        </section>
    </main>
</body>
</html>
