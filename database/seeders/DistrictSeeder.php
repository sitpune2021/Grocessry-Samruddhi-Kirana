<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('districts')->insert([
            ['state_id'=>14,'name' => 'Ahilyanagar',     'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Akola',           'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Amravati',        'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Chhatrapati Sambhajinagar',      'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Beed',            'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Bhandara',        'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Buldhana',        'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Chandrapur',      'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Dhule',           'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Gadchiroli',      'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Gondia',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Hingoli',         'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Jalgaon',         'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Jalna',           'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Kolhapur',        'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Latur',           'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Mumbai City',     'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Mumbai Suburban', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Nagpur',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Nanded',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Nandurbar',       'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Nashik',           'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Dharashiv',       'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Palghar',         'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Parbhani',        'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Pune',            'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Raigad',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Ratnagiri',       'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Sangli',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Satara',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Sindhudurg',      'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Solapur',         'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Thane',           'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Wardha',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Washim',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>14,'name' => 'Yavatmal',        'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
