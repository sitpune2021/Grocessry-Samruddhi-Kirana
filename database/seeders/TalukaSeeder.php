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

            // Ahmednagar District 14
            ['district_id' => 1, 'name' => 'Ahilyanagar', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 1, 'name' => 'Shrirampur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 1, 'name' => 'Sangamner', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 1, 'name' => 'Rahata', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 1, 'name' => 'Parner', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 1, 'name' => 'Pathardi', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 1, 'name' => 'Karjat', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 1, 'name' => 'Akole', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 1, 'name' => 'Jamkhed', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 1, 'name' => 'Kopargaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 1, 'name' => 'Nevasa', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 1, 'name' => 'Rahuri', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 1, 'name' => 'Shevgaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 1, 'name' => 'Shrigonda', 'created_at' => now(), 'updated_at' => now()],

            // Akola 7 
            ['district_id' => 2, 'name' => 'Akola', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 2, 'name' => 'Balapur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 2, 'name' => 'Patur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 2, 'name' => 'Telhara', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 2, 'name' => 'Murtizapur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 2, 'name' => 'Barshi Takli', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 2, 'name' => 'Akot', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 2, 'name' => 'Telhara', 'created_at' => now(), 'updated_at' => now()],

            // Amravati 14
            ['district_id' => 3, 'name' => 'Amravati', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 3, 'name' => 'Achalpur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 3, 'name' => 'Anjangaon-Surji', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 3, 'name' => 'Bhatkuli', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 3, 'name' => 'Chandur Bazaar', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 3, 'name' => 'Chandur Railway', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 3, 'name' => 'Chikhaldara', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 3, 'name' => 'Daryapur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 3, 'name' => 'Dhamangaon Railway', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 3, 'name' => 'Dharni', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 3, 'name' => 'Morshi', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 3, 'name' => 'Nandgaon-Khandeshwar', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 3, 'name' => 'Tivsa', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 3, 'name' => 'Warud', 'created_at' => now(), 'updated_at' => now()],

            // Chhatrapati Sambhajinagar 9
            ['district_id' => 4, 'name' => 'Chhatrapati Sambhajinagar', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 4, 'name' => 'Kannad', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 4, 'name' => 'Gangapur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 4, 'name' => 'Khuldabad', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 4, 'name' => 'Soygaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 4, 'name' => 'Sillod', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 4, 'name' => 'Phulambri', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 4, 'name' => 'Vaijapur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 4, 'name' => 'Paithan', 'created_at' => now(), 'updated_at' => now()],

            // Beed 11
            ['district_id' => 5, 'name' => 'Beed', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 5, 'name' => 'Gevrai', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 5, 'name' => 'Majalgaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 5, 'name' => 'Dharur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 5, 'name' => 'Wadwani', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 5, 'name' => 'Parli', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 5, 'name' => 'Patoda', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 5, 'name' => 'Asthi', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 5, 'name' => 'Shirur (Kasar)', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 5, 'name' => 'Ambajogai', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 5, 'name' => 'Kaij', 'created_at' => now(), 'updated_at' => now()],

            //Bhandara 7
            ['district_id' => 6, 'name' => 'Bhandara', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 6, 'name' => 'Mohadi', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 6, 'name' => 'Tumsar', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 6, 'name' => 'Lakhani', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 6, 'name' => 'Sakoli', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 6, 'name' => 'Pauni', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 6, 'name' => 'Lakhandur', 'created_at' => now(), 'updated_at' => now()],

            //Buldhana 13
            ['district_id' => 7, 'name' => 'Buldhana', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 7, 'name' => 'Chikhli', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 7, 'name' => 'Deulgaon Raja', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 7, 'name' => 'Sindkhed Raja', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 7, 'name' => 'Lonar', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 7, 'name' => 'Khamgaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 7, 'name' => 'Shegaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 7, 'name' => 'Sangrampur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 7, 'name' => 'Jalgaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 7, 'name' => 'Nandura', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 7, 'name' => 'Malkapur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 7, 'name' => 'Motala', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 7, 'name' => 'Mehkar', 'created_at' => now(), 'updated_at' => now()],

            // Chandrapur 15
            ['district_id' => 8, 'name' => 'Chandrapur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 8, 'name' => 'Ballarpur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 8, 'name' => 'Warora', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 8, 'name' => 'Bhadravati', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 8, 'name' => 'Brahmapuri', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 8, 'name' => 'Chimur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 8, 'name' => 'Gondpipari', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 8, 'name' => 'Korpana', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 8, 'name' => 'Rajura', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 8, 'name' => 'Jivati', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 8, 'name' => 'Nagbhid', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 8, 'name' => 'Sawli', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 8, 'name' => 'Sindewahi', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 8, 'name' => 'Mul', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 8, 'name' => 'Pobhurna', 'created_at' => now(), 'updated_at' => now()],

            //Dhule 4 
            ['district_id' => 9, 'name' => 'Dhule', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 9, 'name' => 'Sakri', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 9, 'name' => 'Shindkheda', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 9, 'name' => 'Shirpur', 'created_at' => now(), 'updated_at' => now()],

            //Gadchiroli 12 
            ['district_id' => 10, 'name' => 'Gadchiroli', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 10, 'name' => 'Dhanora', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 10, 'name' => 'Chamorshi', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 10, 'name' => 'Mulchera', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 10, 'name' => 'Aheri', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 10, 'name' => 'Sironcha', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 10, 'name' => 'Etapalli', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 10, 'name' => 'Bhamragad', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 10, 'name' => 'Armori', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 10, 'name' => 'Kurkheda', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 10, 'name' => 'Korchi', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 10, 'name' => 'Shindkheda', 'created_at' => now(), 'updated_at' => now()],

            //Gondia 8
            ['district_id' => 11, 'name' => 'Gondia', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 11, 'name' => 'Amgaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 11, 'name' => 'Morgaon Arjuni', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 11, 'name' => 'Goregaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 11, 'name' => 'Tiroda', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 11, 'name' => 'Deori', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 11, 'name' => 'Salekasa', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 11, 'name' => 'Sadak Arjuni', 'created_at' => now(), 'updated_at' => now()],

            //Hingoli 5
            ['district_id' => 12, 'name' => 'Hingoli', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 12, 'name' => 'Basmath', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 12, 'name' => 'Kalamnuri', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 12, 'name' => 'Aundha Nagnath', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 12, 'name' => 'Sengaon', 'created_at' => now(), 'updated_at' => now()],

            //Jalgaon 15
            ['district_id' => 13, 'name' => 'Jalgaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 13, 'name' => 'Bhusawal', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 13, 'name' => 'Yaval', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 13, 'name' => 'Raver', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 13, 'name' => 'Muktainagar', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 13, 'name' => 'Amalner', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 13, 'name' => 'Chopda', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 13, 'name' => 'Erandol', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 13, 'name' => 'Parola', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 13, 'name' => 'Chalisgaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 13, 'name' => 'Jamner', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 13, 'name' => 'Pachora', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 13, 'name' => 'Bhadgaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 13, 'name' => 'Dharangaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 13, 'name' => 'Bodwad', 'created_at' => now(), 'updated_at' => now()],

            //Jalna 8
            ['district_id' => 14, 'name' => 'Jalna', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 14, 'name' => 'Ambad', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 14, 'name' => 'Partur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 14, 'name' => 'Bhokardan', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 14, 'name' => 'Jafrabad', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 14, 'name' => 'Badnapur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 14, 'name' => 'Ghansavangi', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 14, 'name' => 'Mantha', 'created_at' => now(), 'updated_at' => now()],

            //Kolhapur 12
            ['district_id' => 15, 'name' => 'Karveer', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 15, 'name' => 'Panhala', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 15, 'name' => 'Shahuwadi', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 15, 'name' => 'Kagal', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 15, 'name' => 'Hatkanangle', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 15, 'name' => 'Shirol', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 15, 'name' => 'Gadhinglaj', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 15, 'name' => 'Chandgad', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 15, 'name' => 'Ajra', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 15, 'name' => 'Bhudargad', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 15, 'name' => 'Radhanagari', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 15, 'name' => 'Gagan Bavda ', 'created_at' => now(), 'updated_at' => now()],

            //Latur 10
            ['district_id' => 16, 'name' => 'Latur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 16, 'name' => 'Ausa', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 16, 'name' => 'Renapur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 16, 'name' => 'Deoni', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 16, 'name' => 'Nilanga', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 16, 'name' => 'Udgir', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 16, 'name' => 'Ahmedpur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 16, 'name' => 'Jalkot', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 16, 'name' => 'Shirur Anantpal', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 16, 'name' => 'Chakur', 'created_at' => now(), 'updated_at' => now()],

            //Mumbai City 3
            ['district_id' => 17, 'name' => 'Andheri', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 17, 'name' => 'Borivali', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 17, 'name' => 'Kurla', 'created_at' => now(), 'updated_at' => now()],

            //Nagpur 14
            ['district_id' => 19, 'name' => 'Ramtek', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 19, 'name' => 'Umred', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 19, 'name' => 'Kalameshwar', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 19, 'name' => 'Katol', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 19, 'name' => 'Kamptee', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 19, 'name' => 'Kuhi', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 19, 'name' => 'Narkhed', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 19, 'name' => 'Nagpur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 19, 'name' => 'Parseoni', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 19, 'name' => 'Bhiwapur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 19, 'name' => 'Mouda', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 19, 'name' => 'Savner', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 19, 'name' => 'Hingna', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 19, 'name' => 'Samudrapur', 'created_at' => now(), 'updated_at' => now()],

            //Nanded 16
            ['district_id' => 20, 'name' => 'Ardhapur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 20, 'name' => 'Bhokar', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 20, 'name' => 'Biloli', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 20, 'name' => 'Deglur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 20, 'name' => 'Dharmabad', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 20, 'name' => 'Hadgaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 20, 'name' => 'Kinwat', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 20, 'name' => 'Himayatnagar', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 20, 'name' => 'Kandhar', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 20, 'name' => 'Mukhed', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 20, 'name' => 'Loha', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 20, 'name' => 'Mahur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 20, 'name' => 'Mudkhed', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 20, 'name' => 'Nanded', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 20, 'name' => 'Naigaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 20, 'name' => 'Umri ', 'created_at' => now(), 'updated_at' => now()],

            //Nandurbar 6
            ['district_id' => 22, 'name' => 'Akkalkuwa', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 22, 'name' => 'Akrani Mahal (Dhadgaon)', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 22, 'name' => 'Taloda', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 22, 'name' => 'Shahada', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 22, 'name' => 'Nandurbar', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 22, 'name' => 'Navapur', 'created_at' => now(), 'updated_at' => now()],

            //Nashik 15
            ['district_id' => 23, 'name' => 'Nashik', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 23, 'name' => 'Igatpuri', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 23, 'name' => 'Trimbakeshwar', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 23, 'name' => 'Dindori', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 23, 'name' => 'Peth', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 23, 'name' => 'Kalwan', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 23, 'name' => 'Surgana', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 23, 'name' => 'Chandwad', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 23, 'name' => 'Deola', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 23, 'name' => 'Baglan (Satana)', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 23, 'name' => 'Malegaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 23, 'name' => 'Nandgaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 23, 'name' => 'Yeola', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 23, 'name' => 'Niphad', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 23, 'name' => 'Sinnar', 'created_at' => now(), 'updated_at' => now()],

            //Dharashiv 8
            ['district_id' => 23, 'name' => 'Dharashiv', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 23, 'name' => 'Tuljapur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 23, 'name' => 'Umarga', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 23, 'name' => 'Lohara', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 23, 'name' => 'Kalamb', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 23, 'name' => 'Bhum', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 23, 'name' => 'Paranda', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 23, 'name' => 'Washi', 'created_at' => now(), 'updated_at' => now()],

            //Palghar 8
            ['district_id' => 24, 'name' => 'Palghar', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 24, 'name' => 'Vasai', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 24, 'name' => 'Dahanu', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 24, 'name' => 'Talasari', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 24, 'name' => 'Jawhar', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 24, 'name' => 'Mokhada', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 24, 'name' => 'Vikramgad', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 24, 'name' => 'Wada', 'created_at' => now(), 'updated_at' => now()],

            //Parbhani 9
            ['district_id' => 25, 'name' => 'Parbhani', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 25, 'name' => 'Purna', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 25, 'name' => 'Palam', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 25, 'name' => 'Gangakhed', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 25, 'name' => 'Sonpeth', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 25, 'name' => 'Sailu', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 25, 'name' => 'Pathri', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 25, 'name' => 'Manwath', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 25, 'name' => 'Jintur', 'created_at' => now(), 'updated_at' => now()],

            // Pune 14
            ['district_id' => 26, 'name' => 'Pune City', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 26, 'name' => 'Haveli', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 26, 'name' => 'Mulshi', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 26, 'name' => 'Baramati', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 26, 'name' => 'Indapur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 26, 'name' => 'Daund', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 26, 'name' => 'Junnar', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 26, 'name' => 'Ambegaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 26, 'name' => 'Khed', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 26, 'name' => 'Maval', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 26, 'name' => 'Shirur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 26, 'name' => 'Purandhar', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 26, 'name' => 'Bhor', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 26, 'name' => 'Velhe', 'created_at' => now(), 'updated_at' => now()],

            //Raigad 15
            ['district_id' => 27, 'name' => 'Alibag', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 27, 'name' => 'Panvel', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 27, 'name' => 'Murud', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 27, 'name' => 'Pen', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 27, 'name' => 'Uran', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 27, 'name' => 'Karjat', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 27, 'name' => 'Khalapur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 27, 'name' => 'Mangaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 27, 'name' => 'Roha', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 27, 'name' => 'Sudhagad', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 27, 'name' => 'Tala', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 27, 'name' => 'Mahad', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 27, 'name' => 'Mhasala', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 27, 'name' => 'Shrivardhan', 'created_at' => now(), 'updated_at' => now()],

            //Ratnagiri 9
            ['district_id' => 28, 'name' => 'Mandangad', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 28, 'name' => 'Khed', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 28, 'name' => 'Dapoli', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 28, 'name' => 'Guhagar', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 28, 'name' => 'Chiplun', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 28, 'name' => 'Sangameshwar', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 28, 'name' => 'Ratnagiri', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 28, 'name' => 'Lanja', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 28, 'name' => 'Rajapur', 'created_at' => now(), 'updated_at' => now()],

            //Sangli 10
            ['district_id' => 29, 'name' => 'Miraj', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 29, 'name' => 'Palus', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 29, 'name' => 'Tasgaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 29, 'name' => 'Kavathe-Mahankal', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 29, 'name' => 'Jat', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 29, 'name' => 'Khanapur (Vita)', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 29, 'name' => 'Atpadi', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 29, 'name' => 'Walwa (Islampur)', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 29, 'name' => 'Kadegaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 29, 'name' => 'Shirala', 'created_at' => now(), 'updated_at' => now()],


            //Satara 11
            ['district_id' => 30, 'name' => 'Satara', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 30, 'name' => 'Karad', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 30, 'name' => 'Wai', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 30, 'name' => 'Mahabaleshwar', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 30, 'name' => 'Phaltan', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 30, 'name' => 'Man', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 30, 'name' => 'Khatav', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 30, 'name' => 'Koregaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 30, 'name' => 'Patan', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 30, 'name' => 'Jaoli', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 30, 'name' => 'Khandala', 'created_at' => now(), 'updated_at' => now()],


            //Sindhudurg 8 
            ['district_id' => 31, 'name' => 'Sawantwadi', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 31, 'name' => 'Kudal', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 31, 'name' => 'Vengurla', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 31, 'name' => 'Malvan', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 31, 'name' => 'Devgad', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 31, 'name' => 'Kankavli', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 31, 'name' => 'Vaibhavwadi', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 31, 'name' => 'Dodamarg', 'created_at' => now(), 'updated_at' => now()],

            //Solapur 11 
            ['district_id' => 32, 'name' => 'North Solapur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 32, 'name' => 'South Solapur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 32, 'name' => 'Karmala', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 32, 'name' => 'Akkalkot', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 32, 'name' => 'Barshi', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 32, 'name' => 'Mangalwedha', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 32, 'name' => 'Pandharpur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 32, 'name' => 'Sangola', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 32, 'name' => 'Malshiras', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 32, 'name' => 'Mohol', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 32, 'name' => 'Madha', 'created_at' => now(), 'updated_at' => now()],

            //Thane 7 
            ['district_id' => 33, 'name' => 'Thane', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 33, 'name' => 'Kalyan', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 33, 'name' => 'Bhiwandi', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 33, 'name' => 'Shahapur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 33, 'name' => 'Murbad', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 33, 'name' => 'Ambernath', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 33, 'name' => 'Ulhasnagar', 'created_at' => now(), 'updated_at' => now()],

            //Wardha 8
            ['district_id' => 34, 'name' => 'Arvi', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 34, 'name' => 'Ashti', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 34, 'name' => 'Deoli', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 34, 'name' => 'Hinganghat', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 34, 'name' => 'Karanja', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 34, 'name' => 'Samudrapur', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 34, 'name' => 'Seloo', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 34, 'name' => 'Seloo', 'created_at' => now(), 'updated_at' => now()],

            //Washim 6 
            ['district_id' => 35, 'name' => 'Washim', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 35, 'name' => 'Malegaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 35, 'name' => 'Risod', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 35, 'name' => 'Mangrulpir', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 35, 'name' => 'Karanja', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 35, 'name' => 'Manora', 'created_at' => now(), 'updated_at' => now()],

            //Yavatmal 16 
            ['district_id' => 36, 'name' => 'Yavatmal', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 36, 'name' => 'Wani', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 36, 'name' => 'Pusad', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 36, 'name' => 'Umarkhed', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 36, 'name' => 'Darwha', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 36, 'name' => 'Digras', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 36, 'name' => 'Arni', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 36, 'name' => 'Kalamb', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 36, 'name' => 'Babulgaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 36, 'name' => 'Ralegaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 36, 'name' => 'Maregaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 36, 'name' => 'Ghatanji', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 36, 'name' => 'Ner', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 36, 'name' => 'Mahagaon', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 36, 'name' => 'Zari Jamani', 'created_at' => now(), 'updated_at' => now()],
            ['district_id' => 36, 'name' => 'Pandharkawda/Kelapur', 'created_at' => now(), 'updated_at' => now()],


        ]);
    }
}
