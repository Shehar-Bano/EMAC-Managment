<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allPermissions = Permission::all();

        // 1. Super Admin
        $superAdmin = Role::updateOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Unrestricted root administrator with complete platform access and governance',
                'is_system' => true,
            ]
        );
        $superAdmin->permissions()->sync($allPermissions->pluck('id'));

        // 2. Technician
        $technician = Role::updateOrCreate(
            ['slug' => 'technician'],
            [
                'name' => 'Technician',
                'description' => 'Operations and field technician with directory and task execution privileges',
                'is_system' => false,
            ]
        );
        $technicianPermissions = $allPermissions->filter(function ($p) {
            return in_array($p->name, [
                'users.view',
                'users.edit',
                'users.status',
                'categories.view',
                'subcategories.view',
            ]);
        });
        $technician->permissions()->sync($technicianPermissions->pluck('id'));

        // 3. Customer
        $customer = Role::updateOrCreate(
            ['slug' => 'customer'],
            [
                'name' => 'Customer',
                'description' => 'Client / Customer account with access to account profile and customer services',
                'is_system' => false,
            ]
        );
        $customerPermissions = $allPermissions->filter(function ($p) {
            return in_array($p->name, [
                'users.view',
                'categories.view',
                'subcategories.view',
            ]);
        });
        $customer->permissions()->sync($customerPermissions->pluck('id'));

        // Remove old deprecated roles if they exist and re-assign
        $obsoleteRoles = Role::whereIn('slug', ['admin', 'manager', 'staff'])->get();
        foreach ($obsoleteRoles as $oldRole) {
            $oldRole->permissions()->detach();
            $oldRole->users()->detach();
            $oldRole->delete();
        }
    }
}
