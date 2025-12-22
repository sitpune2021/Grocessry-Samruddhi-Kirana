<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TalukaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('talukas')->insert([

            // ================= MAHARASHTRA =================
            // Ahmednagar District
            ['district_id' => 1, 'name' => 'Ahmednagar', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 1, 'name' => 'Shrirampur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 1, 'name' => 'Sangamner', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 1, 'name' => 'Rahata', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 1, 'name' => 'Parner', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 1, 'name' => 'Pathardi', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 1, 'name' => 'Karjat', 'created_at' => now(), 'updated_at' => now()],

            // Akola
            ['district_id' => 2, 'name' => 'Akola', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 2, 'name' => 'Balapur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 2, 'name' => 'Patur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 2, 'name' => 'Telhara', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 2, 'name' => 'Murtizapur', 'created_at' => now(), 'updated_at' => now()],


            // Amravati
            ['district_id' => 3, 'name' => 'Amravati', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 3, 'name' => 'Achalpur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 3, 'name' => 'Chandur Railway', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 3, 'name' => 'Daryapur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 3, 'name' => 'Morshi', 'created_at' => now(), 'updated_at' => now()],


            // Aurangabad

            // Pune
            ['district_id' => 26, 'name' => 'Pune City', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 26, 'name' => 'Haveli', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 26, 'name' => 'Mulshi', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 26, 'name' => 'Baramati', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 26, 'name' => 'Indapur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 26, 'name' => 'Daund', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 26, 'name' => 'Junnar', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 26, 'name' => 'Ambegaon', 'created_at' => now(), 'updated_at' => now()],

            // Mumbai city
            ['district_id' => 17, 'name' => 'Mumbai City', 'created_at' => now(), 'updated_at' => now()],


            // Thane
            ['district_id' => 34, 'name' => 'Thane', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 34, 'name' => 'Kalyan', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 34, 'name' => 'Ulhasnagar', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 34, 'name' => 'Bhiwandi', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 34, 'name' => 'Murbad', 'created_at' => now(), 'updated_at' => now()],


        ]);
    }
}
