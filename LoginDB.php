<?php 

    require_once 'MainDataBase.php';

    if (isset ($_POST['registration-form'])){
        $full_name = $_POST ['fName'];
        $username = $_POST ['userName'];
        $email = $_POST ['email'];
        $phone_number = $_POST['pNumber'];
        $password = $_POST ['password'];
        $password = md5($password);

        $checkEmail = "SELECT * FROM users where email = '$email' ";
        $result = $connect->query($checkEmail);
        if ($result->num_rows>0){
            echo '<h1> The email already exists. Choose another one! <h1>';
        } else {
            $insertQuery = "INSERT INTO Users (fName, userName, email, pNumber, password)
            VALUES('$full_name', '$username', '$email', '$phone_number', '$password')";
            if($connect->query($insertQuery)==TRUE){
                header("Location: index.html");
            }else {
                echo 'Connection Error: ' .$connect->connect_error;
            }
        }

    }

?>