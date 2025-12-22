<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        Country::firstOrCreate(
            ['name' => 'India'],
            ['code' => '+91']
        );
    }
    // public function run(): void
    // {
    //     DB::table('countries')->insert([
    //         [
    //             'name'       => 'India',
    //             'code'       => '+91',
    //             'created_at' => Carbon::now(),
    //             'updated_at' => Carbon::now(),
    //         ]
    //     ]);
    // }
}
