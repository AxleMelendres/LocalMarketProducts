<?php
session_start();

// Ensure the buyer is logged in
if (!isset($_SESSION['buyer_id'])) {
    echo "Error: Please log in to delete a reservation.";
    exit;
}

require_once "../PHP/dbConnection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get reservation_id from POST request
    $reservation_id = $_POST['reservation_id'] ?? null;

    if (!$reservation_id) {
        echo "Error: Missing reservation ID.";
        exit;
    }

    // Initialize database connection
    $database = new Database();
    $conn = $database->getConnection();

    try {
        // Begin a transaction
        $conn->beginTransaction();

        // Fetch the reservation data to get the reserved quantity and product_id
        $reservationQuery = "SELECT product_id, reserved_quantity FROM reservations WHERE reservation_id = :reservation_id";
        $reservationStmt = $conn->prepare($reservationQuery);
        $reservationStmt->bindParam(':reservation_id', $reservation_id, PDO::PARAM_INT);
        $reservationStmt->execute();

        $reservation = $reservationStmt->fetch(PDO::FETCH_ASSOC);

        if (!$reservation) {
            throw new Exception("Reservation not found.");
        }

        // Restore the original product quantity in the products table
        $updateProductQuery = "UPDATE products SET product_quantity = product_quantity + :reserved_quantity WHERE product_id = :product_id";
        $updateProductStmt = $conn->prepare($updateProductQuery);
        $updateProductStmt->bindParam(':reserved_quantity', $reservation['reserved_quantity'], PDO::PARAM_INT);
        $updateProductStmt->bindParam(':product_id', $reservation['product_id'], PDO::PARAM_INT);
        $updateProductStmt->execute();

        // Delete the reservation from the reservations table
        $deleteQuery = "DELETE FROM reservations WHERE reservation_id = :reservation_id";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bindParam(':reservation_id', $reservation_id, PDO::PARAM_INT);
        $deleteStmt->execute();

        // Commit the transaction
        $conn->commit();
        echo ""; // Success, return an empty response
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request method.";
}
?>
