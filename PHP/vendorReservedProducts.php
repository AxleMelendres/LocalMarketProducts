<?php
// Start session and include necessary files
require_once '../PHP/dbConnection.php';
require_once '../PHP/vendorConnection.php';
require_once '../DB/productsTB.php';

$database = new Database();
$conn = $database->getConnection();

session_start();

// Get vendor_id from the session (assuming vendor logs in and their ID is stored in session)
$vendor_id = isset($_SESSION['vendor_id']) ? $_SESSION['vendor_id'] : null;

if ($vendor_id) {
    // Fetch reserved products for the logged-in vendor
    $reservation = new Reservation($conn);
    $reservedProducts = $reservation->getReservedProductsByVendor($vendor_id);
} else {
    // Handle the case where vendor_id is not available
    echo "Vendor ID is missing or invalid.";
    $reservedProducts = [];
}


$conn = null; // Close the connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserved Products</title>
    <link rel="stylesheet" href="../CSS/vendorReservedProducts.css">
    <script src="../JS/product.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body>
<h2>Reserved Products</h2>
<div class="container">
    <div class="reserved-products">
        <?php if (isset($reservedProducts) && !empty($reservedProducts)): ?>
            <table class="reserved-products-table">
                <thead>
                    <tr>
                        <th>Product Image</th>
                        <th>Product</th>
                        <th>Buyer Image</th>
                        <th>Buyer</th>
                        <th>Quantity</th>
                        <th>Date Reserved</th>
                        <th>Pickup Duration</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservedProducts as $reservation): ?>
                        <tr data-reservation-id="<?php echo $reservation['reservation_id']; ?>">
                            <td>
                                <img src="<?php echo htmlspecialchars($reservation['product_image']); ?>" alt="Product Image" style="width: 100px; height: auto;">
                            </td>
                            <td><?php echo htmlspecialchars($reservation['product_name']); ?></td>
                            <td>
                                <img src="<?php echo htmlspecialchars($reservation['buyer_image']); ?>" alt="Buyer Image" style="width: 50px; height: auto; border-radius: 50%;">
                            </td>
                            <td><?php echo htmlspecialchars($reservation['buyer_name']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['reserved_quantity']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['reserved_date']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['pickup_date']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['status']); ?></td>
                            <td>
                                <?php if ($reservation['status'] !== 'Received' && $reservation['status'] !== 'Cancelled'): ?>
                                <button class="btn-received-product" onclick="markAsReceived(<?php echo $reservation['reservation_id']; ?>)">Received Product</button>
                                <?php endif; ?>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        <?php else: ?>
            <p>No products have been reserved for your listings.</p>
        <?php endif; ?>
    </div>
</div>
<button id="back-button" class="btn">Back</button>

<script>
function markAsReceived(reservationId) {
    fetch('../PHP/update_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `reservation_id=${reservationId}&status=Received`
    })
    .then(response => response.text())
    .then(data => {
        console.log("Response from server:", data);
        if (data.trim() === "Success") {
            Swal.fire({
                title: 'Success!',
                text: 'Product marked as received!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                // Optionally remove the button if the status is updated
                const row = document.querySelector(`tr[data-reservation-id="${reservationId}"]`);
                if (row) {
                    const buttonCell = row.querySelector('.btn-received-product');
                    if (buttonCell) {
                        buttonCell.remove(); // Remove the button
                    }
                }

                location.reload(); // Reload to reflect the updated status in the table
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: 'Error marking product as received: ' + data,
                icon: 'error',
                confirmButtonText: 'Try Again'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Error sending request to update status!',
            icon: 'error',
            confirmButtonText: 'Close'
        });
    });
}



function saveStatus(reservationId) {
    var newStatus = document.getElementById('status-dropdown').value;
    
    fetch('../PHP/update_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `reservation_id=${reservationId}&status=${newStatus}`
    })
    .then(response => response.text())
    .then(data => {
        console.log("Response from server:", data);
        if (data.trim() === "Success") {
            Swal.fire({
                title: 'Success!',
                text: 'Status updated successfully!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: 'Error updating status: ' + data,
                icon: 'error',
                confirmButtonText: 'Try Again'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Error sending request to update status!',
            icon: 'error',
            confirmButtonText: 'Close'
        });
    });

    document.getElementById('status-modal').remove();
}

</script>
</body>
</html>