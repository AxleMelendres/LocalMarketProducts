<?php 
    require_once 'MainDataBase.php';
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];


        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['email'] = $row['email'];
                header("Location: index.html");
                exit();
            } else {
                echo '<h1>Incorrect password!</h1>';
            }
        } else {
            echo '<h1>Account not found, Incorrect email or password!</h1>';
        }
    }
?>