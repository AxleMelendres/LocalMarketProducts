<?php
// update_status.php
require_once '../PHP/dbConnection.php';
require_once '../PHP/vendorConnection.php';
require_once '../DB/productsTB.php';

// Start session to access vendor_id
session_start();

// Function to update reservation status
function updateReservationStatus($reservation_id, $new_status, $vendor_id) {
    $database = new Database();
    $conn = $database->getConnection();

    try {
        // First, verify that the reservation belongs to the logged-in vendor
        $verify_query = "SELECT r.reservation_id 
                         FROM reservations r
                         JOIN products p ON r.product_id = p.product_id
                         WHERE r.reservation_id = :reservation_id AND p.vendor_id = :vendor_id";
        $verify_stmt = $conn->prepare($verify_query);
        $verify_stmt->bindParam(':reservation_id', $reservation_id, PDO::PARAM_INT);
        $verify_stmt->bindParam(':vendor_id', $vendor_id, PDO::PARAM_INT);
        $verify_stmt->execute();

        if ($verify_stmt->rowCount() === 0) {
            return "Error: Unauthorized access or invalid reservation.";
        }

        // If verified, proceed with the update
        $query = "UPDATE reservations SET status = :new_status WHERE reservation_id = :reservation_id";
        $stmt = $conn->prepare($query);

        $stmt->bindParam(':new_status', $new_status, PDO::PARAM_STR);
        $stmt->bindParam(':reservation_id', $reservation_id, PDO::PARAM_INT);

        $stmt->execute();

        return "Success";
    } catch (Exception $e) {
        error_log("Error updating status: " . $e->getMessage());
        return "Error: " . $e->getMessage();
    }
}

// Handle the POST request to update the status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservation_id = $_POST['reservation_id'] ?? null;
    $new_status = $_POST['status'] ?? null;
    $vendor_id = $_SESSION['vendor_id'] ?? null;

    if ($reservation_id && $new_status && $vendor_id) {
        $result = updateReservationStatus($reservation_id, $new_status, $vendor_id);
        echo $result;
    } else {
        echo "Error: Missing reservation ID, status, or vendor ID.";
    }
} else {
    echo "Error: Invalid request method.";
}