<?php
class Product {
    private $conn;
    private $tbl_name = "products"; 

    public $id;
    public $product_name;
    public $product_image;
    public $product_quantity;
    public $product_price;
    public $product_description;
    public $product_category; // Add this line to handle the category

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->tbl_name . " (product_name, product_image, product_quantity, product_price, product_description, product_category) 
                  VALUES (:product_name, :product_image, :product_quantity, :product_price, :product_description, :product_category)";
    
        $stmt = $this->conn->prepare($query);
    
        // Bind all parameters
        $stmt->bindParam(':product_name', $this->product_name);
        $stmt->bindParam(':product_image', $this->product_image);
        $stmt->bindParam(':product_quantity', $this->product_quantity);
        $stmt->bindParam(':product_price', $this->product_price);
        $stmt->bindParam(':product_description', $this->product_description);
        $stmt->bindParam(':product_category', $this->product_category); // Bind the category field
    
        if ($stmt->execute()) {
            return true;
        } else {
            echo "Error: " . $stmt->errorInfo()[2];
            return false;
        }
    }
}

?>
