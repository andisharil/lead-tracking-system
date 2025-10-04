<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed lookup tables first
        $this->call([
            LocationSeeder::class,
            SourceSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            TeamUserSeeder::class,
        ]);

        // Optionally generate additional random users
        // User::factory(10)->create();
    }
}
