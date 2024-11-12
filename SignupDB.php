<?php

    require_once 'MainDataBase.php';
    
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signUP'])) {

    $fullName = $_POST['fName'];
    $userName = $_POST['userName'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['pNumber'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $userType = $_POST['user_type'];


    $sql = "INSERT INTO users (full_name, username, email, phone_number, password, user_type) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $fullName, $userName, $email, $phoneNumber, $password, $userType);

    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $stmt->error;
    }



    $stmt->close();
    $conn->close();
}
?>
