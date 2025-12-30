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
            ['name' => 'Ahilyanagar',     'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Akola',           'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Amravati',        'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Chhatrapati Sambhajinagar',      'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Beed',            'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Bhandara',        'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Buldhana',        'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Chandrapur',      'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Dhule',           'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Gadchiroli',      'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Gondia',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Hingoli',         'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Jalgaon',         'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Jalna',           'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Kolhapur',        'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Latur',           'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Mumbai City',     'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Mumbai Suburban', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Nagpur',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Nanded',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Nandurbar',       'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Nashik',           'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Dharashiv',       'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Palghar',         'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Parbhani',        'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Pune',            'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Raigad',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Ratnagiri',       'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Sangli',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Satara',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Sindhudurg',      'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Solapur',         'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Thane',           'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Wardha',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Washim',          'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Yavatmal',        'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}
