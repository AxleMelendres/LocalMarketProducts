<?php
class Product {
    private $conn;
    private $tbl_name = "products"; 

    public $product_id;
    public $product_name;
    public $product_image;
    public $product_quantity;
    public $product_price;
    public $product_description;
    public $product_category; 
    public $vendor_id; 

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Method to create a new product
    public function create() {
        $query = "INSERT INTO " . $this->tbl_name . " (product_name, product_image, product_quantity, product_price, product_description, product_category, vendor_id) 
                  VALUES (:product_name, :product_image, :product_quantity, :product_price, :product_description, :product_category, :vendor_id)";
        
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':product_name', $this->product_name);
        $stmt->bindParam(':product_image', $this->product_image);
        $stmt->bindParam(':product_quantity', $this->product_quantity);
        $stmt->bindParam(':product_price', $this->product_price);
        $stmt->bindParam(':product_description', $this->product_description);
        $stmt->bindParam(':product_category', $this->product_category);
        $stmt->bindParam(':vendor_id', $this->vendor_id); // Bind the vendor_id for the product
    
        if ($stmt->execute()) {
            return true;
        } else {
            echo "Error: " . $stmt->errorInfo()[2];
            return false;
        }
    }

    // Method to get products by vendor_id
    public function getProductsByVendor($vendor_id) {
        $query = "SELECT product_id, product_name, product_image, product_quantity, product_price, product_description, product_category
                  FROM " . $this->tbl_name . " 
                  WHERE vendor_id = :vendor_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':vendor_id', $vendor_id); // Bind vendor_id

        $stmt->execute();

        // Check if any products are found
        if ($stmt->rowCount() > 0) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return null; // No products found for the vendor
        }
    }

    public function delete($product_id) {
        $query = "DELETE FROM " . $this->tbl_name . " WHERE product_id = :product_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getProductDetails($product_id, $vendor_id) {
        $query = "SELECT product_id, product_name, product_image, product_quantity, product_price, product_description, product_category
                  FROM " . $this->tbl_name . " 
                  WHERE product_id = :product_id AND vendor_id = :vendor_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':vendor_id', $vendor_id);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    }

    // Update product details
    public function update($product_id) {
        $query = "UPDATE " . $this->tbl_name . " 
                  SET product_name = :product_name, 
                      product_image = :product_image, 
                      product_quantity = :product_quantity, 
                      product_price = :product_price, 
                      product_description = :product_description, 
                      product_category = :product_category
                  WHERE product_id = :product_id";

        $stmt = $this->conn->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':product_name', $this->product_name);
        $stmt->bindParam(':product_image', $this->product_image);
        $stmt->bindParam(':product_quantity', $this->product_quantity);
        $stmt->bindParam(':product_price', $this->product_price);
        $stmt->bindParam(':product_description', $this->product_description);
        $stmt->bindParam(':product_category', $this->product_category);
        $stmt->bindParam(':product_id', $product_id);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}


?>
