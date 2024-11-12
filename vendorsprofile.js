
const addProductButton = document.getElementById('add-products');
const editProductButton = document.getElementById('edit-products');

addProductButton.addEventListener('click', function() {
    window.location.href = "add_product.html";
});

editProductButton.addEventListener('click', function() {
    window.location.href = "edit_product.html"; 
});
