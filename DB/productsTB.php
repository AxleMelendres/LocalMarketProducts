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

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->tbl_name . " (product_name, product_image, product_quantity, product_price, product_description) 
                  VALUES (:product_name, :product_image, :product_quantity, :product_price, :product_description)";
    
        $stmt = $this->conn->prepare($query);
    
        $stmt->bindParam(':product_name', $this->product_name);
        $stmt->bindParam(':product_image', $this->product_image);
        $stmt->bindParam(':product_quantity', $this->product_quantity);
        $stmt->bindParam(':product_price', $this->product_price);
        $stmt->bindParam(':product_description', $this->product_description);
    
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
                echo "<p>$" . htmlspecialchars($row['product_price'], ENT_QUOTES, 'UTF-8') . "</p>";
                
                // Add buttons with safe URLs
                echo "<a href='../DISPLAY/view.php?product_id=" . urlencode($row['product_id']) . "' class='view-button'>View</a>";
                echo "<a href='reserve_product.php?product_id=" . urlencode($row['product_id']) . "' class='reserve-button'>Reserve</a>";

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

    public function view(){
        $product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

        $query = "SELECT * FROM products WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);;
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            echo "Product not found.";
            exit;
        }
        return $product;
    }

    public function search($queryParams) {
        $query = $queryParams['query'] ?? '';
        $district = $queryParams['district'] ?? '';
        $category = $queryParams['category'] ?? '';
    
        // SQL query with placeholders to prevent SQL injection
        $sql = "SELECT product_id, product_name, product_price, product_image FROM " . $this->tbl_name . " WHERE product_name LIKE :query";
    
        if (!empty($district)) {
            $sql .= " AND district = :district";
        }
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
    
                    // Add buttons with safe URLs
                    echo "<a href='../DISPLAY/view.php?product_id=" . urlencode($row['product_id']) . "' class='view-button'>View</a>";
                    echo "<a href='reserve_product.php?product_id=" . urlencode($row['product_id']) . "' class='reserve-button'>Reserve</a>";
    
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
    
}
?>
