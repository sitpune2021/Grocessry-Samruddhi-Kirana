@include('layouts.header')


<!-- Sidebar -->
<div class="sidebar" id="sidebar">

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
                <i class="bx bx-home-smile me-2"></i><span>DASHBOARD</span>
            </a>
        </li>


        <!-- <li class="menu-header">MANAGEMENT</li> -->

        <li class="menu-item">
            <div class="menu-link  text-white" onclick="toggleMenu('OrderMenu','inventoryArrow')">
                <span><i class="bx bx-package me-2 "></i>ORDER MANAGEMENT</span>
                <i class="bx bx-chevron-right arrow" id="inventoryArrow"></i>
            </div>

            <ul class="submenu" id="OrderMenu">

                <li><a href="">TEMPORALY ORDER</a></li>

            </ul>
        </li>

        <li class="menu-item">
            <div class="menu-link  text-white" onclick="toggleMenu('CustomerMenu','inventoryArrow')">
                <span><i class="bx bx-package me-2 "></i>CUSTOMER MANAGEMENT</span>
                <i class="bx bx-chevron-right arrow" id="inventoryArrow"></i>
            </div>

            <ul class="submenu" id="CustomerMenu">

                <li><a href="">TEMPORALY</a></li>

            </ul>
        </li>

        <li class="menu-item">
            <div class="menu-link  text-white" onclick="toggleMenu('ProductMenu','warehouseArrow')">
                <span><i class="bx bx-store me-2 "></i>PRODUCT MANAGEMENT</span>
                <i class="bx bx-chevron-right arrow" id="warehouseArrow"></i>
            </div>
            <ul class="submenu" id="ProductMenu">
                @if (auth()->check() && auth()->user()->role_id == 1)
                    <li><a href="{{ route('brands.index') }}" class="uppercase">Brand</a></li>
                    <li><a href="{{ route('category.index') }}" class="uppercase">Category</a></li>
                    <li><a href="{{ route('sub-category.index') }}" class="uppercase">Sub Category</a></li>
                    <li><a href="{{ route('units.index') }}" class="uppercase">Unit</a></li>
                    <li><a href="{{ route('product.index') }}" class="uppercase">Products</a></li>
                @endif
            </ul>
        </li>

        <!-- Warehouse -->
        <li class="menu-item">
            <div class="menu-link  text-white" onclick="toggleMenu('warehouseMenu','warehouseArrow')">
                <i class="bx bx-store me-2 "></i><span>WAREHOUSE MANAGEMENT</span>
                <i class="bx bx-chevron-right arrow" id="warehouseArrow"></i>
            </div>
            <ul class="submenu" id="warehouseMenu">
                <li><a href="{{ route('warehouse.index') }}">Add Warehouse</a></li>
                <li><a href="{{ route('index.addStock.warehouse') }}">Add Warehouse Stock</a></li>
            </ul>
        </li>

        <!-- Inventory Dropdown -->
        <li class="menu-item">
            <div class="menu-link  text-white" onclick="toggleMenu('inventoryMenu','inventoryArrow')">
                <span><i class="bx bx-package me-2 "></i>INVENTORY MANAGEMENT</span>
                <i class="bx bx-chevron-right arrow" id="inventoryArrow"></i>
            </div>

            <ul class="submenu" id="inventoryMenu">

                <li><a href="{{ route('batches.index') }}">Batch Management</a></li>
                <li><a href="/expiry-alerts">Expiry Alerts</a></li>
                <li><a href="{{ route('supplier.index') }}">Supplier</a></li>

            </ul>
        </li>

        <li class="menu-item">
            <div class="menu-link  text-white" onclick="toggleMenu('TransferMenu','inventoryArrow')">
                <span><i class="bx bx-package me-2 "></i>TRANSFER MANAGEMENT</span>
                <i class="bx bx-chevron-right arrow" id="inventoryArrow"></i>
            </div>

            <ul class="submenu" id="TransferMenu">
                <li><a href="{{ route('transfer.index') }}">District Warehouse Transfers</a></li>
            </ul>
        </li>

        @if (auth()->check() && auth()->user()->role_id == 1)
            <li class="menu-item">
                <div class="menu-link  text-white" onclick="toggleMenu('shopMenu','warehouseArrow')">
                    <i class="bx bx-store me-2 "></i><span>SHOP MANAGEMENT</span>
                    <i class="bx bx-chevron-right arrow" id="warehouseArrow"></i>
                </div>
                <ul class="submenu" id="shopMenu">
                    <li><a href="{{ route('grocery-shops.index') }}">Shop Details</a></li>
                    @if (auth()->check() && auth()->user()->role_id == 1)
                        <li><a href="{{ route('delivery-agents.index') }}">Agent & Vehicle</a></li>
                    @endif
                    <li><a href="{{ route('deliveries.index') }}">Deliveries</a></li>
                </ul>
            </li>
        @endif

        <li class="menu-item">
            <div class="menu-link  text-white" onclick="toggleMenu('OfferMenu','inventoryArrow')">
                <span><i class="bx bx-package me-2 "></i>OFFER / SCHEME MANAGEMENT</span>
                <i class="bx bx-chevron-right arrow" id="inventoryArrow"></i>
            </div>

            <ul class="submenu" id="OfferMenu">
                @if (auth()->check() && auth()->user()->role_id == 1)
                    <li><a href="{{ route('sale.create') }}">Offer Management</a></li>
                @endif
            </ul>
        </li>

        <li class="menu-item">
            <div class="menu-link  text-white" onclick="toggleMenu('SettingMenu','inventoryArrow')">
                <span><i class="bx bx-package me-2 "></i>SETTINGS</span>
                <i class="bx bx-chevron-right arrow" id="inventoryArrow"></i>
            </div>

            <ul class="submenu" id="SettingMenu">
                <li><a href="{{ route('user.profile') }}">User Management</a></li>
                <li><a href="{{ route('roles.index') }}">Role Management</a></li>
                <li><a href="{{ route('RolePermission') }}">Permission Management</a></li>
                <li><a href="{{ route('RolePermission') }}">Tax Management</a></li>
            </ul>
        </li>

        {{-- @if (hasPermission('Roles', 'view')) --}}
        <li class="menu-item">

        </li>
        {{-- @endif --}}











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
