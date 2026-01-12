<?php

return [

    'sidebar' => [

        /* ================= Dashboard ================= */
        [
            'type'  => 'single',
            'title' => 'Dashboard',
            'icon'  => 'bx bx-home-smile',
            'route'   => 'dashboard',
        ],

        /* ================= Product Management ================= */
        [
            'type'  => 'dropdown',
            'key'   => 'ProductMenu',
            'title' => 'Product Management',
            'icon'  => 'bx bx-store',
            'children' => [
                ['title' => 'Brand', 'route' => 'brands.index'],
                ['title' => 'Category', 'route' => 'category.index'],
                ['title' => 'Sub Category', 'route' => 'sub-category.index'],
                ['title' => 'Unit', 'route' => 'units.index'],
                ['title' => 'Products', 'route' => 'product.index'],
            ],
        ],

        /* ================= Supplier Management ================= */
        [
            'type'  => 'dropdown',
            'key'   => 'suppplierMenu',
            'title' => 'Supplier Management',
            'icon'  => 'bx bx-package',
            'children' => [
                ['title' => 'Supplier Details', 'route' => 'supplier.index'],
            ],
        ],

        /* ================= Warehouse Management ================= */
        [
            'type'  => 'dropdown',
            'key'   => 'warehouseMenu',
            'title' => 'Warehouse Management',
            'icon'  => 'bx bx-store',
            'children' => [
                ['title' => 'Add Warehouse', 'route' => 'warehouse.index'],
                ['title' => 'Role Management', 'route' => 'roles.index'],
                ['title' => 'User Management', 'route' => 'user.profile'],
                ['title' => 'Add Warehouse Stock', 'route' => 'index.addStock.warehouse'],
            ],
        ],

        /* ================= Inventory Management ================= */
        [
            'type'  => 'dropdown',
            'key'   => 'inventoryMenu',
            'title' => 'Inventory Management',
            'icon'  => 'bx bx-package',
            'children' => [
                ['title' => 'Batch Management', 'route' => 'batches.index'],
                ['title' => 'Expiry Alerts', 'route' => 'batches.expiry'],
                ['title' => 'Expiry Sell', 'route' => 'sale.create'],
            ],
        ],

        /* ================= Transfer Management ================= */
        [
            'type'  => 'dropdown',
            'key'   => 'TransferMenu',
            'title' => 'Transfer Management',
            'icon'  => 'bx bx-package',
            'children' => [
                ['title' => ' Master to District Warehouse Transfers', 'route' => 'transfer.index'],
                ['title' => 'District To District Warehouse Transfers', 'route' => 'district-district.index'],
                ['title' => 'District To Taluka Warehouse Transfers', 'route' => 'district-taluka-transfer.index'],
                ['title' => 'Taluka to Taluka Warehouse Transfers', 'route' => 'taluka.transfer.index'],
                ['title' => 'Taluka to Distribution Center Warehouse Transfers', 'route' => 'taluka-shop.index'],
            ],
        ],

        /* ================= Order Management ================= */
        [
            'type'  => 'dropdown',
            'key'   => 'OrderMenu',
            'title' => 'Order & Approval',
            'icon'  => 'bx bx-package',
            'children' => [
                [
                    'title' => 'Master → District Transfer Approval',
                    'route' => 'warehouse.transfer.index',
                ],
                [
                    'title' => 'District → District Transfer Approval',
                    'route' => 'district.transfer.index',
                ],
                [
                    'title' => 'District → Taluka Transfer Approval',
                    'route' => 'district-taluka.transfer.index',
                ],
                [
                    'title' => 'Taluka → Taluka Transfer Approval',
                    'route' => 'taluka-taluka.transfer.index',
                ],
                [
                    'title' => 'Taluka → Distribution Transfer Approval',
                    'route' => 'taluka-distribution.transfer.index',
                ],
                [
                    'title' => 'Warehouse Stock Return Approval',
                    'route' => 'stock-returns.index',
                ],

            ],
        ],


        /* ================= Distribution Center (Admin) ================= */
        [
            'type'  => 'dropdown',
            'key'   => 'shopMenu',
            'title' => 'Distribution Center',
            'icon'  => 'bx bx-store',
            'children' => [
                ['title' => 'Shop Management', 'route' => 'grocery-shops.index'],
                ['title' => 'Delivery Agent', 'route' => 'delivery-agents.index'],
                ['title' => 'Vehicle Assignment', 'route' => 'vehicle-assignments.index'],
            ],
        ],

        /* ================= POS System ================= */
        [
            'type'  => 'dropdown',
            'key'   => 'PosMenu',
            'title' => 'POS System',
            'icon'  => 'bx bx-package',
            'children' => [
                ['title' => 'Add Purches List', 'route' => 'purchase.orders.create'],
                ['title' => 'Purches History', 'route' => 'purchase.orders.index'],
                ['title' => 'Stock Request', 'route' => 'warehouse_transfer.create'],
                ['title' => 'Incoming Request', 'route' => 'warehouse-transfer-request.incoming'],
            ],
        ],

        /* ================= Offer / Scheme Management ================= */
        [
            'type'  => 'dropdown',
            'key'   => 'OfferMenu',
            'title' => 'Offer / Scheme Management',
            'icon'  => 'bx bx-package',
            'children' => [
                ['title' => 'Offer Management', 'route' => 'sale.create'],
                ['title' => 'Retailer Offer Management', 'route' => 'retailer-offers.index'],
                ['title' => 'Coupon Management', 'route' => 'offers.index'],
            ],
        ],

        /* ================= Customer Management ================= */
        [
            'type'  => 'dropdown',
            'key'   => 'CustomerMenu',
            'title' => 'Customer Management',
            'icon'  => 'bx bx-package',
            'children' => [
                ['title' => 'WebSite Order', 'route' => 'userorder'],
                ['title' => 'Customer Order', 'route' => 'customer-orders.index'],
                ['title' => 'Order Return', 'route' => 'customer-returns.index'],
            ],
        ],

        /* ================= Reports ================= */
        [
            'type'  => 'dropdown',
            'key'   => 'ReportMenu',
            'title' => 'Reports',
            'icon'  => 'bx bx-bar-chart-alt-2',
            'children' => [
                ['title' => 'Warehouse transfer Report', 'route' => 'warehouse-stock.report'],
                ['title' => 'Stock Movement Report', 'route' => 'stock-movement.report'],
                ['title' => 'Low Stock Alert', 'route' => 'lowstock.index'],
                ['title' => 'Low Stock Analytics', 'route' => 'lowstock.analytics'],
                ['title' => 'Transfer Challen', 'route' => 'transfer-challans.index'],
            ],
        ],

        /* ================= Setting ================= */
        [
            'type'  => 'dropdown',
            'key'   => 'SettingMenu',
            'title' => 'Setting',
            'icon'  => 'bx bx-cog',
            'children' => [
                ['title' => 'Permission Management', 'route' => 'RolePermission'],
                ['title' => 'Tax Management', 'route' => 'taxes.index'],
            ],
        ],

        /* ================= Website ================= */
        [
            'type'  => 'single',
            'title' => 'Banner Management',
            'icon'  => 'bx bx-package',
            'route' => 'banners.index',
        ],

        [
            'type'  => 'single',
            'title' => 'User Contact Details',
            'icon'  => 'bx bx-package',
            'route' => 'admin.contacts',
        ],

        [
          'type'  => 'single',
            'title' => 'About us',
            'icon'  => 'bx bx-info-circle',
            'route' => 'admin.contacts',
        ],

    ],

];
