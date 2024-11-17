<?php
session_start();

require_once '../PHP/dbConnection.php';
require_once '../DB/accountTB.php';

$database = new Database();
$conn = $database->getConnection();

$account = new account($conn);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the username and password from the POST request
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Call the login method of the account class
    $account->login($username, $password);
}
?>