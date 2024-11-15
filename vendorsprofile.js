

const addProductButton = document.getElementById('add-products');
const editProductButton = document.getElementById('edit-products');
const deleteProductButton = document.getElementById('delete-products');

addProductButton.addEventListener('click', function() {
    window.location.href = "add_product.html";
});

editProductButton.addEventListener('click', function() {
    window.location.href = "edit_product.html"; 
});

deleteProductButton.addEventListener('click', function() {
    window.location.href = "delete_product.html"; 
});

