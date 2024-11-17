<?php

require_once '../PHP/dbConnection.php';
require_once '../DB/accountTB.php';

$database = new Database();
$conn = $database->getConnection();

$account = new Account($conn);
if ($account->register()) {
    header("Location: ../HTML/loginn.html");
    exit;
} else {
    echo "Registration failed!";
}

?>