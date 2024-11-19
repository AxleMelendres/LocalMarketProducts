<?php
class Vendor {
    private $conn;
    private $tbl_name = "vendor"; 

    public $vendor_id;
    public $vendor_name;
    public $vendor_uname;
    public $vendor_description;
    public $vendor_email;
    public $vendor_contact;
    public $vendor_address;
    public $vendor_district;
    public $vendor_image;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getVendor($vendor_uname) {
        $query = "SELECT vendor_id, vendor_name, vendor_username, vendor_description, vendor_email, 
                         vendor_contact, vendor_address, vendor_district, vendor_image
                  FROM " . $this->tbl_name . " 
                  WHERE vendor_username = :vendor_uname LIMIT 1";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':vendor_uname', $vendor_uname);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }
}
?>
