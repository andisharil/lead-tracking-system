<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'manage_users', 'description' => 'Create, edit, and delete users'],
            ['name' => 'manage_roles', 'description' => 'Create, edit, and delete roles and assign permissions'],
            ['name' => 'view_reports', 'description' => 'View reporting dashboards and exports'],
            ['name' => 'edit_campaigns', 'description' => 'Create and edit campaigns'],
            ['name' => 'view_leads', 'description' => 'View leads and basic details'],
            ['name' => 'edit_leads', 'description' => 'Create, edit, and delete leads'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name']],
                ['description' => $perm['description']]
            );
        }
    }
}