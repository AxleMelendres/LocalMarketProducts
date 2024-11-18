const addProductButton = document.getElementById('add-products');
const editProductButton = document.getElementById('edit-products');
const deleteProductButton = document.getElementById('delete-products');
const editProfileButton = document.getElementById('edit-profile');  // Fixed typo

addProductButton.addEventListener('click', function() {
    window.location.href = "../HTML/add_product.html";  
});

editProductButton.addEventListener('click', function() {
    window.location.href = "../HTML/edit_product.html";  
});

deleteProductButton.addEventListener('click', function() {
    window.location.href = "../HTML/delete_product.html"; 
});

editProfileButton.addEventListener('click', function() {
    window.location.href = "../PHP/editProfile.php"; 
});  
