<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RolePermission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear old permissions (optional but recommended)
        RolePermission::truncate();

        $modules = [
            'Dashboard',
            'Roles',
            'User',
            'RolePermission',
            'Brands',
            'Category',
            'Product',
            'Batches',
            'Warehouse',
            'AddStock',
            'Sale',
            'Warehousetransfer'
        ];

        /**
         * ==========================
         * ROLE ID 1 → SUPER ADMIN
         * ==========================
         * Full access to everything
         */
        foreach ($modules as $module) {
            if ($module === 'Dashboard') {
                RolePermission::create([
                    'role_id' => 1,
                    'module'  => 'Dashboard',
                    'permissions' => json_encode([
                        'view_total_users',
                        'view_total_products',
                        'view_total_sales',
                        'view_total_stock'
                    ])
                ]);
            } else {
                RolePermission::create([
                    'role_id' => 1,
                    'module'  => $module,
                    'permissions' => json_encode(['add', 'view', 'edit', 'delete'])
                ]);
            }
        }

        /**
         * ==========================
         * ROLE ID 2 → ADMIN
         * ==========================
         * Same as Super Admin (as per your JS logic)
         */
        foreach ($modules as $module) {
            RolePermission::create([
                'role_id' => 2,
                'module'  => $module,
                'permissions' => json_encode(['add', 'view', 'edit', 'delete'])
            ]);
        }

        /**
         * ==========================
         * ROLE ID 3 → STAFF / USER
         * ==========================
         * Limited permissions
         */
        $staffPermissions = [
            'Dashboard' => ['view_total_products', 'view_total_stock'],
            'Product'   => ['view'],
            'Batches'   => ['view'],
            'Warehouse' => ['view'],
            'Sale'      => ['add', 'view']
        ];

        foreach ($staffPermissions as $module => $actions) {
            RolePermission::create([
                'role_id' => 3,
                'module'  => $module,
                'permissions' => json_encode($actions)
            ]);
        }
    }
}
