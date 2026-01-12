<?php

return [

    'sidebar' => [

        /* ================= Dashboard ================= */
        [
            'title' => 'Dashboard',
            'icon'  => 'bx bx-home-smile',
            'route' => 'dashboard',
            'type'  => 'single',
        ],

        /* ================= Supplier & Purchase ================= */
        [
            'title' => 'Supplier & Purchase Management',
            'icon'  => 'bx bx-package',
            'type'  => 'dropdown',
            'key'   => 'supplier',
            'children' => [
                ['title' => 'Supplier Management', 'route' => 'supplier.index'],
                ['title' => 'Add Purchase List', 'route' => 'purchase.orders.create'],
                ['title' => 'Purchase History', 'route' => 'purchase.orders.index'],
            ],
        ],

        /* ================= Warehouse ================= */
        [
            'title' => 'Warehouse Management',
            'icon'  => 'bx bx-store',
            'type'  => 'dropdown',
            'key'   => 'warehouse',
            'children' => [
                ['title' => 'Warehouse Management', 'route' => 'warehouse.index'],
                ['title' => 'Add Warehouse Stock', 'route' => 'index.addStock.warehouse'],
            ],
        ],

        /* ================= Inventory ================= */
        [
            'title' => 'Inventory Management',
            'icon'  => 'bx bx-package',
            'type'  => 'dropdown',
            'key'   => 'inventory',
            'children' => [
                ['title' => 'Batch Management', 'route' => 'batches.index'],
                ['title' => 'Expiry Alerts', 'url' => '/expiry-alerts'],
                ['title' => 'Near Expiry Sale', 'route' => 'sale.create'],
            ],
        ],

        /* ================= Stock Transfer ================= */
        [
            'title' => 'Stock Transfer',
            'icon'  => 'bx bx-package',
            'type'  => 'dropdown',
            'key'   => 'transfer',
            'children' => [
                ['title' => 'Master → District', 'route' => 'transfer.index'],
                ['title' => 'District → District', 'route' => 'district-district.index'],
                ['title' => 'District → Taluka', 'route' => 'district-taluka-transfer.index'],
                ['title' => 'Taluka → Taluka', 'route' => 'taluka.transfer.index'],
                ['title' => 'Taluka → Distribution Center', 'route' => 'taluka-shop.index'],
            ],
        ],

        /* ================= Order & Approval ================= */
        [
            'title' => 'Order & Approval',
            'icon'  => 'bx bx-package',
            'type'  => 'dropdown',
            'key'   => 'order',
            'children' => [
                ['title' => 'Transfer Approval', 'route' => 'warehouse.transfer.index'],
                ['title' => 'Warehouse Stock Return', 'route' => 'stock-returns.index'],
            ],
        ],

        /* ================= Distribution Center (Admin Only) ================= */
        [
            'title' => 'Distribution Center',
            'icon'  => 'bx bx-store',
            'type'  => 'dropdown',
            'key'   => 'distribution',
            // 'roles' => [1], // 👈 super admin only
            'children' => [
                ['title' => 'Shop Management', 'route' => 'grocery-shops.index'],
                ['title' => 'Delivery Agent Management', 'route' => 'delivery-agents.index'],
                ['title' => 'Vehicle Assignment', 'route' => 'vehicle-assignments.index'],
            ],
        ],

        /* ================= POS & Sales ================= */
        [
            'title' => 'POS & Sales',
            'icon'  => 'bx bx-package',
            'type'  => 'dropdown',
            'key'   => 'pos',
            'children' => [
                ['title' => 'Stock Request', 'url' => '/warehouse-transfer-request/create'],
                ['title' => 'Incoming Requests', 'url' => '/warehouse-transfer-request/incoming'],
            ],
        ],

        /* ================= Offers ================= */
        [
            'title' => 'Offers & Schemes',
            'icon'  => 'bx bx-package',
            'type'  => 'dropdown',
            'key'   => 'offers',
            'children' => [
                ['title' => 'Offer Management', 'route' => 'sale.create', 'roles' => [1]],
                ['title' => 'Retailer Offers', 'route' => 'retailer-offers.index', 'roles' => [1]],
                ['title' => 'Coupon Management', 'route' => 'offers.index'],
            ],
        ],

        /* ================= Customer ================= */
        [
            'title' => 'Customer Management',
            'icon'  => 'bx bx-package',
            'type'  => 'dropdown',
            'key'   => 'customer',
            'children' => [
                ['title' => 'Customer Orders', 'route' => 'customer-orders.index'],
                ['title' => 'Order Returns', 'route' => 'customer-returns.index'],
                ['title' => 'User Order', 'route' =>'userorder']
            ],
        ],

        /* ================= Reports ================= */
        [
            'title' => 'Reports & Analytics',
            'icon'  => 'bx bx-bar-chart-alt-2',
            'type'  => 'dropdown',
            'key'   => 'reports',
            'children' => [
                ['title' => 'Warehouse Transfer Report', 'route' => 'warehouse-stock.report'],
                ['title' => 'Stock Movement Report', 'route' => 'stock-movement.report'],
                ['title' => 'Low Stock Alert', 'route' => 'lowstock.index'],
                ['title' => 'Low Stock Analytics', 'route' => 'lowstock.analytics'],
                ['title' => 'Transfer Challan', 'route' => 'transfer-challans.index'],
            ],
        ],

        /* ================= Settings ================= */
        [
            'title' => 'Settings',
            'icon'  => 'bx bx-package',
            'type'  => 'dropdown',
            'key'   => 'settings',
            'children' => [
                ['title' => 'Permission Management', 'route' => 'RolePermission'],
                ['title' => 'Role Management', 'route' => 'roles.index'],
                ['title' => 'User Management', 'route' => 'user.profile'],
                ['title' => 'Tax Management', 'route' => 'taxes.index'],
            ],
        ],

        /* ================= Website ================= */
        [
            'title' => 'Banner Management',
            'icon'  => 'bx bx-package',
            'route' => 'banners.index',
            'type'  => 'single',
        ],

        [
            'title' => 'User Contact Details',
            'icon'  => 'bx bx-package',
            'route' => 'admin.contacts',
            'type'  => 'single',
        ],

    ],

];
