<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Source;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            'TikTok',
            'Facebook',
            'Instagram',
            'WhatsApp',
            'Website'
        ];

        foreach ($sources as $source) {
            Source::firstOrCreate(['name' => $source]);
        }
    }
}
