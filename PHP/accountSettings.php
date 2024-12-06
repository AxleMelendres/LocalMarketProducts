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
    $district = $_POST['district'];
    $profile_picture = $_FILES['profile_picture'] ?? null;

    // Call the update function
    $result = $customer->updateCustomerDetails($username, $full_name, $email, $contact_number, $district, $profile_picture);

    // Check the result
    if (strpos($result, 'successfully') !== false) {
        $success = $result;
    } else {
        $error = $result;
    }
}

// Fetch account details
$user = $customer->getCustomerDetails($username);
if ($user === false) {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings</title>
    <link rel="stylesheet" href="../CSS/Accountsettings.css">
    <script src="https://kit.fontawesome.com/89e47c0436.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
</head>
<body>
    <main class="profile-container">
        <section class="main-content">
            <h2>Account Settings</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="full_name">Full Name:</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="contact_number">Contact Number:</label>
                    <input type="text" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($user['Contact Number'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="profile_picture" class="file-input-label">Choose Profile Picture</label>
                    <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
                    <div class="file-name-display" id="file-name-display"></div>
                </div>

                <div class="form-group">
                    <label for="district">District:</label>
                    <select id="district" name="district" required>
                        <option value="" disabled <?php echo empty($user['District']) ? 'selected' : ''; ?>>Select your district</option>
                        <option value="South District" <?php echo $user['District'] == 'South District' ? 'selected' : ''; ?>>South District</option>
                        <option value="North District" <?php echo $user['District'] == 'North District' ? 'selected' : ''; ?>>North District</option>
                        <option value="West District" <?php echo $user['District'] == 'West District' ? 'selected' : ''; ?>>West District</option>
                        <option value="East District" <?php echo $user['District'] == 'East District' ? 'selected' : ''; ?>>East District</option>
                        <option value="Urban District" <?php echo $user['District'] == 'Urban District' ? 'selected' : ''; ?>>Urban District</option>
                    </select>
                </div>

                <div class="button-container">
                    <a href="customerProfile.php" class="back-button">Back</a>
                    <button type="submit" class="details-button">Save Changes</button>
                </div>
            </form>
        </section>
    </main>

    <script>
    document.getElementById('profile_picture').addEventListener('change', function(e) {
        var fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
        document.getElementById('file-name-display').textContent = fileName;
    });
    </script>

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