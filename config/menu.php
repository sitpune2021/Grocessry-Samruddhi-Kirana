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

        /* ================ User Management ==============*/
        [
            'type'  => 'dropdown',
            'key'   => 'userMenu',
            'title' => 'User Management',
            'icon'  => 'bx bx-package',
            'children' => [
                ['title' => 'Role Management', 'route' => 'roles.index'],
                ['title' => 'User Management', 'route' => 'user.profile'],
                ['title' => 'Permission Management', 'route' => 'RolePermission'],
                ['title' => 'Tax Management', 'route' => 'taxes.index'],

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


        /* ================= Warehouse Management ================= */
        [
            'type'  => 'dropdown',
            'key'   => 'warehouseMenu',
            'title' => 'Warehouse / Distribution Center',
            'icon'  => 'bx bx-store',
            'children' => [
                ['title' => 'Warehouse', 'route' => 'warehouse.index'],
                ['title' => 'Warehouse Stock', 'route' => 'index.addStock.warehouse'],
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
                //['title' => 'Near Expiry Sell', 'route' => 'sale.create'],
                //['title' => 'Sale Product', 'route' => 'sale.create'],
            ],
        ],

        /* ================= Transfer Management ================= */
        [
            'type'  => 'dropdown',
            'key'   => 'TransferMenu',
            'title' => 'Transfer Management',
            'icon'  => 'bx bx-package',
            'children' => [
                ['title' => 'Warehouse Stock Request', 'route' => 'transfer.index'],
                // ['title' => 'District To District Warehouse Transfers', 'route' => 'district-district.index'],
                // ['title' => 'District To Taluka Warehouse Transfers', 'route' => 'district-taluka-transfer.index'],
                // ['title' => 'Taluka to Taluka Warehouse Transfers', 'route' => 'taluka.transfer.index'],
                // ['title' => 'Taluka to Distribution Center Warehouse Transfers', 'route' => 'taluka-shop.index'],
            ],
        ],

        /* ================= Order Management ================= */
        [
            'type'  => 'dropdown',
            'key'   => 'OrderMenu',
            'title' => 'Approval / Recieve',
            'icon'  => 'bx bx-package',
            'children' => [
                [
                    'title' => 'Warehouse Stock Approve',
                    'route' => 'warehouse.transfer.index',
                ],
                // [
                //     'title' => 'District → District Transfer Approval',
                //     'route' => 'district.transfer.index',
                // ],
                // [
                //     'title' => 'District → Taluka Transfer Approval',
                //     'route' => 'district-taluka.transfer.index',
                // ],
                // [
                //     'title' => 'Taluka → Taluka Transfer Approval',
                //     'route' => 'taluka-taluka.transfer.index',
                // ],
                // [
                //     'title' => 'Taluka → Distribution Transfer Approval',
                //     'route' => 'taluka-distribution.transfer.index',
                // ],
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
            'title' => 'Delivery Agent',
            'icon'  => 'bx bx-store',
            'children' => [
                // ['title' => 'Shop Management', 'route' => 'grocery-shops.index'],
                ['title' => 'Delivery Agent', 'route' => 'delivery-agents.index'],
                ['title' => 'Vehicle Assignment', 'route' => 'vehicle-assignments.index'],
            ],
        ],

        /* ================= POS System ================= */
        // [
        //     'type'  => 'dropdown',
        //     'key'   => 'PosMenu',
        //     'title' => 'POS System',
        //     'icon'  => 'bx bx-package',
        //     'children' => [
        //         ['title' => 'Add Purches List', 'route' => 'purchase.orders.create'],
        //         ['title' => 'Purches History', 'route' => 'purchase.orders.index'],
        //         ['title' => 'Stock Request', 'route' => 'warehouse_transfer.create'],
        //         ['title' => 'Incoming Request', 'route' => 'warehouse-transfer-request.incoming'],
        //     ],
        // ],

        /* ================= Offer / Scheme Management ================= */
        [
            'type'  => 'dropdown',
            'key'   => 'OfferMenu',
            'title' => 'Offer / Scheme Management',
            'icon'  => 'bx bx-package',
            'children' => [
                ['title' => 'Offer Management', 'route' => 'offers.index'],
                //['title' => 'Retailer Offer Management', 'route' => 'retailer-offers.index'],
                ['title' => 'Coupon Management', 'route' => 'coupons.index'],
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
        // [
        //     'type'  => 'dropdown',
        //     'key'   => 'SettingMenu',
        //     'title' => 'Setting',
        //     'icon'  => 'bx bx-cog',
        //     'children' => [
        //         ['title' => 'Tax Management', 'route' => 'taxes.index'],
        //     ],
        // ],

        /* ================= Website ================= */
        // [
        //     'type'  => 'single',
        //     'title' => 'Banner Management',
        //     'icon'  => 'bx bx-package',
        //     'route' => 'banners.index',
        // ],

        // [
        //     'type'  => 'single',
        //     'title' => 'User Contact Details',
        //     'icon'  => 'bx bx-package',
        //     'route' => 'admin.contacts',
        // ],

        // [
        //     'type'  => 'single',
        //     'title' => 'About us',
        //     'icon'  => 'bx bx-info-circle',
        //     'route' => 'admin.aboutus',
        // ],

        [
            'type'  => 'dropdown',
            'key'   => 'wesiteMenu',
            'title' => 'Website Management',
            'icon'  => 'bx bx-store',
            'children' => [
                ['title' => 'Banner Management', 'route' => 'banners.index'],
                ['title' => 'User Contact Details', 'route' => 'admin.contacts'],
                ['title' => 'About Us', 'route' => 'admin.aboutus'],
            ],
        ],

    ],

];
