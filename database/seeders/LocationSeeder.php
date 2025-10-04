<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            'KL/Klang/Sg Buloh/Shah Alam',
            'Bangi/Kajang',
            'Kuantan',
            'Johor Bahru',
            'Seremban',
            'Kota Bharu',
            'Ipoh',
            'Kuala Terengganu',
            'Melaka',
            'Batu Pahat',
            'Perai'
        ];

        foreach ($locations as $location) {
            Location::firstOrCreate(['name' => $location]);
        }
    }
}
