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
                ['title' => 'Role Management', 'route' => 'roles.index', 'roles' => [1, 2]],
                [
                    'title' => 'Tax Management',
                    'route' => 'taxes.index',
                    'roles' => [1, 2] // Only visible for role_id 1 & 2
                ],
                ['title' => 'Permission Management', 'route' => 'RolePermission', 'roles' => [1]],
                ['title' => 'User Management', 'route' => 'user.profile'],

            ],
        ],

        /* ================= Product Management ================= */
        [
            'type'  => 'dropdown',
            'key'   => 'ProductMenu',
            'title' => 'Product Management',
            'icon'  => 'bx bx-store',
            'children' => [
                ['title' => 'Category', 'route' => 'category.index'],
                ['title' => 'Sub Category', 'route' => 'sub-category.index'],
                ['title' => 'Brand', 'route' => 'brands.index'],
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
            'roles' => [1, 2], // allowed roles
            'children' => [
                ['title' => 'Supplier Details', 'route' => 'supplier.index'],
                ['title' => 'Supplier Challan', 'route' => 'supplier_challan.index'],

            ],
        ],

        /* ================= Warehouse Management ================= */
        [
            'type'  => 'single',
            'title' => 'Warehouse / Distribution',
            'icon'  => 'bx bx-store',
            'route'   => 'warehouse.index',
        ],

        [
            'type'  => 'single',
            'title' => 'DC Service Area',
            'icon'  => 'bx bx-map-pin',
            'route'   => 'warehouse.service-areas.index',
        ],
    

        /* ================= Inventory Management ================= */
        [
            'type'  => 'dropdown',
            'key'   => 'inventoryMenu',
            'title' => 'Inventory Management',
            'icon'  => 'bx bx-package',
            'children' => [
                ['title' => 'Stock Management', 'route' => 'index.addStock.warehouse'],
                ['title' => 'Batch Management', 'route' => 'batches.index'],
                ['title' => 'Expiry Alerts', 'route' => 'batches.expiry'],
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
                ['title' => 'Transfer Challen', 'route' => 'transfer-challans.index'],
                [
                    'title' => 'Warehouse Stock Return',
                    'route' => 'stock-returns.index',
                ],
            ],
        ],

        /* ================= Order Management ================= */
        [
            'type'  => 'dropdown',
            'key'   => 'OrderMenu',
            'title' => 'Approve / Recieve',
            'icon'  => 'bx bx-package',
            'children' => [
                [
                    'title' => 'Warehouse Stock Approve',
                    'route' => 'warehouse.transfer.index',
                ],
            ],
        ],


        /* ================= Distribution Center (Admin) ================= */
        [
            'type'  => 'dropdown',
            'key'   => 'shopMenu',
            'title' => 'Delivery Agent',
            'icon'  => 'bx bx-store',
            'exclude_roles' => [3, 4],
            'children' => [
                // ['title' => 'Shop Management', 'route' => 'grocery-shops.index'],
                ['title' => 'Delivery Agent', 'route' => 'delivery-agents.index'],
                ['title' => 'Vehicle Assignment', 'route' => 'vehicle-assignments.index'],
            ],
        ],

        /* ================= Retailer Register ================= */
        // [
        //     'type'       => 'single',
        //     'title'      => 'Retailer Management',
        //     'icon'       => 'bx bx-store',
        //     'route'      => 'retailers.index',
        //     'roles' => [5],
        //     //'permission' => 'retailer.view',
        // ],

        [
            'type'  => 'dropdown',
            'key'   => 'retailerMenu',
            'title' => 'Retailer Management',
            'icon'  => 'bx bx-store',
            'roles' => [5],
            'children' => [
                ['title' => 'Retailer Register', 'route' => 'retailers.index'],
                ['title' => 'Retailer Pricing', 'route' => 'retailer-pricing.index'],
                ['title' => 'Retailer Order', 'route' => 'retailer.order.retailerindex'],
            ],
        ],

        /* ================= POS System ================= */
        [
            'type'  => 'single',
            'title' => 'POS System',
            'icon'  => 'bx bx-package',
            'roles' => [5],
            'route'   => 'pos.create',
        ],

        /* ================= Offer / Scheme Management ================= */
        [
            'type'  => 'single',
            'title' => 'Coupon Management',
            'icon'  => 'bx bx-package',
            'roles' => [1, 2, 5],
            'route'   => 'coupons.index',
        ],

        /* ================= Customer Management ================= */
        [
            'type'  => 'dropdown',
            'key'   => 'CustomerMenu',
            'title' => 'Customer Management',
            'icon'  => 'bx bx-package',
            'children' => [
                ['title' => 'Customer Order', 'route' => 'customer-orders.index', 'exclude_roles' => [3, 4],],
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
                ['title' => 'Stock Request Report', 'route' => 'warehouse-stock.report'],
                ['title' => 'Stock Movement Report', 'route' => 'stock-movement.report'],
                ['title' => ' Stock Return Report', 'route' => 'stock-returns.report'],
                ['title' => 'POS Report', 'route' => 'pos-report'],
                ['title' => 'Low Stock Alert', 'route' => 'lowstock.index'],
                // ['title' => 'Low Stock Analytics', 'route' => 'lowstock.analytics'],
                ['title' => 'Web-Site Order', 'route' => 'userorder', 'exclude_roles' => [3, 4],],
            ],
        ],

        [
            'type'  => 'dropdown',
            'key'   => 'wesiteMenu',
            'title' => 'Website Management',
            'icon'  => 'bx bx-store',
            'roles' => [1],
            'children' => [
                ['title' => 'Banner Management', 'route' => 'banners.index'],
                ['title' => 'User Contact Details', 'route' => 'admin.contacts'],
                ['title' => 'About Us', 'route' => 'admin.aboutus'],
            ],
        ],

    ],

];
