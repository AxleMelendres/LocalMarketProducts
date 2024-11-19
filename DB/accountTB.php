<?php 
    require_once 'vendorConnection.php';

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

    public function getAccountById($account_id) {
        // Prepare the query
        $query = "SELECT * FROM account WHERE account_id = :account_id LIMIT 0, 25";
        
        // Prepare the statement
        $stmt = $this->conn->prepare($query);

        // Bind the parameter to the placeholder
        $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Fetch the results
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the results (or handle them as needed)
        return $results;
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
    
        // Hash the password
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
    
        // Prepare the query with named placeholders for the account insertion
        $query = "INSERT INTO " . $this->tbl_name . " 
                    (`Full Name`, Username, Email, `Contact Number`, Password, Purpose, District) 
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
            // Get the last inserted ID from the account table (which is now account_id)
            $account_id = $this->conn->lastInsertId(); // This retrieves the newly inserted account_id
    
            // If the user is a seller, insert data into the vendor table
            if ($this->purpose === "Seller") {
                // Ensure that seller-related fields (address and description) are available
                if (isset($_POST['address']) && isset($_POST['description'])) {
                    // Prepare the query to insert data into the vendor table
                    $vendorQuery = "INSERT INTO vendor 
                                    (account_id, vendor_name, vendor_username, vendor_contact, vendor_password, vendor_address, vendor_district, vendor_description, vendor_email)
                                    VALUES (:account_id, :vendor_name, :vendor_username, :vendor_contact, :vendor_password, :vendor_address, :vendor_district, :vendor_description, :vendor_email)";
    
                    // Prepare the statement for the vendor table
                    $vendorStmt = $this->conn->prepare($vendorQuery);
    
                    // Bind the vendor data to the statement
                    $vendorStmt->bindParam(':account_id', $account_id);  // Insert the correct account_id (from the account table)
                    $vendorStmt->bindParam(':vendor_name', $this->name);
                    $vendorStmt->bindParam(':vendor_username', $this->uname);
                    $vendorStmt->bindParam(':vendor_contact', $this->contact);
                    $vendorStmt->bindParam(':vendor_password', $hashedPassword);
                    $vendorStmt->bindParam(':vendor_address', $_POST['address']);
                    $vendorStmt->bindParam(':vendor_district', $this->district);
                    $vendorStmt->bindParam(':vendor_description', $_POST['description']);
                    $vendorStmt->bindParam(':vendor_email', $this->email);
    
                    // Execute the vendor insert
                    if ($vendorStmt->execute()) {
                        return true; // Vendor registration successful
                    } else {
                        // Output error if vendor insert fails
                        echo "Error inserting into vendor table: " . $vendorStmt->errorInfo()[2];
                        return false;
                    }
                } else {
                    echo "Seller details (address and description) are required.";
                    return false;  // If it's a seller but no address/description, return false
                }
            } else {
                return true; // For buyers, just insert into the account table
            }
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
                        $_SESSION['purpose'] = 'Buyer';  // Store 'Buyer' in session
                        header("Location: ../PHP/customerProfile.php"); 
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
        } else {
            echo "Error: " . $stmt->errorInfo()[2];
        }
    }
}

?>
