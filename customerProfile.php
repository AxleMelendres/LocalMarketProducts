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

$stmt = $conn->prepare("SELECT `Full Name`, Username, Email, `Contact Number`, Purpose, District FROM account WHERE Username = ?");
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
    <title>Customer Profile</title>
    <link rel="stylesheet" href="customerProfile.css">
</head>
<body>
    <div class="profile-container">
        <header>
            <h1>Welcome, <?php echo htmlspecialchars($user['Full Name']); ?>!</h1>
        </header>
        <div class="profile-details">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['Username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['Email']); ?></p>
            <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($user['Contact Number']); ?></p>
            <p><strong>Purpose:</strong> <?php echo htmlspecialchars($user['Purpose']); ?></p>
            <p><strong>District:</strong> <?php echo htmlspecialchars($user['District']); ?></p>
        </div>
        <a href="logout.php" class="logout-button">Logout</a>
    </div>
</body>
</html>