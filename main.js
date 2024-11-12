let sidebarMenu = document.querySelector('.sidebarMenu');
        let slider = document.querySelector('.sidebar');
        let closeSidebar = document.querySelector('.closeSidebar');

        sidebarMenu.onclick = function() {
            slider.classList.add('active');
        }
        closeSidebar.onclick = function() {
            slider.classList.remove('active');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const productItems = document.querySelectorAll('.product-item');
            const productDetails = document.querySelectorAll('.product-detail');
        
            productItems.forEach(item => {
                item.addEventListener('click', () => {
                    const productId = item.getAttribute('data-product-id');
                    const productDetail = document.getElementById(`product-detail-${productId}`);
                    productDetail.style.display = 'block';
                });
            });
        
            productDetails.forEach(detail => {
                detail.addEventListener('click', (e) => {
                    if (e.target.classList.contains('reserve-btn')) {
                        alert('Reserved!');
                    } else {
                        detail.style.display = 'none';
                    }
                });
            });
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            const districtButton = document.getElementById('district-button');
            const dropdownContent = document.querySelector('.dropdown-content');
        
            districtButton.addEventListener('click', function(e) {
                e.preventDefault();
                dropdownContent.classList.toggle('show');
            });
        
            window.addEventListener('click', function(e) {
                if (!e.target.matches('#district-button')) {
                    if (dropdownContent.classList.contains('show')) {
                        dropdownContent.classList.remove('show');
                    }
                }
            });
        });