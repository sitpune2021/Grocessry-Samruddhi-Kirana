@include('layouts.header')

<style>
.sidebar-text{
    display: flex !important;
    justify-content: space-between !important;  
    align-items: center !important; 
    width: 100%;
}
.list-outer{
    margin-bottom: 10px !important;
}
.dropdowns-menus{
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}
</style>
<!-- Sidebar -->
<div class="sidebar" id="sidebar">

    <div class="sidebar-close">
        <i class="bx bx-x" style="color:red !important;" id="sidebarClose"></i>
    </div>

    <!-- Logo -->
    <div class="sidebar-logo">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('admin/assets/img/logo/samrudhi-kirana-logo1.png') }}" alt="Samruddhi Kirana">
        </a>
    </div>

    <div class="sidebar-menu" id="sidebarMenu">
        <ul>

            <div class="sidebar-text"> <!-- Dashboard -->
                <li class="list-outer">
                    <a href="/dashboard" class=" active text-white">
                          <i class="bx bx-home-smile me-2"></i>
                        <span style="">
                          
                            Dashboard
                        </span>
                    </a>
                </li>
            </div>
            <!-- <li class="menu-header">MANAGEMENT</li> -->

          <div class="sidebar-text" style="cursor: pointer;">
              <li class="">
                <div class="dropdowns-menus  text-white" onclick="toggleMenu('ProductMenu','warehouseArrow')">
                     <i class="bx bx-store me-2 "></i>
                    <span class="block" >
                        Product Management
                    </span>
                    <i class="bx bx-chevron-right arrow " style="margin-left: 20px; font-size: 20px;" id="warehouseArrow"></i>
                </div>
                <ul class="submenu" id="ProductMenu" class=" " style="list-style:none ; margin-left: 45px; text-transform: uppercase;">
                    <li><a href="{{ route('brands.index') }}">Brand</a></li>
                    <li><a href="{{ route('category.index') }}">Category</a></li>
                    <li><a href="{{ route('sub-category.index') }}">Sub Category</a></li>
                    <li><a href="{{ route('units.index') }}">Unit</a></li>
                    <li><a href="{{ route('product.index') }}">Products</a></li>

                </ul>

            </li>
          </div>


        </ul>
    </div>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.submenu').forEach(menu => {
            menu.style.display = 'none';
        });

        document.querySelectorAll('.arrow').forEach(arrow => {
            arrow.classList.remove('rotate');
        });
    });

    function toggleMenu(menuId, arrowId) {
        const currentMenu = document.getElementById(menuId);
        const currentArrow = document.getElementById(arrowId);

        const isOpen = currentMenu.style.display === 'block';

        document.querySelectorAll('.submenu').forEach(menu => {
            menu.style.display = 'none';
        });

        document.querySelectorAll('.arrow').forEach(arrow => {
            arrow.classList.remove('rotate');
        });

        if (!isOpen) {
            currentMenu.style.display = 'block';
            currentArrow.classList.add('rotate');
        }
    }
</script>