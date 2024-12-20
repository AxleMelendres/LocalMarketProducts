document.addEventListener('DOMContentLoaded', () => {
    const backButton = document.getElementById('back-button');
    if (backButton) {
        backButton.addEventListener('click', function () {
            window.history.back(); // Go back to the previous page in history
        });
    }
});



    // Product Selection: Highlight selected product
    let selectedProductId = null;

    // Ensure that product elements are loaded before adding event listeners
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

    // Product Selection: Enable checkbox selection and toggle the delete button visibility
    let selectedProductIds = [];

    const productCheckboxes = document.querySelectorAll('.product-checkbox');
    const removeBtn = document.getElementById('remove-product-btn');

    // Add click event listener to checkboxes
    productCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const productId = this.getAttribute('data-product-id');

            // Add or remove product ID from the selectedProductIds array
            if (this.checked) {
                selectedProductIds.push(productId);
            } else {
                selectedProductIds = selectedProductIds.filter(id => id !== productId);
            }

            // Show or hide the "Delete" button depending on whether any products are selected
            if (selectedProductIds.length > 0) {
                removeBtn.style.display = 'block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    });

    // Add click event listener for the delete button
    removeBtn.addEventListener('click', function () {
        if (selectedProductIds.length > 0) {
            // Check all the selected checkboxes and ensure they are part of the form
            const form = document.querySelector('form');
            // Add the selected product IDs to the form (hidden input fields)
            selectedProductIds.forEach(id => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'product_ids[]';
                hiddenInput.value = id;
                form.appendChild(hiddenInput);
            });
            // Submit the form
            form.submit();
        } else {
            alert('Please select at least one product to delete.');
        }
    });

