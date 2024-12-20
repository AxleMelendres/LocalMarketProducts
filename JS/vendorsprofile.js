const addProductButton = document.getElementById('add-products');
const editProductButton = document.getElementById('edit-products');
const deleteProductButton = document.getElementById('delete-products');
const editProfileButton = document.getElementById('edit-profile');  // Fixed typo
const reservedProducts = document.getElementById('reserved-products');  // Fixed typo

addProductButton.addEventListener('click', function() {
    window.location.href = "../PHP/add_product.php";  
});

editProductButton.addEventListener('click', function() {
    window.location.href = "../PHP/edit_product.php";  
});

deleteProductButton.addEventListener('click', function() {
    window.location.href = "../PHP/delete_product.php"; 
});

editProfileButton.addEventListener('click', function() {
    window.location.href = "../PHP/editProfile.php"; 
});  

reservedProducts.addEventListener('click', function() {
    window.location.href = "../PHP/vendorReservedProducts.php"; 
});  
