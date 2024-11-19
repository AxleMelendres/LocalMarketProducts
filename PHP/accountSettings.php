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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update account information
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];

    // Handle file upload for profile picture
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_name = $_FILES['profile_picture']['name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array(strtolower($file_ext), $allowed_exts)) {
            $upload_dir = '../uploads/';
            $new_file_name = uniqid() . '.' . $file_ext;
            $file_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp, $file_path)) {
                // Update the profile picture in the database
                $stmt = $conn->prepare("UPDATE buyer SET buyer_image = ? WHERE account_id = (SELECT account_id FROM account WHERE Username = ?)");
                $stmt->bind_param("ss", $file_path, $username);
                $stmt->execute();
                $stmt->close();
            } else {
                $error = "Error uploading file.";
            }
        } else {
            $error = "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
    }

    // Update other account details
    $stmt = $conn->prepare("UPDATE account SET `Full Name` = ?, Email = ?, `Contact Number` = ? WHERE Username = ?");
    $stmt->bind_param("ssss", $full_name, $email, $contact_number, $username);
    if ($stmt->execute()) {
        $success = "Account details updated successfully!";
    } else {
        $error = "Error updating account details. Please try again.";
    }
    $stmt->close();
}

// Fetch account details
$stmt = $conn->prepare("SELECT account.account_id, account.`Full Name`, account.Email, account.`Contact Number`, buyer.buyer_name, buyer.buyer_email, buyer.buyer_contact, buyer.buyer_image 
                        FROM account 
                        LEFT JOIN buyer ON account.account_id = buyer.account_id 
                        WHERE account.Username = ?");
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings</title>
    <link rel="stylesheet" href="../CSS/accountsettings.css">
    <script src="https://kit.fontawesome.com/89e47c0436.js" crossorigin="anonymous"></script>
</head>
<body>
    <header class="navbar">
        <h1 class="logo">Market Alchemy</h1>
        <nav>
            <ul class="nav-links">
                <li><a href="../PHP/customerInterface.php" style="color: wheat;">Home</a></li>
                <li><a href="../PHP/reservedProduct.php" style="color: wheat;"><i class="fas fa-shopping-cart"></i></a></li>
                <li><a href="../PHP/customerLogout.php" class="logout-button" style="color: wheat;">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="profile-container">
        <section class="main-content">
            <h2>Account Settings</h2>
            <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
            <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
            <form method="POST" action="" enctype="multipart/form-data">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['buyer_name'] ?? $user['Full Name']); ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['buyer_email'] ?? $user['Email']); ?>" required>

                <label for="contact_number">Contact Number:</label>
                <input type="text" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($user['buyer_contact'] ?? $user['Contact Number']); ?>" required>

                <label for="profile_picture">Profile Picture:</label>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*">

                <button type="submit" class="details-button" style="color: wheat;">Save Changes</button>
            </form>
        </section>
    </main>
</body>
</html>
