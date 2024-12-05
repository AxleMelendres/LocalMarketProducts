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
    

    public function displayAll(){
        $query = "SELECT product_id, product_name, product_price, product_image FROM " . $this->tbl_name;

        // Prepare the statement
        $stmt = $this->conn->prepare($query);

        // Execute the query
        if ($stmt->execute()) {
        // Fetch all products
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<div class='product-boxes'>";

        if (count($products) > 0) { // Check if products exist
            foreach ($products as $row) {
                echo "<div class='product-box'>";

                // Handle image path
                $imagePath = "../uploads/" . htmlspecialchars($row['product_image'], ENT_QUOTES, 'UTF-8');
                
                // Check if image file exists
                if (!file_exists($imagePath)) {
                    $imagePath = "../uploads/default.png"; // Default image fallback
                }

                echo "<img src='{$imagePath}' alt='" . htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8') . "' class='product-image'>";
                
                // Output product name and price
                echo "<h3>" . htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8') . "</h3>";
                echo "<p>₱" . htmlspecialchars($row['product_price'], ENT_QUOTES, 'UTF-8') . "</p>";
                
                // Determine the full script path
                $fullPath = $_SERVER['SCRIPT_NAME']; // Full path to the script

                // Normalize the path to identify the folder structure
                if (strpos($fullPath, '/DISPLAY/main.php') !== false) {
                    $actionUrl = '../DISPLAY/view.php';
                } elseif (strpos($fullPath, '/ConnectedVendor/main.php') !== false) {
                    $actionUrl = '../ConnectedVendor/view.php';
                } elseif (strpos($fullPath, '/ConnectedBuyer/main.php') !== false) {
                    $actionUrl = '../ConnectedBuyer/view.php';
                } else {
                    $actionUrl = '../DISPLAY/view.php'; // Default redirection
                }

                // Generate the form dynamically
                echo "<form method='POST' action='" . htmlspecialchars($actionUrl, ENT_QUOTES, 'UTF-8') . "' class='view-form'>";
                echo "<input type='hidden' name='product_id' value='" . htmlspecialchars($row['product_id'], ENT_QUOTES, 'UTF-8') . "'>";
                echo "<button type='submit' class='view-button'>View</button>";
                echo "</form>";

                echo "</div>";
            }
        } else {
            echo "<p>No products available.</p>";
        }

        echo "</div>";
        } else {
            // Handle execution error
            echo "Error: " . $stmt->errorInfo()[2];
        }
    }

    public function view() {
        // Get product_id from POST or GET
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : (isset($_GET['product_id']) ? intval($_GET['product_id']) : 0);
    
        if ($product_id === 0) {
            echo "Error: No Product ID provided.";
            exit;
        }
    
        // Query to fetch product and vendor details
        $query = "SELECT 
                    p.*, 
                    v.vendor_image, 
                    v.vendor_username 
                  FROM 
                    products AS p 
                  INNER JOIN 
                    vendor AS v 
                  ON 
                    p.vendor_id = v.vendor_id 
                  WHERE 
                    p.product_id = :product_id";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    
        try {
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$product) {
                echo "Error: Product not found.";
                exit;
            }
    
            return $product;
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
            exit;
        }
    }
    
    
    public function search($queryParams) {
        $query = $queryParams['query'] ?? '';
        $category = $queryParams['category'] ?? '';
    
        // SQL query with placeholders to prevent SQL injection
        $sql = "SELECT product_id, product_name, product_price, product_image FROM " . $this->tbl_name . " WHERE product_name LIKE :query";
    
        if (!empty($category)) {
            $sql .= " AND product_category = :category";
        }
    
        // Prepare the statement
        $stmt = $this->conn->prepare($sql);
    
        // Bind parameters
        $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
        if (!empty($district)) {
            $stmt->bindValue(':district', $district, PDO::PARAM_STR);
        }
        if (!empty($category)) {
            $stmt->bindValue(':category', $category, PDO::PARAM_STR);
        }
    
        // Execute the query
        if ($stmt->execute()) {
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            echo "<div class='product-boxes'>";
    
            if (count($products) > 0) {
                foreach ($products as $row) {
                    echo "<div class='product-box'>";
    
                    // Handle image path
                    $imagePath = "../uploads/" . htmlspecialchars($row['product_image'], ENT_QUOTES, 'UTF-8');
    
                    // Check if image file exists
                    if (!file_exists($imagePath)) {
                        $imagePath = "../uploads/default.png"; // Default image fallback
                    }
    
                    echo "<img src='{$imagePath}' alt='" . htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8') . "' class='product-image'>";
    
                    // Output product name and price
                    echo "<h3>" . htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8') . "</h3>";
                    echo "<p>$" . htmlspecialchars($row['product_price'], ENT_QUOTES, 'UTF-8') . "</p>";
    
                    // Determine the full script path
                $fullPath = $_SERVER['SCRIPT_NAME']; // Full path to the script

                // Normalize the path to identify the folder structure
                if (strpos($fullPath, '/DISPLAY/search.php') !== false) {
                    $actionUrl = '../DISPLAY/view.php';
                } elseif (strpos($fullPath, '/ConnectedVendor/search.php') !== false) {
                    $actionUrl = '../ConnectedVendor/view.php';
                } elseif (strpos($fullPath, '/ConnectedBuyer/search.php') !== false) {
                    $actionUrl = '../ConnectedBuyer/view.php';
                } else {
                    $actionUrl = '../DISPLAY/view.php'; // Default redirection
                }

                // Generate the form dynamically
                echo "<form method='POST' action='" . htmlspecialchars($actionUrl, ENT_QUOTES, 'UTF-8') . "' class='view-form'>";
                echo "<input type='hidden' name='product_id' value='" . htmlspecialchars($row['product_id'], ENT_QUOTES, 'UTF-8') . "'>";
                echo "<button type='submit' class='view-button'>View</button>";
                echo "</form>";

                echo "</div>";
                }
            } else {
                echo "<p>No products found.</p>";
            }
    
            echo "</div>";
        } else {
            echo "Error: " . $stmt->errorInfo()[2];
        }
    }
    

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

    public function getProductDetails($product_id) {
        $query = "SELECT product_id, product_name, product_image, product_quantity, product_price, product_description, product_category
                FROM " . $this->tbl_name . " 
                WHERE product_id = :product_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return null; // Return null if no product is found
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

class Reservation {
    private $conn;

    // Constructor with database connection
    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // Get reserved products for a specific vendor
    public function getReservedProductsByVendor($vendor_id) {
        // SQL query to get reserved products by vendor_id, including images and calculating pickup_date
        $query = "SELECT 
                      r.reservation_id,
                      p.product_name, 
                      p.product_image, 
                      r.reserved_quantity, 
                      r.reserved_date, 
                      b.buyer_name, 
                      b.buyer_image,
                      DATE_ADD(r.reserved_date, INTERVAL 3 DAY) AS pickup_date,  -- Adding 3 days to reserved_date
                      r.status,
                      CASE
                          WHEN CURDATE() >= DATE_ADD(r.reserved_date, INTERVAL 3 DAY) AND r.status != 'Received' THEN 'Cancelled'
                          ELSE r.status
                      END AS updated_status
                  FROM reservations r
                  JOIN products p ON r.product_id = p.product_id
                  JOIN buyer b ON r.buyer_id = b.buyer_id
                  WHERE p.vendor_id = :vendor_id
                  ORDER BY r.reserved_date DESC"; // Sorted by reservation date
        
        // Prepare the statement
        $stmt = $this->conn->prepare($query);
        
        // Bind the vendor_id parameter
        $stmt->bindParam(':vendor_id', $vendor_id, PDO::PARAM_INT);
        
        // Execute the query
        $stmt->execute();
        
        // Fetch the results and update the status field
        $reservedProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Iterate over the products and update the status in the array
        foreach ($reservedProducts as &$product) {
            // Update the status field based on the query logic
            $product['status'] = $product['updated_status'];
        }
    
        // Return the results as an associative array
        return $reservedProducts;
    }
    
}

?>
