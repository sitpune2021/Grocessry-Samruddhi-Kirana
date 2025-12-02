<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'first_name' => 'Anuradha',
            'last_name' => 'Jamdade',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Admin@123'),
            'role'=> 'Super Admin',
            'mobile'=>9503654539
             

        ]);
    }
}
