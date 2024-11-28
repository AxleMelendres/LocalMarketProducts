<?php

session_start();

$hostname = 'localhost';
$dbname = 'dbgroup1';
$username = 'root';
$password = '';

$conn = new mysqli($hostname, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT `Full Name`, Username, Email, `Contact Number`, Purpose, District, buyer_image FROM account LEFT JOIN buyer ON account.account_id = buyer.account_id WHERE Username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../CSS/customerprofile.css">
</head>
<body>
    <?php  require "../ConnectedBuyer/HEADER/profileheader.html" ?>

    <main class="profile-container">
        <aside class="sidebar">
            <div class="profile-section">
                <!-- Display the profile picture -->
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../JS/logout.js"></script>
</body>
</html>
