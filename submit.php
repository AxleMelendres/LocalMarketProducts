<?php

$hostname = 'localhost';
$dbname = 'dbgroup1';
$username = 'root';
$password = '';

$conn = new mysqli($hostname, $username, $password, $dbname);

if($conn->connect_error){
    die("Connection Failed" .$conn->connect_error);
} 

if($_SERVER["REQUEST_METHOD"] == "POST")
{

    if (!isset($_POST['terms']) || $_POST['terms'] !== 'accepted') {
        die("You must agree that the information you provided are valid and correct.");
    }

    $name = $_POST['name'];
    $uname = $_POST['username'];
    $email = $_POST['email'];
    $contact = $_POST['contact'] ?? '';
    $password = $_POST['password'];
    $purpose = $_POST['purpose'];
    $district = $_POST['district'];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);//add

    $txt = $conn->prepare("INSERT INTO account (`Full Name`, Username, Email, `Contact Number`, Password, Purpose, District) VALUES (?, ?, ?, ?, ?, ?, ?)");
   
    if ($txt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $txt->bind_param("sssssss", $name, $uname, $email, $contact, $hashedPassword, $purpose, $district);

    if ($txt->execute()) {
         echo "Record created successfully"; 
        }else {
         echo "Error: " . $txt->error;
        }
        $txt->close();
}

$conn->close();

?>