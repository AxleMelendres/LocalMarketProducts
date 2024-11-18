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
$stmt = $conn->prepare("SELECT `Full Name`, Email, `Contact Number` FROM account WHERE Username = ?");
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
            <form method="POST" action="">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['Full Name']); ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>

                <label for="contact_number">Contact Number:</label>
                <input type="text" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($user['Contact Number']); ?>" required>

                <button type="submit" class="details-button" style="color: wheat;">Save Changes</button>
            </form>
        </section>
    </main>
</body>
</html>
