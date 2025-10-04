<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class TeamUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'company' => 'Acme Inc',
                'position' => 'Administrator',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Manager User',
                'email' => 'manager@example.com',
                'company' => 'Acme Inc',
                'position' => 'Marketing Manager',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Staff User',
                'email' => 'staff@example.com',
                'company' => 'Acme Inc',
                'position' => 'Sales Associate',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'company' => $data['company'] ?? null,
                    'position' => $data['position'] ?? null,
                    'password' => $data['password'],
                    'email_verified_at' => now(),
                ]
            );

            // Assign role based on email
            $roleName = str_contains($data['email'], 'admin') ? 'Admin' : (str_contains($data['email'], 'manager') ? 'Manager' : 'Staff');
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $user->roles()->syncWithoutDetaching([$role->id]);
            }
        }
    }
}