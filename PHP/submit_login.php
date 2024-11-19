<?php
session_start();  // Start the session

require_once '../PHP/dbConnection.php';  // Database connection
require_once '../DB/accountTB.php';  // Account logic

$database = new Database();
$conn = $database->getConnection();

$account = new Account($conn);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Call the login method directly without parameters
    $account->login();

    // No need to check login success here, as login method handles the redirection
}

?>
