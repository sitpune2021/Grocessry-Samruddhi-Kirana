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

            // Andhra Pradesh
            // ['district_id' => 1, 'name' => 'Anantapur'],
            // ['district_id' => 1, 'name' => 'Gooty'],
            // ['district_id' => 1, 'name' => 'Tadipatri'],
            // ['district_id' => 1, 'name' => 'Dharmavaram'],
            // ['district_id' => 1, 'name' => 'Kadiri'],
            // ['district_id' => 1, 'name' => 'Penukonda'],
            // ['district_id' => 1, 'name' => 'Kalyandurg'],
            // ['district_id' => 1, 'name' => 'Rayadurg'],

            // ['district_id' => 2, 'name' => 'Chittoor'],
            // ['district_id' => 2, 'name' => 'Tirupati'],
            // ['district_id' => 2, 'name' => 'Madanapalle'],
            // ['district_id' => 2, 'name' => 'Puttur'],
            // ['district_id' => 2, 'name' => 'Nagari'],
            // ['district_id' => 2, 'name' => 'Palamaner'],
            // ['district_id' => 2, 'name' => 'Srikalahasti'],
            // ['district_id' => 3, 'name' => 'Kakinada'],
            // ['district_id' => 3, 'name' => 'Rajahmundry'],
            // ['district_id' => 3, 'name' => 'Peddapuram'],
            // ['district_id' => 3, 'name' => 'Tuni'],
            // ['district_id' => 3, 'name' => 'Amalapuram'],
            // ['district_id' => 3, 'name' => 'Ramachandrapuram'],

            // ['district_id' => 4, 'name' => 'Guntur'],
            // ['district_id' => 4, 'name' => 'Tenali'],
            // ['district_id' => 4, 'name' => 'Mangalagiri'],
            // ['district_id' => 4, 'name' => 'Repalle'],
            // ['district_id' => 4, 'name' => 'Bapatla'],
            // ['district_id' => 4, 'name' => 'Sattenapalle'],

            // ['district_id' => 5, 'name' => 'Machilipatnam'],
            // ['district_id' => 5, 'name' => 'Vijayawada'],
            // ['district_id' => 5, 'name' => 'Nandigama'],
            // ['district_id' => 5, 'name' => 'Gudivada'],
            // ['district_id' => 5, 'name' => 'Jaggayyapeta'],

            // ['district_id' => 6, 'name' => 'Kurnool'],
            // ['district_id' => 6, 'name' => 'Adoni'],
            // ['district_id' => 6, 'name' => 'Nandyal'],
            // ['district_id' => 6, 'name' => 'Yemmiganur'],
            // ['district_id' => 6, 'name' => 'Dhone'],

            // ['district_id' => 7, 'name' => 'Ongole'],
            // ['district_id' => 7, 'name' => 'Chirala'],
            // ['district_id' => 7, 'name' => 'Markapur'],
            // ['district_id' => 7, 'name' => 'Kanigiri'],

            // ['district_id' => 8, 'name' => 'Srikakulam'],
            // ['district_id' => 8, 'name' => 'Amadalavalasa'],
            // ['district_id' => 8, 'name' => 'Palasa'],
            // ['district_id' => 8, 'name' => 'Ichchapuram'],

            // ['district_id' => 10, 'name' => 'Vizianagaram'],
            // ['district_id' => 10, 'name' => 'Bobbili'],
            // ['district_id' => 10, 'name' => 'Parvathipuram'],

            // ['district_id' => 11, 'name' => 'Eluru'],
            // ['district_id' => 11, 'name' => 'Bhimavaram'],
            // ['district_id' => 11, 'name' => 'Tadepalligudem'],
            // ['district_id' => 11, 'name' => 'Narsapuram'],

            // ['district_id' => 12, 'name' => 'Kadapa'],
            // ['district_id' => 12, 'name' => 'Proddatur'],
            // ['district_id' => 12, 'name' => 'Pulivendula'],
            // ['district_id' => 12, 'name' => 'Rajampet'],

            // ['district_id' => 13, 'name' => 'Tawang'],
            // ['district_id' => 13, 'name' => 'Lumla'],
            // ['district_id' => 13, 'name' => 'Zemithang'],
            // ['district_id' => 13, 'name' => 'Jang'],


            // ================= MAHARASHTRA =================
            // Ahmednagar District
            ['district_id' => 317, 'name' => 'Ahmednagar'],
            ['district_id' => 317, 'name' => 'Akole'],
            ['district_id' => 317, 'name' => 'Jamkhed'],
            ['district_id' => 317, 'name' => 'Karjat'],
            ['district_id' => 317, 'name' => 'Kopargaon'],
            ['district_id' => 317, 'name' => 'Newasa'],
            ['district_id' => 317, 'name' => 'Parner'],
            ['district_id' => 317, 'name' => 'Pathardi'],
            ['district_id' => 317, 'name' => 'Rahata'],
            ['district_id' => 317, 'name' => 'Rahuri'],
            ['district_id' => 317, 'name' => 'Sangamner'],
            ['district_id' => 317, 'name' => 'Shevgaon'],
            ['district_id' => 317, 'name' => 'Shrigonda'],
            ['district_id' => 317, 'name' => 'Shrirampur'],

            // Akola
            ['district_id' => 318, 'name' => 'Akola'],
            ['district_id' => 318, 'name' => 'Barshitakli'],
            ['district_id' => 318, 'name' => 'Balapur'],
            ['district_id' => 318, 'name' => 'Murum'],
            ['district_id' => 318, 'name' => 'Patur'],
            ['district_id' => 318, 'name' => 'Telhara'],

            // Amravati
            ['district_id' => 319, 'name' => 'Amravati'],
            ['district_id' => 319, 'name' => 'Achalpur'],
            ['district_id' => 319, 'name' => 'Chikhaldara'],
            ['district_id' => 319, 'name' => 'Daryapur'],
            ['district_id' => 319, 'name' => 'Anjangaon'],
            ['district_id' => 319, 'name' => 'Morshi'],
            ['district_id' => 319, 'name' => 'Chandur Bazar'],
            ['district_id' => 319, 'name' => 'Warud'],

            // Aurangabad
            ['district_id' => 320, 'name' => 'Aurangabad'],
            ['district_id' => 320, 'name' => 'Kannad'],
            ['district_id' => 320, 'name' => 'Vaijapur'],
            ['district_id' => 320, 'name' => 'Gangapur'],
            ['district_id' => 320, 'name' => 'Sillod'],
            ['district_id' => 320, 'name' => 'Paithan'],
            ['district_id' => 320, 'name' => 'Khuldabad'],

            ['district_id' => 321, 'name' => 'Beed'],
            ['district_id' => 321, 'name' => 'Ashti'],
            ['district_id' => 321, 'name' => 'Georai'],
            ['district_id' => 321, 'name' => 'Kaij'],
            ['district_id' => 321, 'name' => 'Parli'],
            ['district_id' => 321, 'name' => 'Ambajogai'],
            ['district_id' => 321, 'name' => 'Wadwani'],
            ['district_id' => 321, 'name' => 'Patoda'],
            ['district_id' => 321, 'name' => 'Majalgaon'],
            ['district_id' => 321, 'name' => 'Shirur (Ka)']

        ]);
    }
}
