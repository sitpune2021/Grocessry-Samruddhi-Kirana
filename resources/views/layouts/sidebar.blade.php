@include('layouts.header')

 

<!-- Sidebar -->
<div class="sidebar">

    <!-- Logo -->
    <div class="sidebar-logo">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('admin/assets/img/logo/samrudhi-kirana-logo.png') }}"
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

        <!-- Warehouse -->
        <li class="menu-item">
            <a href="{{ route('warehouse.index') }}" class="menu-link text-white">
                <span><i class="bx bx-store me-2"></i> Warehouse</span>
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
                <li><a href="{{ url('/expiry-alerts') }}">Expiry Alerts</a></li>
            </ul>
        </li>

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