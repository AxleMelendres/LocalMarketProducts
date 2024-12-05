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
                        <tr>
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
                                <button class="btn-edit-status" onclick="editStatus(<?php echo $reservation['reservation_id']; ?>)">Edit Status</button>
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
function editStatus(reservationId) {
    var dropdownHtml = `
        <label for="status">Select Status:</label>
        <select id="status-dropdown">
            <option value="Pending">Pending</option>
            <option value="Received">Received</option>
            <option value="Cancelled">Cancelled</option>
        </select>
        <button onclick="saveStatus(${reservationId})">Save</button>
    `;
    
    var modalContent = document.createElement('div');
    modalContent.innerHTML = dropdownHtml;
    
    var modal = document.createElement('div');
    modal.id = 'status-modal';
    modal.className = 'status-modal';
    modal.appendChild(modalContent);
    
    document.body.appendChild(modal);
    
    var style = document.createElement('style');
    style.innerHTML = `
        .status-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .status-modal div {
            background: white;
            padding: 20px;
            border-radius: 8px;
        }
    `;
    document.head.appendChild(style);
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
            alert("Status updated successfully!");
            location.reload();
        } else {
            alert("Error updating status: " + data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("Error sending request to update status!");
    });

    document.getElementById('status-modal').remove();
}
</script>
</body>
</html>