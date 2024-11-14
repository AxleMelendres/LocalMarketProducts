document.getElementById('customer-registration').onclick = function() {
    document.getElementById('registration-form').style.display = 'block'; 
    document.getElementById('registration-title').textContent = 'Customer Registration'; 
    document.getElementById('user_type').value = 'customer';
    hideRegistrationOptions(); 
};

document.getElementById('seller-registration').onclick = function() {
    document.getElementById('registration-form').style.display = 'block'; 
    document.getElementById('registration-title').textContent = 'Seller Registration'; 
    document.getElementById('user_type').value = 'seller';
    hideRegistrationOptions(); 
};

function hideRegistrationOptions() {
    document.querySelector('.registration-options').style.display = 'none'; 
};