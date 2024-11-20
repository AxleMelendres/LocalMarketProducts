const backButton = document.getElementById('back-button');
backButton.addEventListener('click', function() {
    window.history.back(); // Go back to previous page
});

document.addEventListener('DOMContentLoaded', () => {
    let selectedProductId = null;

    // Handle product click
    const productElements = document.querySelectorAll('.product-item');
    productElements.forEach(product => {
        product.addEventListener('click', () => {
            // Toggle selection of the product
            if (selectedProductId === product.getAttribute('data-product-id')) {
                product.classList.remove('selected');
                selectedProductId = null;
            } else {
                // Deselect previously selected product
                document.querySelectorAll('.product-item').forEach(item => item.classList.remove('selected'));
                // Select the clicked product
                product.classList.add('selected');
                selectedProductId = product.getAttribute('data-product-id');
            }
        });
    });

    // Handle remove button click
    document.getElementById('remove-product-btn').addEventListener('click', () => {
        if (selectedProductId !== null) {
            if (confirm('Are you sure you want to delete this product?')) {
                // Create a form data object to send the product_id
                const formData = new FormData();
                formData.append('product_id', selectedProductId);

                // Send the product ID to the server using a POST request
                fetch('delete_product.php', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.text())  // Handle response as text
                .then(data => {
                    console.log(data);  // Log the response data for debugging

                    if (data === 'success') {  // Checking if deletion was successful
                        alert('Product deleted successfully.');

                        // Remove the product from the UI
                        document.querySelector(`.product-item[data-product-id="${selectedProductId}"]`).remove();

                        // Redirect to vendor profile page after successful deletion
                        window.location.href = 'vendorsprofile.php';  // Use the correct relative path
                    } else if (data === 'error') {
                        alert('Error deleting product.');
                    } else {
                        alert('Unexpected server response.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting product.');
                });
            }
        } else {
            alert('Please select a product to delete.');
        }
    });
});



// Wait for the document to fully load
document.addEventListener('DOMContentLoaded', function () {
// Select all product items
const productItems = document.querySelectorAll('.product-item');

// Loop through each product item and add a click event listener
productItems.forEach(item => {
    item.addEventListener('click', function () {
        // Get the id of the selected product from the data-id attribute
        const selectedProductId = item.getAttribute('data-id');

        // Loop through all product items and hide the ones that don't match the selected product
        productItems.forEach(product => {
            if (product.getAttribute('data-id') !== selectedProductId) {
                product.style.display = 'none'; // Hide the other products
            }
        });

        // Optionally, you can focus on the product details section after hiding others
        // Scroll to the product details or display the editing form
        // Example:
        // document.querySelector('.edit-form').scrollIntoView({ behavior: 'smooth' });
    });
});
});

document.addEventListener('DOMContentLoaded', function () {
    // Check if a product is being edited (i.e., product_id exists in the URL)
    const isEditing = window.location.search.indexOf('product_id') !== -1;
    
    if (isEditing) {
        // Hide the product list when editing
        const productList = document.getElementById('product-list');
        if (productList) {
            productList.style.display = 'none';
        }
    }
});


