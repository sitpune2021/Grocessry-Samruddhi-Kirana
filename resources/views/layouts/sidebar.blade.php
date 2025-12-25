@include('layouts.header')



<!-- Sidebar -->
<div class="sidebar">

    <!-- Logo -->
    <div class="sidebar-logo">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('admin/assets/img/logo/samrudhi-kirana-logo1.png') }}" alt="Samruddhi Kirana">
        </a>
    </div>

    <ul>

        <!-- Dashboard -->

        <li class="menu-item">
            <a href="/dashboard" class="menu-link active text-white">
                <span><i class="bx bx-home-smile me-2"></i> Dashboard</span>
            </a>
        </li>


        <li class="menu-header">Management</li>

        {{-- @if (hasPermission('Roles', 'view')) --}}
        <li class="menu-item">

        </li>
        {{-- @endif --}}

        @if(auth()->check() && auth()->user()->role_id == 1)
        <li class="menu-item">
            <div class="menu-link  text-white" onclick="toggleMenu('roleMenu','inventoryArrow')">
                <span><i class="bx bx-package me-2 "></i>Role & Permission</span>
                <i class="bx bx-chevron-right arrow" id="inventoryArrow"></i>
            </div>

            <ul class="submenu" id="roleMenu">

                <li><a href="{{ route('roles.index') }}">Role</a></li>

                <li><a href="{{ route('user.profile') }}">Users</a></li>

                <li><a href="{{ route('RolePermission') }}">Role & Permission</a></li>

            </ul>
        </li>
        @endif

        <!-- Inventory Dropdown -->
        <li class="menu-item">
            <div class="menu-link  text-white" onclick="toggleMenu('inventoryMenu','inventoryArrow')">
                <span><i class="bx bx-package me-2 "></i> Inventory</span>
                <i class="bx bx-chevron-right arrow" id="inventoryArrow"></i>
            </div>

            <ul class="submenu" id="inventoryMenu">
                <li><a href="{{ route('brands.index') }}">Brand</a></li>
                <li><a href="{{ route('category.index') }}">Category</a></li>
                <li><a href="{{ route('product.index') }}">Products</a></li>
                <li><a href="{{ route('batches.index') }}">Batch Management</a></li>
                <li><a href="/expiry-alerts">Expiry Alerts</a></li>
            </ul>
        </li>

        <!-- Warehouse -->
        <li class="menu-item">
            <div class="menu-link  text-white" onclick="toggleMenu('warehouseMenu','warehouseArrow')">
                <span><i class="bx bx-store me-2 "></i> Warehouse</span>
                <i class="bx bx-chevron-right arrow" id="warehouseArrow"></i>
            </div>
            <ul class="submenu" id="warehouseMenu">
                <li><a href="{{ route('warehouse.index') }}">Add Warehouse</a></li>
                <li><a href="{{ route('index.addStock.warehouse') }}">Add Stock</a></li>
                <li><a href="{{ route('sale.create') }}">FIFO Management</a></li>
                <li><a href="{{ route('sell.index') }}">FIFO History</a></li>

                <li><a href="{{ route('transfer.index') }}">Warehouse Transfers</a></li>
            </ul>
        </li>

        <!-- Delivery Agent -->
        <li class="menu-item">
            <div class="menu-link  text-white" onclick="toggleMenu('deliveryAgentMenu','deliveryAgentArrow')">
                <span><i class="bx bx-store me-2 "></i> Delivery Agent</span>
                <i class="bx bx-chevron-right arrow" id="deliveryAgentArrow"></i>
            </div>
            <ul class="submenu" id="deliveryAgentMenu">
                <li><a href="{{ route('delivery-agents.index') }}">Agent & vehicle</a></li>
                <li><a href="{{ route('deliveries.index') }}">Deliveries</a></li>
            </ul>
        </li>
        <li class="menu-item">
            <div class="menu-link  text-white" onclick="toggleMenu('retailerMenu','warehouseArrow')">
                <span><i class="bx bx-store me-2 "></i>Retailer</span>
                <i class="bx bx-chevron-right arrow" id="warehouseArrow"></i>
            </div>
            <ul class="submenu" id="retailerMenu">
                <li><a href="{{ route('retailers.index') }}">Retailer profile</a></li>
                <li><a href="{{ route('retailer-pricing.index') }}">Retailer pricing</a></li>
                <li><a href="{{ route('retailer-orders.create') }}">Retailer Order price Lock</a></li>
            </ul>
        </li>
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