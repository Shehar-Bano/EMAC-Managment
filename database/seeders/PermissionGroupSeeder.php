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
                'name' => 'Region Management',
                'slug' => 'regions',
                'icon' => 'globe-americas',
                'description' => 'Manage operational regional territories and localized currencies',
                'sort_order' => 5,
            ],
            [
                'name' => 'Regional Service Pricing',
                'slug' => 'regional_prices',
                'icon' => 'currency-dollar',
                'description' => 'Configure localized base pricing per region, category, and subcategory',
                'sort_order' => 6,
            ],
            [
                'name' => 'Inquiry & Lead Management',
                'slug' => 'inquiries',
                'icon' => 'chat-bubble-left-right',
                'description' => 'Manage incoming customer service inquiries, quote requests, and leads',
                'sort_order' => 7,
            ],
            [
                'name' => 'Customer Service Requests',
                'slug' => 'service_requests',
                'icon' => 'clipboard-document-list',
                'description' => 'Manage customer service requests, attachments, and fulfillment workflows',
                'sort_order' => 8,
            ],
            [
                'name' => 'Quotes & Estimates',
                'slug' => 'quotes',
                'icon' => 'document-chart-bar',
                'description' => 'Generate, review, send, and manage customer quotes and pricing breakdowns',
                'sort_order' => 9,
            ],
            [
                'name' => 'Legal & Compliance',
                'slug' => 'legal',
                'icon' => 'document-text',
                'description' => 'Manage Terms and Conditions and Privacy Policy documentation',
                'sort_order' => 10,
            ],
            [
                'name' => 'System Settings',
                'slug' => 'settings',
                'icon' => 'cog-6-tooth',
                'description' => 'Configure global system parameters, company identity, and security policies',
                'sort_order' => 11,
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
