<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'name' => 'Super Admin',
                'description' => 'Full system access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin',
                'description' => 'Administrative access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Warehouse',
                'description' => 'Manages warehouse operations',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Master Godown',
                'description' => 'Oversees stock in the godown',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Retailer',
                'description' => 'Retail role',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Delivery Agent',
                'description' => 'Handles deliveries',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Customer',
                'description' => 'Application customer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Delivery Boy',
                'description' => 'Last-mile delivery',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
