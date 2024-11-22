const addProductButton = document.getElementById('add-products');
const editProductButton = document.getElementById('edit-products');
const deleteProductButton = document.getElementById('delete-products');
const editProfileButton = document.getElementById('edit-profile');  // Fixed typo

addProductButton.addEventListener('click', function() {
    window.location.href = "../HTML/add_product.html";  
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
