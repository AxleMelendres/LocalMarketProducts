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

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->tbl_name . " (product_name, product_image, product_quantity, product_price, product_description, product_category) 
                  VALUES (:product_name, :product_image, :product_quantity, :product_price, :product_description, :product_category)";
                          
                  $stmt = $this->conn->prepare($query);
    
        $stmt->bindParam(':product_name', $this->product_name);
        $stmt->bindParam(':product_image', $this->product_image);
        $stmt->bindParam(':product_quantity', $this->product_quantity);
        $stmt->bindParam(':product_price', $this->product_price);
        $stmt->bindParam(':product_description', $this->product_description);
        $stmt->bindParam(':product_category', $this->product_category);
    
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


                echo "<form method='POST' action='reserve_product.php' class='reserve-form'>";
                echo "<input type='hidden' name='product_id' value='" . htmlspecialchars($row['product_id'], ENT_QUOTES, 'UTF-8') . "'>";
                echo "<button type='submit' class='reserve-button'>Reserve</button>";
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
    
        // Query to fetch product details
        $query = "SELECT * FROM products WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);
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
    
                    // Add buttons using a form to send POST requests
                    echo "<form method='POST' action='../DISPLAY/view.php' class='view-form'>";
                    echo "<input type='hidden' name='product_id' value='" . htmlspecialchars($row['product_id'], ENT_QUOTES, 'UTF-8') . "'>";
                    echo "<button type='submit' class='view-button'>View</button>";
                    echo "</form>";

                    echo "<form method='POST' action='reserve_product.php' class='reserve-form'>";
                    echo "<input type='hidden' name='product_id' value='" . htmlspecialchars($row['product_id'], ENT_QUOTES, 'UTF-8') . "'>";
                    echo "<button type='submit' class='reserve-button'>Reserve</button>";
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
    
}
?>
