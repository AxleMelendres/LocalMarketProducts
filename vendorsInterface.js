let sidebarMenu = document.querySelector('.sidebarMenu');
        let slider = document.querySelector('.sidebar');
        let closeSidebar = document.querySelector('.closeSidebar');

        sidebarMenu.onclick = function() {
            slider.classList.add('active');
        }
        closeSidebar.onclick = function() {
            slider.classList.remove('active');
        };