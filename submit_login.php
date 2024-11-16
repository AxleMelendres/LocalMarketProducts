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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT password, purpose FROM account WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashedPassword, $purpose);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['username'] = $username;

            if ($purpose === "Seller") {
                header("Location: vendorsprofile.php");
            } elseif ($purpose === "Buyer") {
                header("Location: customerprofile.php");
            } else {
                echo "Invalid account type.";
            }
            exit;
        } else {
            echo "Invalid password. Please try again.";
        }
    } else {
        echo "No account found with that username.";
    }
    $stmt->close();
}
$conn->close();
?>