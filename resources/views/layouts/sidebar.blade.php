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
                <span><i class="bx bx-home-smile me-2"></i>DASHBOARD</span>
            </a>
        </li>


        <li class="menu-header">MANAGEMENT</li>

        {{-- @if (hasPermission('Roles', 'view')) --}}
        <li class="menu-item">

        </li>
        {{-- @endif --}}

        @if(auth()->check() && auth()->user()->role_id == 1)
        <li class="menu-item">
            <div class="menu-link  text-white" onclick="toggleMenu('userMenu','inventoryArrow')">
                <span><i class="bx bx-package me-2 "></i>USER MANAGEMENT</span>
                <i class="bx bx-chevron-right arrow" id="inventoryArrow"></i>
            </div>

            <ul class="submenu" id="userMenu">

                <li><a href="{{ route('user.profile') }}">USER</a></li>

            </ul>
        </li>
        @endif
        

        <!-- Inventory Dropdown -->
        <li class="menu-item">
            <div class="menu-link  text-white" onclick="toggleMenu('inventoryMenu','inventoryArrow')">
                <span><i class="bx bx-package me-2 "></i>INVENTORY</span>
                <i class="bx bx-chevron-right arrow" id="inventoryArrow"></i>
            </div>

            <ul class="submenu" id="inventoryMenu">
                <li><a href="{{ route('brands.index') }}">BRAND</a></li>
                <li><a href="{{ route('category.index') }}">CATEGORY</a></li>
                <li><a href="{{ route('product.index') }}">PRODUCTS</a></li>
                <li><a href="{{ route('batches.index') }}">BATCH MANAGEMENT</a></li>
                <li><a href="/expiry-alerts">EXPIRY ALERTS</a></li>
            </ul>
        </li>

        <!-- Warehouse -->
        <li class="menu-item">
            <div class="menu-link  text-white" onclick="toggleMenu('warehouseMenu','warehouseArrow')">
                <span><i class="bx bx-store me-2 "></i>WAREHOUSE MANAGEMENT</span>
                <i class="bx bx-chevron-right arrow" id="warehouseArrow"></i>
            </div>
            <ul class="submenu" id="warehouseMenu">
                <li><a href="{{ route('warehouse.index') }}">Add Warehouse</a></li>
                <li><a href="{{ route('index.addStock.warehouse') }}">Add Warehouse Stock</a></li>
                @if(auth()->check() && auth()->user()->role_id == 1)
                <li><a href="{{ route('sale.create') }}">Offer Management</a></li>
                @endif
                <li><a href="{{ route('transfer.index') }}">District Warehouse Transfers</a></li>

            </ul>
        </li>

        <!-- Delivery Agent -->
        <li class="menu-item">
            <div class="menu-link  text-white" onclick="toggleMenu('deliveryAgentMenu','deliveryAgentArrow')">
                <span><i class="bx bx-store me-2 "></i> DELIVERY AGENT</span>
                <i class="bx bx-chevron-right arrow" id="deliveryAgentArrow"></i>
            </div>
            <ul class="submenu" id="deliveryAgentMenu">
                <li><a href="{{ route('delivery-agents.index') }}">AGENT & VEHICALE</a></li>
                <li><a href="{{ route('deliveries.index') }}">DELIVERIES</a></li>
            </ul>
        </li>
        <!-- <li class="menu-item">
            <div class="menu-link  text-white" onclick="toggleMenu('retailerMenu','warehouseArrow')">
                <span><i class="bx bx-store me-2 "></i>Retailer</span>
                <i class="bx bx-chevron-right arrow" id="warehouseArrow"></i>
            </div>
            <ul class="submenu" id="retailerMenu">
                <li><a href="{{ route('retailers.index') }}">Retailer profile</a></li>
                <li><a href="{{ route('retailer-pricing.index') }}">Retailer pricing</a></li>
                <li><a href="{{ route('retailer-orders.create') }}">Retailer Order price Lock</a></li>
            </ul>
        </li> -->

        @if(auth()->check() && auth()->user()->role_id == 1)
        <li class="menu-item">
            <div class="menu-link  text-white" onclick="toggleMenu('shopMenu','warehouseArrow')">
                <span><i class="bx bx-store me-2 "></i>SHOP MANAGEMENT</span>
                <i class="bx bx-chevron-right arrow" id="warehouseArrow"></i>
            </div>
            <ul class="submenu" id="shopMenu">
                <li><a href="{{ route('grocery-shops.index') }}">SHOP DETAILS</a></li>
            </ul>
        </li>

        @if(auth()->check() && auth()->user()->role_id == 1)
        <li class="menu-item">
            <div class="menu-link  text-white" onclick="toggleMenu('roleMenu','inventoryArrow')">
                <span><i class="bx bx-package me-2 "></i>ROLE & PERMISSION</span>
                <i class="bx bx-chevron-right arrow" id="inventoryArrow"></i>
            </div>

            <ul class="submenu" id="roleMenu">

                <li><a href="{{ route('roles.index') }}">ROLE</a></li>

                <li><a href="{{ route('RolePermission') }}">PERMISSION</a></li>

            </ul>
        </li>
        @endif
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