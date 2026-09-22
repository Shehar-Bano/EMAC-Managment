<?php

namespace Database\Seeders;

use App\Models\PermissionGroup;
use Illuminate\Database\Seeder;

class PermissionGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            [
                'name' => 'User Management',
                'slug' => 'users',
                'icon' => 'users',
                'description' => 'Manage system staff, operators, and user profiles',
                'sort_order' => 1,
            ],
            [
                'name' => 'Role Management',
                'slug' => 'roles',
                'icon' => 'shield-check',
                'description' => 'Manage security roles and granular permission assignments',
                'sort_order' => 2,
            ],
            [
                'name' => 'Category Management',
                'slug' => 'categories',
                'icon' => 'folder',
                'description' => 'Manage operational categories, taxonomy, and service groupings',
                'sort_order' => 3,
            ],
            [
                'name' => 'Subcategory Management',
                'slug' => 'subcategories',
                'icon' => 'tag',
                'description' => 'Manage secondary subcategories linked to parent categories',
                'sort_order' => 4,
            ],
            [
                'name' => 'Inquiry & Lead Management',
                'slug' => 'inquiries',
                'icon' => 'chat-bubble-left-right',
                'description' => 'Manage incoming customer service inquiries, quote requests, and leads',
                'sort_order' => 5,
            ],
            [
                'name' => 'System Settings',
                'slug' => 'settings',
                'icon' => 'cog-6-tooth',
                'description' => 'Configure global system parameters, company identity, and security policies',
                'sort_order' => 6,
            ],
        ];

        foreach ($groups as $group) {
            PermissionGroup::updateOrCreate(
                ['slug' => $group['slug']],
                $group
            );
        }

        // Clean up any removed groups
        PermissionGroup::whereNotIn('slug', array_column($groups, 'slug'))->delete();
    }
}
