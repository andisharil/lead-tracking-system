<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Admin' => ['manage_users', 'manage_roles', 'view_reports', 'edit_campaigns', 'view_leads', 'edit_leads'],
            'Manager' => ['view_reports', 'edit_campaigns', 'view_leads', 'edit_leads'],
            'Staff' => ['view_leads', 'edit_leads'],
        ];

        foreach ($roles as $roleName => $permissionNames) {
            $role = Role::firstOrCreate(
                ['name' => $roleName],
                ['description' => $roleName . ' role']
            );

            $permissionIds = Permission::whereIn('name', $permissionNames)->pluck('id');
            if ($permissionIds->isNotEmpty()) {
                $role->permissions()->syncWithoutDetaching($permissionIds);
            }
        }
    }
}