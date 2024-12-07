<?php 
    require_once '../PHP/vendorConnection.php';

    class account {

        private $conn;
        private $tbl_name = "account"; 
    
        public $id;
        public $name;
        public $uname;
        public $email;
        public $contact;
        public $password;
        public $purpose;
        public $district;
    
        public function __construct($db) {
            $this->conn = $db;
        }
    
        public function register() {
            // Check if terms are accepted
            if (!isset($_POST['terms']) || $_POST['terms'] !== 'accepted') {
                die("You must agree that the information you provided is valid and correct.");
            }
        
            // Get the POST data
            $this->name = $_POST['name'];
            $this->uname = $_POST['username'];
            $this->email = $_POST['email'];
            $this->contact = $_POST['contact'] ?? '';  // Default empty if not provided
            $this->password = $_POST['password'];
            $this->purpose = $_POST['purpose'];
            $this->district = $_POST['district'];
        
            // Check for duplicate username
            $usernameQuery = "SELECT * FROM " . $this->tbl_name . " WHERE Username = :username";
            $usernameStmt = $this->conn->prepare($usernameQuery);
            $usernameStmt->bindParam(':username', $this->uname);
            $usernameStmt->execute();
        
            if ($usernameStmt->rowCount() > 0) {
                echo "<!DOCTYPE html>
                    <html lang='en'>
                    <head>
                        <meta charset='UTF-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                    </head>
                    <body>
                    <script>
                        Swal.fire({
                            title: 'Error!',
                            text: 'The username you provided already exists. Please choose a different username.',
                            icon: 'error',
                            confirmButtonText: 'Okay'
                        }).then(() => {
                            window.history.back(); // Redirect the user back to the previous page
                        });
                    </script>
                    </body>
                    </html>";
                return false;
            }
    
            // Check for duplicate email
            $emailQuery = "SELECT * FROM " . $this->tbl_name . " WHERE Email = :email";
            $emailStmt = $this->conn->prepare($emailQuery);
            $emailStmt->bindParam(':email', $this->email);
            $emailStmt->execute();
        
            if ($emailStmt->rowCount() > 0) {
                echo "<!DOCTYPE html>
                    <html lang='en'>
                    <head>
                        <meta charset='UTF-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                    </head>
                    <body>
                    <script>
                        Swal.fire({
                            title: 'Error!',
                            text: 'The email you provided already exists. Please use a different email.',
                            icon: 'error',
                            confirmButtonText: 'Okay'
                        }).then(() => {
                            window.history.back(); // Redirect the user back to the previous page
                        });
                    </script>
                    </body>
                    </html>";
                return false;
            }
    
            // Check for duplicate contact number
            $contactQuery = "SELECT * FROM " . $this->tbl_name . " WHERE `Contact Number` = :contact";
            $contactStmt = $this->conn->prepare($contactQuery);
            $contactStmt->bindParam(':contact', $this->contact);
            $contactStmt->execute();
        
            if ($contactStmt->rowCount() > 0) {
                echo "<!DOCTYPE html>
                    <html lang='en'>
                    <head>
                        <meta charset='UTF-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                    </head>
                    <body>
                    <script>
                        Swal.fire({
                            title: 'Error!',
                            text: 'The contact number you provided already exists. Please use a different contact number.',
                            icon: 'error',
                            confirmButtonText: 'Okay'
                        }).then(() => {
                            window.history.back(); // Redirect the user back to the previous page
                        });
                    </script>
                    </body>
                    </html>";
                return false;
            }
        
            // Hash the password
            $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
        
            // Prepare the query with named placeholders for the account insertion
            $query = "INSERT INTO " . $this->tbl_name . " 
                        (`full_name`, Username, Email, `Contact Number`, Password, Purpose, District) 
                        VALUES (:name, :username, :email, :contact, :password, :purpose, :district)";
        
            // Prepare the statement
            $stmt = $this->conn->prepare($query);
        
            // Bind the parameters to the placeholders
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':username', $this->uname);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':contact', $this->contact);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':purpose', $this->purpose);
            $stmt->bindParam(':district', $this->district);
        
            // Execute the query and check if it was successful
            if ($stmt->execute()) {
                // If successful, display SweetAlert and redirect
                echo "<!DOCTYPE html>
                    <html lang='en'>
                    <head>
                        <meta charset='UTF-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                    </head>
                    <body>
                    <script>
                        Swal.fire({
                            title: 'Account Created!',
                            text: 'Your account has been successfully created.',
                            icon: 'success',
                            confirmButtonText: 'Login Now'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '../HTML/login.html'; // Redirect to login page
                            }
                        });
                    </script>
                    </body>
                    </html>";
                exit; // Stop further script execution after alert
            } else {
                // Output error if account insert fails
                echo "Error: " . $stmt->errorInfo()[2];
                return false;  // Registration failed
            }
        }
    
    

    public function login() {

        
        $this->uname = trim($_POST['username']);
        $password = trim($_POST['password']);
    
        // Step 1: Fetch account details (account_id, password, and purpose) from the account table
        $query = "SELECT account_id, password, purpose FROM " . $this->tbl_name . " WHERE Username = :username";
    
        // Prepare the statement
        $stmt = $this->conn->prepare($query);
    
        // Bind the parameters
        $stmt->bindParam(':username', $this->uname);
    
        // Execute the query
        if ($stmt->execute()) {
            // Check if user exists
            if ($stmt->rowCount() > 0) {
                $hashedPassword = '';
                $purpose = '';
                $account_id = '';  // Declare variable for account_id
                $stmt->bindColumn('account_id', $account_id);  // Fetch account_id
                $stmt->bindColumn('password', $hashedPassword);
                $stmt->bindColumn('purpose', $purpose);
                $stmt->fetch(PDO::FETCH_ASSOC);
    
                // Step 2: Verify the password
                if (password_verify($password, $hashedPassword)) {
                    $_SESSION['username'] = $this->uname;  // Store username in session
                    $_SESSION['account_id'] = $account_id; // Store account_id for reference
    
                    // Step 3: Redirect based on account type (Seller or Buyer)
                    if ($purpose === "Seller") {
                        $_SESSION['purpose'] = 'Seller';  // Store 'Seller' in session
                        // Get the vendor_id and store it in the session
                        $vendor = new Vendor($this->conn);
                        $vendorDetails = $vendor->getVendor($this->uname); // Get vendor details
                        $_SESSION['vendor_id'] = $vendorDetails['vendor_id'];  // Store vendor_id in session
                        header("Location: vendorsprofile.php");  // Redirect to vendor profile
                    } elseif ($purpose === "Buyer") {
                        $_SESSION['username'] = $this->uname;
                        $_SESSION['purpose'] = 'Buyer';
                    
                        // Fetch buyer_id from the buyer table
                        $buyerQuery = "SELECT buyer_id FROM buyer WHERE account_id = :account_id";
                        $buyerStmt = $this->conn->prepare($buyerQuery);
                        $buyerStmt->bindParam(':account_id', $account_id);
                        $buyerStmt->execute();
                        
                        if ($buyerRow = $buyerStmt->fetch(PDO::FETCH_ASSOC)) {
                            $_SESSION['buyer_id'] = $buyerRow['buyer_id'];
                        } else {
                            // Handle the case where buyer_id is not found
                            echo "Error: Buyer ID not found.";
                            exit;
                        }
                    
                        header("Location: ../PHP/customerProfile.php");
                        exit; // Stop further script execution after redirection
                    }
                     else {
                        echo "Invalid account type.";
                    }
                    exit;
                } else {
                    echo "<!DOCTYPE html>
                    <html lang='en'>
                    <head>
                        <meta charset='UTF-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <title>Error Login</title>
                        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                    </head>
                    <body>
                    <script>
                    Swal.fire({
                        title: 'Error!',
                        text: 'Incorrect password. Please try again!',
                        icon: 'error',
                        confirmButtonText: 'Okay'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '../HTML/login.html';
                        }
                    });
                    </script>
                    </body>
                    </html>";
                }
            } else {
                echo "<!DOCTYPE html>
                    <html lang='en'>
                    <head>
                        <meta charset='UTF-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <title>Error Login</title>
                        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                    </head>
                    <body>
                    <script>
                    Swal.fire({
                        title: 'Error!',
                        text: 'No account found with this username. Please try again!',
                        icon: 'error',
                        confirmButtonText: 'Okay'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '../HTML/login.html';
                        }
                    });
                    </script>
                    </body>
                    </html>";
            }
        } else {
            echo "Error: " . $stmt->errorInfo()[2];
        }
    }
}

?>