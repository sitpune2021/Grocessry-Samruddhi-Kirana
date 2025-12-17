@include('layouts.header')



<!-- Sidebar -->
<div class="sidebar">

    <!-- Logo -->
    <div class="sidebar-logo">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('admin/assets/img/logo/samrudhi-kirana-logo1.png') }}"
                alt="Samruddhi Kirana">
        </a>
    </div>

    <ul>

        <!-- Dashboard -->
        <li class="menu-item">
            <a href="{{ route('dashboard') }}" class="menu-link active text-white">
                <span><i class="bx bx-home-smile me-2"></i> Dashboard</span>
            </a>
        </li>

        <li class="menu-header">Management</li>

        <li class="menu-item">
            <a href="{{ route('user.profile') }}" class="menu-link text-white">
                <span><i class="bx bx-store me-2"></i> User Profile</span>
            </a>
        </li>
        <!-- Warehouse -->
        <li class="menu-item">
            <a href="{{ route('warehouse.index') }}" class="menu-link text-white">
                <span><i class="bx bx-store me-2"></i> Warehouse</span>
            </a>
            <a href="{{ route('index.addStock.warehouse') }}" class="menu-link text-white">
                <span><i class="bx bx-store me-2"></i>Add Stock in Warehouse</span>
            </a>
        </li>

        <!-- Inventory Dropdown -->
        <li class="menu-item">
            <div class="menu-link  text-white" onclick="toggleInventory()">
                <span><i class="bx bx-package me-2 "></i> Inventory</span>
                <i class="bx bx-chevron-right arrow" id="inventoryArrow"></i>
            </div>

            <ul class="submenu" id="inventoryMenu">
                <li><a href="{{ route('category.index') }}">Category</a></li>
                <li><a href="{{ route('product.index') }}">Products</a></li>
                <li><a href="{{ route('batches.index') }}">Batch Management</a></li>
                <li>
                    <a href="{{ route('sale.create') }}">
                        FIFO Management
                    </a>
                </li>
                <li><a href="/expiry-alerts">Expiry Alerts</a></li>
            </ul>
        </li>



        <li class="menu-item">
<<<<<<< HEAD
    <a href="{{ route('transfer.index') }}"
       class="menu-link text-white flex items-center">
        <i class="menu-icon tf-icons bx bx-package" ></i>
        <span class="m-0 p-0">Warehouse Transfers</span>
    </a>
</li>
=======
            <a href="{{ route('transfer.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-package"></i>
                <div class=" " data-i18n="Warehouse ">Warehouse Transfers</div>

            </a>
        </li>
>>>>>>> c0516e2df30525db0483d71a4e999d3b237df265


    </ul>
</div>

<script>
    function toggleInventory() {
        const menu = document.getElementById('inventoryMenu');
        const arrow = document.getElementById('inventoryArrow');

        if (menu.style.display === 'block') {
            menu.style.display = 'none';
            arrow.classList.remove('rotate');
        } else {
            menu.style.display = 'block';
            arrow.classList.add('rotate');
        }
    }
</script>
