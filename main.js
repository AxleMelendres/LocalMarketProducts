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
        