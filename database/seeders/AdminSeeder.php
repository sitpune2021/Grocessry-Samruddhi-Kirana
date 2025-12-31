<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\Warehouse;

class AdminSeeder extends Seeder
{
    public function run()
    {

        $warehouse = Warehouse::create([
            'name'        => 'Samruddh Kirana Main Warehouse',
            'country_id'      => 1,
            'state_id'        => 14,
            'district_id'     => 317,
            'taluka_id'       => 2,
             'type'            => 'Master',
              'code'            => '422605',
              'address'         => 'sangamner, maharashtra, india',
            'status'      => 'active',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        User::create([
            'first_name' => 'Samruddh',
            'last_name' => 'Kirana',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Admin@123'),
            'role_id' => 1,
            'mobile' => 9503654539,
            'warehouse_id' => $warehouse->id
        ]);
    }
}
