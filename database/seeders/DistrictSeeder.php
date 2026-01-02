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
            ['state_id'=>1,'name' => 'Ahilyanagar',     'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Akola',           'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Amravati',        'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Chhatrapati Sambhajinagar',      'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Beed',            'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Bhandara',        'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Buldhana',        'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Chandrapur',      'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Dhule',           'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Gadchiroli',      'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Gondia',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Hingoli',         'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Jalgaon',         'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Jalna',           'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Kolhapur',        'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Latur',           'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Mumbai City',     'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Mumbai Suburban', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Nagpur',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Nanded',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Nandurbar',       'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Nashik',           'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Dharashiv',       'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Palghar',         'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Parbhani',        'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Pune',            'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Raigad',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Ratnagiri',       'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Sangli',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Satara',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Sindhudurg',      'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Solapur',         'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Thane',           'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Wardha',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Washim',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['state_id'=>1,'name' => 'Yavatmal',        'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
