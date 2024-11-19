<?php
session_start();  // Start the session

// Include necessary files
require_once '../PHP/dbConnection.php';  // Database connection
require_once '../DB/accountTB.php';  // Account logic

// Create a new Database instance
$database = new Database();
$conn = $database->getConnection();

// Create an instance of the account class
$account = new Account($conn);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Bind POST data to the class properties
    $account->name = $_POST['name'];
    $account->uname = $_POST['username'];
    $account->email = $_POST['email'];
    $account->contact = $_POST['contact'];
    $account->password = $_POST['password'];
    $account->purpose = $_POST['purpose'];
    $account->district = $_POST['district'];

    if ($account->register()) {

        header("Location: ../HTML/login.html");
        exit;
    } else {
        echo "Registration failed!";
    }
}
?>
