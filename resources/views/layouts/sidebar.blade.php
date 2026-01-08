@include('layouts.header')


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
            <!-- Dashboard -->
            <li class="menu-item">
                <a href="/dashboard" class="menu-link active text-white">
                    <span style="padding-left:10px">
                        <i class="bx bx-home-smile me-2"></i>
                        Dashboard
                    </span>
                </a>
            </li>

            <!-- <li class="menu-header">MANAGEMENT</li> -->

            <li class="menu-item">
                <div class="menu-link  text-white" onclick="toggleMenu('ProductMenu','warehouseArrow')">
                    <span>
                        <i class="bx bx-store me-2 "></i>
                        Product Management
                    </span>
                    <i class="bx bx-chevron-right arrow" id="warehouseArrow"></i>
                </div>
                <ul class="submenu" id="ProductMenu">
                    @if (auth()->check() && auth()->user()->role_id == 1)
                    <li><a href="{{ route('brands.index') }}">Brand</a></li>
                    <li><a href="{{ route('category.index') }}">Category</a></li>
                    <li><a href="{{ route('sub-category.index') }}">Sub Category</a></li>
                    @endif
                    <li><a href="{{ route('units.create') }}">Unit</a></li>
                    <li><a href="{{ route('product.index') }}">Products</a></li>

                </ul>

            </li>

            <li class="menu-item">
                <div class="menu-link  text-white" onclick="toggleMenu('suppplierMenu','inventoryArrow')">
                    <span>
                        <i class="bx bx-package me-2 "></i>
                        Supplier Management
                    </span>
                    <i class="bx bx-chevron-right arrow" id="suppplierArrow"></i>
                </div>

                <ul class="submenu" id="suppplierMenu">
                    <li><a href="{{ route('supplier.index') }}">Supplier Details</a></li>

                </ul>
            </li>

            <!-- Warehouse -->
            <li class="menu-item">
                <div class="menu-link  text-white" onclick="toggleMenu('warehouseMenu','warehouseArrow')">
                    <span>
                        <i class="bx bx-store me-2 "></i>
                        Warehouse Management
                    </span>
                    <i class="bx bx-chevron-right arrow" id="warehouseArrow"></i>
                </div>
                <ul class="submenu" id="warehouseMenu">
                    <li><a href="{{ route('warehouse.index') }}">Add Warehouse</a></li>
                    @if (auth()->check() && auth()->user()->role_id == 1)
                    <li><a href="{{ route('roles.index') }}">Role Management</a></li>
                    <li><a href="{{ route('user.profile') }}">User Management</a></li>
                    @endif
                    <li><a href="{{ route('index.addStock.warehouse') }}">Add Warehouse Stock</a></li>
                </ul>
            </li>

            <!-- Inventory Dropdown -->
            <li class="menu-item">
                <div class="menu-link  text-white" onclick="toggleMenu('inventoryMenu','inventoryArrow')">
                    <span>
                        <i class="bx bx-package me-2 "></i>
                        Inventory Management
                    </span>
                    <i class="bx bx-chevron-right arrow" id="inventoryArrow"></i>
                </div>

                <ul class="submenu" id="inventoryMenu">
                    <li><a href="{{ route('batches.index') }}">Batch Management</a></li>
                    <li><a href="/expiry-alerts">Expiry Alerts</a></li>
                    <li><a href="{{ route('sale.create') }}">Expiry Sell</a></li>
                </ul>
            </li>

            <li class="menu-item">
                <div class="menu-link  text-white" onclick="toggleMenu('TransferMenu','inventoryArrow')">
                    <span>
                        <i class="bx bx-package me-2 "></i>
                        Transfer Management
                    </span>
                    <i class="bx bx-chevron-right arrow" id="inventoryArrow"></i>
                </div>

                <ul class="submenu" id="TransferMenu">
                    <li><a href="{{ route('transfer.index') }}"> Master to District Warehouse Transfers</a></li>
                    <li><a href="">District To District Warehouse Transfers</a></li>
                    <li><a href="{{ route('district-taluka-transfer.index') }}">District To Taluka Warehouse Transfers</a></li>
                    <li><a href="{{ route('taluka.transfer.index') }}">Taluka to Taluka Warehouse Transfers</a></li>
                    <li><a href="">Taluka to Distribution Center Warehouse Transfers</a></li>
                </ul>
            </li>

            <li class="menu-item">
                <div class="menu-link  text-white" onclick="toggleMenu('OrderMenu','orderArrow')">
                    <span>
                        <i class="bx bx-package me-2 "></i>
                        Order Management
                    </span>
                    <i class="bx bx-chevron-right arrow" id="orderArrow"></i>
                </div>

                <ul class="submenu" id="OrderMenu">
                    <li><a href="{{ route('warehouse.transfer.index') }}">District-Wise Warehouse Stock Transfer
                            Approval</a></li>
                    <li><a href="{{ route('stock-returns.index') }}">Warehouse Stock Return</a></li>

                </ul>
            </li>

            @if (auth()->check() && auth()->user()->role_id == 1)
            <li class="menu-item">
                <div class="menu-link  text-white" onclick="toggleMenu('shopMenu','warehouseArrow')">
                    <span>
                        <i class="bx bx-store me-2 "></i>
                        Distribution Center
                    </span>
                    <i class="bx bx-chevron-right arrow" id="warehouseArrow"></i>
                </div>
                <ul class="submenu" id="shopMenu">
                    <li><a href="{{ route('grocery-shops.index') }}">Shop Management</a></li>
                    @if (auth()->check() && auth()->user()->role_id == 1)
                    <li><a href="{{ route('delivery-agents.index') }}">Delivery Agent</a></li>
                    <li><a href="{{ route('vehicle-assignments.index') }}">Vehicle Assignment</a></li>
                    @endif

                </ul>
            </li>
            @endif

            <li class="menu-item">
                <div class="menu-link  text-white" onclick="toggleMenu('PosMenu','PosArrow')">
                    <span style="padding-left: 10px;">
                        <i class="bx bx-package me-2 "></i>
                        POS System
                    </span>
                    <i class="bx bx-chevron-right arrow" id="PosArrow"></i>
                </div>

                <ul class="submenu" id="PosMenu">
                    <li><a href="/purchase-orders/create">Add Purches List</a></li>
                    <li><a href="{{ route('purchase.orders.index') }}">Purches History</a></li>
                    <li><a href="/warehouse-transfer-request/create">Stock Request</a></li>
                    <li><a href="/warehouse-transfer-request/incoming">Incoming Request</a></li>
                </ul>
            </li>

            <li class="menu-item">
                <div class="menu-link  text-white" onclick="toggleMenu('OfferMenu','offerArrow')">
                    <span>
                        <i class="bx bx-package me-2 "></i>
                        Offer / Scheme Management
                    </span>
                    <i class="bx bx-chevron-right arrow" id="offerArrow"></i>
                </div>

                <ul class="submenu" id="OfferMenu">
                    @if (auth()->check() && auth()->user()->role_id == 1)
                    <li><a href="{{ route('sale.create') }}">Offer Management</a></li>
                    <li><a href="{{ route('retailer-offers.index') }}">Retailer Offer Management</a></li>

                    <li><a href="{{ route('coupons.index') }}">Coupon Management</a></li>
                    @endif
                    <li><a href="{{ route('offers.index') }}">Coupon</a></li>
                </ul>
            </li>

            <li class="menu-item">
                <div class="menu-link  text-white" onclick="toggleMenu('CustomerMenu','inventoryArrow')">
                    <span>
                        <i class="bx bx-package me-2 "></i>
                        Customer Management
                    </span>
                    <i class="bx bx-chevron-right arrow" id="inventoryArrow"></i>
                </div>

                <ul class="submenu" id="CustomerMenu">

                    <li><a href="{{ route('customer-orders.index') }}">Customer Order</a></li>
                    <li><a href="{{ route('customer-returns.index') }}">Order Return</a></li>

                </ul>
            </li>

            <li class="menu-item">
                <div class="menu-link text-white" onclick="toggleMenu('ReportMenu','reportArrow')">
                    <span style="padding-left: 10px;">
                        <i class="bx bx-bar-chart-alt-2 me-2"></i>
                        Reports
                    </span>
                    <i class="bx bx-chevron-right arrow" id="reportArrow"></i>
                </div>

                <ul class="submenu" id="ReportMenu">
                    <li>
                        <a href="{{ route('warehouse-stock.report') }}">
                            Warehouse transfer Report
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('stock-movement.report') }}">
                            Stock Movement Report
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('lowstock.index') }}">
                            Low Stock Alert
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('lowstock.analytics') }}">
                            Low Stock Analytics
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('transfer-challans.index') }}">
                            Transfer Challen
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item">
                <div class="menu-link  text-white" onclick="toggleMenu('SettingMenu','settingArrow')">
                    <span style="padding-left: 10px;">
                        <i class="bx bx-package me-2 "></i>
                        Setting
                    </span>
                    <i class="bx bx-chevron-right arrow" id="settingArrow"></i>
                </div>

                <ul class="submenu" id="SettingMenu">
                    <li><a href="{{ route('RolePermission') }}">Permission Management</a></li>
                    <li><a href="{{ route('taxes.index') }}">Tax Management</a></li>
                </ul>
            </li>

            <li class="menu-header">WEBSITE</li>

            <li class="menu-item">
                <div class="menu-link  text-white" onclick="toggleMenu('BannerMenu','BannerArrow')">
                    <span style="padding-left: 10px;">
                        <i class="bx bx-package me-2 "></i>
                        <a href="{{ route('banners.index') }}">Banner Management</a>
                    </span>
                </div>
            </li>

            <li class="menu-item">
                <div class="menu-link  text-white" onclick="toggleMenu('BannerMenu','BannerArrow')">
                    <span style="padding-left: 10px;">
                        <i class="bx bx-package me-2 "></i>
                        <a href="{{ route('admin.contacts') }}">User Contact Details</a>
                    </span>
                </div>
            </li>

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