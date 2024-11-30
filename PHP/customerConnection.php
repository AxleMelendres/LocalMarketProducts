<?php
class Customer {
    private $conn;
    private $tbl_name = "buyer";

    public $customer_id;
    public $full_name;
    public $username;
    public $email;
    public $contact_number;
    public $purpose;
    public $district;
    public $buyer_image;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getCustomerDetails($username) {
        // Corrected query
        $query = "SELECT 
                    a.full_name AS full_name, 
                    a.Username, 
                    a.Email, 
                    a.`Contact Number`, 
                    a.Purpose, 
                    a.District, 
                    b.buyer_image 
                  FROM account a 
                  LEFT JOIN buyer b ON a.account_id = b.account_id 
                  WHERE a.Username = :username";
    
        // Prepare the statement
        $stmt = $this->conn->prepare($query);
    
        // Bind the parameter
        $stmt->bindParam(':username', $username);
    
        // Execute the query
        $stmt->execute();
    
        // Return the result
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    
}
?>
