<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lead;
use App\Models\Location;
use App\Models\Source;
use Carbon\Carbon;

class LeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = Location::all();
        $sources = Source::all();
        
        if ($locations->isEmpty() || $sources->isEmpty()) {
            $this->command->info('Please run LocationSeeder and SourceSeeder first.');
            return;
        }

        $statuses = ['new', 'successful', 'lost'];
        $names = [
            'John Doe', 'Jane Smith', 'Michael Johnson', 'Sarah Wilson', 'David Brown',
            'Emily Davis', 'Robert Miller', 'Lisa Anderson', 'William Taylor', 'Jennifer Thomas',
            'Christopher Jackson', 'Amanda White', 'Matthew Harris', 'Ashley Martin', 'Daniel Thompson',
            'Jessica Garcia', 'Anthony Martinez', 'Melissa Robinson', 'Mark Clark', 'Stephanie Rodriguez',
            'Paul Lewis', 'Nicole Lee', 'Steven Walker', 'Kimberly Hall', 'Kenneth Allen',
            'Donna Young', 'Joshua Hernandez', 'Carol King', 'Kevin Wright', 'Sharon Lopez'
        ];

        // Create leads for the past 60 days
        for ($i = 0; $i < 150; $i++) {
            $status = $statuses[array_rand($statuses)];
            $createdAt = Carbon::now()->subDays(rand(0, 60));
            
            // Assign value only to successful leads
            $value = null;
            if ($status === 'successful') {
                $value = rand(500, 5000); // Random value between $500-$5000
            }
            
            Lead::create([
                'name' => $names[array_rand($names)] . ' ' . rand(1, 999),
                'phone' => '+60' . rand(100000000, 199999999),
                'location_id' => $locations->random()->id,
                'source_id' => $sources->random()->id,
                'status' => $status,
                'value' => $value,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'closed_at' => in_array($status, ['successful', 'lost']) ? $createdAt->copy()->addDays(rand(1, 7)) : null,
            ]);
        }

        // Create targeted mock leads for August 2025
        $start = Carbon::create(2025, 8, 1)->startOfDay();
        $end = Carbon::create(2025, 8, 31)->endOfDay();

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dailyCount = rand(8, 20); // realistic daily leads volume

            for ($j = 0; $j < $dailyCount; $j++) {
                $status = $statuses[array_rand($statuses)];
                $createdAt = $date->copy()->setTime(rand(8, 20), rand(0, 59)); // within business hours

                $value = null;
                if ($status === 'successful') {
                    $value = rand(500, 5000);
                }

                Lead::create([
                    'name' => $names[array_rand($names)] . ' ' . rand(1, 999),
                    'phone' => '+60' . rand(100000000, 199999999),
                    'location_id' => $locations->random()->id,
                    'source_id' => $sources->random()->id,
                    'status' => $status,
                    'value' => $value,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                    'closed_at' => in_array($status, ['successful', 'lost']) ? $createdAt->copy()->addDays(rand(1, 7)) : null,
                ]);
            }
        }

        // Create targeted mock leads for September 1 - October 4, 2025
        $start = Carbon::create(2025, 9, 1)->startOfDay();
        $end = Carbon::create(2025, 10, 4)->endOfDay();

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dailyCount = rand(8, 20); // realistic daily leads volume

            for ($j = 0; $j < $dailyCount; $j++) {
                $status = $statuses[array_rand($statuses)];
                $createdAt = $date->copy()->setTime(rand(8, 20), rand(0, 59)); // within business hours

                $value = null;
                if ($status === 'successful') {
                    $value = rand(500, 5000);
                }

                Lead::create([
                    'name' => $names[array_rand($names)] . ' ' . rand(1, 999),
                    'phone' => '+60' . rand(100000000, 199999999),
                    'location_id' => $locations->random()->id,
                    'source_id' => $sources->random()->id,
                    'status' => $status,
                    'value' => $value,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                    'closed_at' => in_array($status, ['successful', 'lost']) ? $createdAt->copy()->addDays(rand(1, 7)) : null,
                ]);
            }
        }

        $this->command->info('Created 150 sample leads, plus targeted August 2025 and September-October 2025 test leads.');
    }
}