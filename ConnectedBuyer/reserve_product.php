<?php
require_once "../PHP/dbConnection.php";
session_start(); // Start the session to access buyer ID

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve reservation data from POST request
    $product_id = $_POST['product_id'] ?? null;
    $product_name = $_POST['product_name'] ?? null;
    $product_price = $_POST['product_price'] ?? null;
    $reserved_quantity = $_POST['quantity'] ?? null;

    // Get the buyer_id from the session (logged-in user)
    $buyer_id = $_SESSION['buyer_id'] ?? null;

    // Validate input data
    if (!$product_id || !$product_name || !$product_price || !$reserved_quantity || !$buyer_id) {
        echo "Error: Missing required fields. Debug info:";
        echo "product_id: " . ($product_id ? "set" : "not set") . ", ";
        echo "product_name: " . ($product_name ? "set" : "not set") . ", ";
        echo "product_price: " . ($product_price ? "set" : "not set") . ", ";
        echo "reserved_quantity: " . ($reserved_quantity ? "set" : "not set") . ", ";
        echo "buyer_id: " . ($buyer_id ? "set" : "not set");
        exit;
    }

    // Initialize database connection
    $database = new Database();
    $conn = $database->getConnection();

    // Calculate the total price (price * quantity)
    $total_price = $product_price * $reserved_quantity;

    // Begin a transaction
    $conn->beginTransaction();

    try {
        // Check if buyer exists in the buyer table
        $buyerCheckQuery = "SELECT buyer_id FROM buyer WHERE buyer_id = :buyer_id";
        $buyerCheckStmt = $conn->prepare($buyerCheckQuery);
        $buyerCheckStmt->bindParam(':buyer_id', $buyer_id, PDO::PARAM_INT);
        $buyerCheckStmt->execute();

        if ($buyerCheckStmt->rowCount() === 0) {
            throw new Exception("Invalid buyer ID: Buyer does not exist.");
        }

        // Fetch the vendor_id from the products table using product_id
        $vendorQuery = "SELECT vendor_id FROM products WHERE product_id = :product_id";
        $vendorStmt = $conn->prepare($vendorQuery);
        $vendorStmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $vendorStmt->execute();

        $vendorRow = $vendorStmt->fetch(PDO::FETCH_ASSOC);
        if (!$vendorRow) {
            throw new Exception("Invalid product ID: Product does not exist.");
        }
        $vendor_id = $vendorRow['vendor_id'];

        // Insert reservation into the `reservations` table, including vendor_id, total price, and status 'pending'
        $query = "INSERT INTO reservations (product_id, buyer_id, vendor_id, product_name, product_price, reserved_quantity, total_price, reserved_date, status)
                  VALUES (:product_id, :buyer_id, :vendor_id, :product_name, :product_price, :reserved_quantity, :total_price, NOW(), 'pending')";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':buyer_id', $buyer_id, PDO::PARAM_INT);
        $stmt->bindParam(':vendor_id', $vendor_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_name', $product_name, PDO::PARAM_STR);
        $stmt->bindParam(':product_price', $product_price, PDO::PARAM_STR);
        $stmt->bindParam(':reserved_quantity', $reserved_quantity, PDO::PARAM_INT);
        $stmt->bindParam(':total_price', $total_price, PDO::PARAM_STR); // Insert the total price
        $stmt->execute();

        // Update product quantity in the `products` table
        $updateQuery = "UPDATE products SET product_quantity = product_quantity - :reserved_quantity WHERE product_id = :product_id";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':reserved_quantity', $reserved_quantity, PDO::PARAM_INT);
        $updateStmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $updateStmt->execute();

        // Commit the transaction
        $conn->commit();
        header("Location: ../ConnectedBuyer/main.php"); // Redirect after reservation
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $conn->rollBack();
        echo "Error: Unable to reserve the product. " . $e->getMessage();
    }
} else {
    echo "Invalid request method.";
}
?>
