<?php
session_start();

// Include dbConnection.php and Customer class
require_once 'dbConnection.php';
require_once 'customerConnection.php';

$database = new Database();
$conn = $database->getConnection();
$customer = new Customer($conn);

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $profile_picture = $_FILES['profile_picture'] ?? null;

    // Call the update function
    $result = $customer->updateCustomerDetails($username, $full_name, $email, $contact_number, $profile_picture);

    // Check the result
    if (strpos($result, 'successfully') !== false) {
        $success = $result;
    } else {
        $error = $result;
    }
}

// Fetch account details
$user = $customer->getCustomerDetails($username);
if (!$user) {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings</title>
    <link rel="stylesheet" href="../CSS/accountsettings.css">
    <script src="https://kit.fontawesome.com/89e47c0436.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
</head>
<body>
    <header class="navbar">
        <h1 class="logo">Market Alchemy</h1>
        <nav>
            <ul class="nav-links">
                <li><a href="../ConnectedBuyer/main.php" style="color: wheat;">Home</a></li>
                <li><a href="../PHP/reservedProduct.php" style="color: wheat;"><i class="fas fa-shopping-cart"></i></a></li>
                <li><a href="../PHP/customerLogout.php" class="logout-button" style="color: wheat;">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="profile-container">
        <section class="main-content">
            <h2>Account Settings</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>

                <label for="contact_number">Contact Number:</label>
                <input type="text" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($user['Contact Number'] ?? ''); ?>" required>



                <label for="profile_picture">Profile Picture:</label>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*">

                <button type="submit" class="details-button" style="color: wheat;">Save Changes</button>
            </form>
        </section>
    </main>

    <!-- SweetAlert Trigger -->
    <?php if ($success): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?php echo $success; ?>',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'customerProfile.php';
            }
        });
    </script>
    <?php elseif ($error): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '<?php echo $error; ?>',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Try Again'
        });
    </script>
    <?php endif; ?>

</body>
</html>
