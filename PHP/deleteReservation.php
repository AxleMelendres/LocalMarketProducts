<?php
require_once "../PHP/dbConnection.php";
session_start();

// Check if the request method is POST and the reservation_id is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_id'])) {
    $reservation_id = $_POST['reservation_id'];

    // Validate buyer session
    if (!isset($_SESSION['buyer_id'])) {
        http_response_code(403); // Forbidden
        echo "Error: You need to log in to delete a reservation.";
        exit;
    }

    $buyer_id = $_SESSION['buyer_id']; // Get the logged-in buyer ID

    // Initialize database connection
    $database = new Database();
    $conn = $database->getConnection();

    // Begin a transaction
    $conn->beginTransaction();

    try {
        // Check if the reservation exists and belongs to the logged-in buyer
        $query = "SELECT * FROM reservations WHERE reservation_id = :reservation_id AND buyer_id = :buyer_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':reservation_id', $reservation_id, PDO::PARAM_INT);
        $stmt->bindParam(':buyer_id', $buyer_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw new Exception("Reservation not found or you are not authorized to delete this reservation.");
        }

        // Delete the reservation
        $deleteQuery = "DELETE FROM reservations WHERE reservation_id = :reservation_id";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bindParam(':reservation_id', $reservation_id, PDO::PARAM_INT);
        $deleteStmt->execute();

        // Commit the transaction
        $conn->commit();

        // Return success (empty response or success message)
        http_response_code(200); // OK
        exit; // Exit without outputting anything else
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollBack();
        http_response_code(500); // Internal Server Error
        echo "Error: Unable to delete the reservation. " . $e->getMessage();
        exit;
    }
} else {
    http_response_code(400); // Bad Request
    echo "Invalid request.";
    exit;
}
