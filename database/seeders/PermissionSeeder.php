<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PermissionGroup;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // User Management
            [
                'group' => 'users',
                'name' => 'users.view',
                'label' => 'View Users',
                'description' => 'View staff directory and individual profile details',
            ],
            [
                'group' => 'users',
                'name' => 'users.create',
                'label' => 'Create User',
                'description' => 'Register new staff accounts and operators',
            ],
            [
                'group' => 'users',
                'name' => 'users.edit',
                'label' => 'Edit User',
                'description' => 'Modify staff user profiles and role assignments',
            ],
            [
                'group' => 'users',
                'name' => 'users.delete',
                'label' => 'Delete User',
                'description' => 'Delete a single user record',
            ],
            [
                'group' => 'users',
                'name' => 'users.bulk-delete',
                'label' => 'Bulk Delete Users',
                'description' => 'Bulk delete multiple selected staff members',
            ],
            [
                'group' => 'users',
                'name' => 'users.status',
                'label' => 'Change User Status',
                'description' => 'Toggle user status between active and inactive',
            ],
            [
                'group' => 'users',
                'name' => 'users.export',
                'label' => 'Export Users',
                'description' => 'Export users data to CSV file',
            ],

            // Role Management
            [
                'group' => 'roles',
                'name' => 'roles.view',
                'label' => 'View Roles',
                'description' => 'View roles and their assigned permission matrices',
            ],
            [
                'group' => 'roles',
                'name' => 'roles.create',
                'label' => 'Create Role',
                'description' => 'Create new security and operational roles',
            ],
            [
                'group' => 'roles',
                'name' => 'roles.edit',
                'label' => 'Edit Role',
                'description' => 'Update role details and granular permissions',
            ],
            [
                'group' => 'roles',
                'name' => 'roles.delete',
                'label' => 'Delete Role',
                'description' => 'Delete custom non-system roles',
            ],
            [
                'group' => 'roles',
                'name' => 'roles.assign',
                'label' => 'Assign Permissions',
                'description' => 'Grant or revoke permissions to roles',
            ],

            // Category Management
            [
                'group' => 'categories',
                'name' => 'categories.view',
                'label' => 'View Categories',
                'description' => 'View product and service categories catalog',
            ],
            [
                'group' => 'categories',
                'name' => 'categories.create',
                'label' => 'Create Category',
                'description' => 'Create new primary categories',
            ],
            [
                'group' => 'categories',
                'name' => 'categories.edit',
                'label' => 'Edit Category',
                'description' => 'Update category details and sort order',
            ],
            [
                'group' => 'categories',
                'name' => 'categories.delete',
                'label' => 'Delete Category',
                'description' => 'Delete a single category record',
            ],
            [
                'group' => 'categories',
                'name' => 'categories.bulk-delete',
                'label' => 'Bulk Delete Categories',
                'description' => 'Delete multiple selected categories',
            ],
            [
                'group' => 'categories',
                'name' => 'categories.status',
                'label' => 'Toggle Category Status',
                'description' => 'Toggle category active/inactive status',
            ],

            // Subcategory Management
            [
                'group' => 'subcategories',
                'name' => 'subcategories.view',
                'label' => 'View Subcategories',
                'description' => 'View list of subcategories and relationships',
            ],
            [
                'group' => 'subcategories',
                'name' => 'subcategories.create',
                'label' => 'Create Subcategory',
                'description' => 'Create new subcategory linked to a category',
            ],
            [
                'group' => 'subcategories',
                'name' => 'subcategories.edit',
                'label' => 'Edit Subcategory',
                'description' => 'Update subcategory name, parent, and status',
            ],
            [
                'group' => 'subcategories',
                'name' => 'subcategories.delete',
                'label' => 'Delete Subcategory',
                'description' => 'Delete a single subcategory record',
            ],
            [
                'group' => 'subcategories',
                'name' => 'subcategories.bulk-delete',
                'label' => 'Bulk Delete Subcategories',
                'description' => 'Delete multiple selected subcategories',
            ],
            [
                'group' => 'subcategories',
                'name' => 'subcategories.status',
                'label' => 'Toggle Subcategory Status',
                'description' => 'Toggle subcategory active/inactive status',
            ],

            // Region Management
            [
                'group' => 'regions',
                'name' => 'regions.view',
                'label' => 'View Regions',
                'description' => 'View list of service regions and details',
            ],
            [
                'group' => 'regions',
                'name' => 'regions.create',
                'label' => 'Create Region',
                'description' => 'Create new regional service territories',
            ],
            [
                'group' => 'regions',
                'name' => 'regions.edit',
                'label' => 'Edit Region',
                'description' => 'Update region name, currency, and details',
            ],
            [
                'group' => 'regions',
                'name' => 'regions.delete',
                'label' => 'Delete Region',
                'description' => 'Soft delete a single region record',
            ],
            [
                'group' => 'regions',
                'name' => 'regions.bulk-delete',
                'label' => 'Bulk Delete Regions',
                'description' => 'Delete multiple selected region records',
            ],
            [
                'group' => 'regions',
                'name' => 'regions.status',
                'label' => 'Toggle Region Status',
                'description' => 'Toggle region active/inactive status',
            ],

            // Regional Service Pricing
            [
                'group' => 'regional_prices',
                'name' => 'regional_prices.view',
                'label' => 'View Regional Prices',
                'description' => 'View regional service pricing configurations',
            ],
            [
                'group' => 'regional_prices',
                'name' => 'regional_prices.create',
                'label' => 'Create Regional Price',
                'description' => 'Create localized pricing for services in specific regions',
            ],
            [
                'group' => 'regional_prices',
                'name' => 'regional_prices.edit',
                'label' => 'Edit Regional Price',
                'description' => 'Update regional service prices and currency rates',
            ],
            [
                'group' => 'regional_prices',
                'name' => 'regional_prices.delete',
                'label' => 'Delete Regional Price',
                'description' => 'Soft delete a regional service price entry',
            ],
            [
                'group' => 'regional_prices',
                'name' => 'regional_prices.bulk-delete',
                'label' => 'Bulk Delete Regional Prices',
                'description' => 'Delete multiple selected regional price entries',
            ],
            [
                'group' => 'regional_prices',
                'name' => 'regional_prices.status',
                'label' => 'Toggle Price Status',
                'description' => 'Toggle regional service price active/inactive state',
            ],

            // Inquiry Management
            [
                'group' => 'inquiries',
                'name' => 'inquiries.view',
                'label' => 'View Inquiries',
                'description' => 'View incoming service requests and customer inquiries',
            ],
            [
                'group' => 'inquiries',
                'name' => 'inquiries.edit',
                'label' => 'Update Inquiry',
                'description' => 'Update inquiry status and follow-up notes',
            ],
            [
                'group' => 'inquiries',
                'name' => 'inquiries.delete',
                'label' => 'Delete Inquiry',
                'description' => 'Delete an inquiry record',
            ],
            [
                'group' => 'inquiries',
                'name' => 'inquiries.bulk-delete',
                'label' => 'Bulk Delete Inquiries',
                'description' => 'Delete multiple selected customer inquiries',
            ],
            [
                'group' => 'inquiries',
                'name' => 'inquiries.status',
                'label' => 'Update Inquiry Status',
                'description' => 'Change inquiry progress state (new, contacted, quoted, completed)',
            ],

            // Service Request Management
            [
                'group' => 'service_requests',
                'name' => 'service_requests.view',
                'label' => 'View Service Requests',
                'description' => 'View customer service requests, attachments, and schedules',
            ],
            [
                'group' => 'service_requests',
                'name' => 'service_requests.edit',
                'label' => 'Edit Service Request',
                'description' => 'Update service request details and notes',
            ],
            [
                'group' => 'service_requests',
                'name' => 'service_requests.status',
                'label' => 'Update Request Status',
                'description' => 'Update workflow progress status of service requests',
            ],
            [
                'group' => 'service_requests',
                'name' => 'service_requests.delete',
                'label' => 'Delete Service Request',
                'description' => 'Delete a service request record',
            ],
            [
                'group' => 'service_requests',
                'name' => 'service_requests.bulk-delete',
                'label' => 'Bulk Delete Service Requests',
                'description' => 'Delete multiple selected customer service requests',
            ],
            [
                'group' => 'service_requests',
                'name' => 'service_requests.export',
                'label' => 'Export Service Requests',
                'description' => 'Export customer service requests to CSV',
            ],

            // Quote & Estimate Management
            [
                'group' => 'service_requests',
                'name' => 'quotes.view',
                'label' => 'View Quotes',
                'description' => 'View generated price quotes and estimates',
            ],
            [
                'group' => 'service_requests',
                'name' => 'quotes.create',
                'label' => 'Create & Send Quotes',
                'description' => 'Create, calculate, and issue price quotes for customer requests',
            ],
            [
                'group' => 'service_requests',
                'name' => 'quotes.status',
                'label' => 'Update Quote Status',
                'description' => 'Update quote status (approved, declined, ask for question)',
            ],
            [
                'group' => 'service_requests',
                'name' => 'quotes.delete',
                'label' => 'Delete Quotes',
                'description' => 'Delete price quotes from the system',
            ],

            // Legal & Compliance Management
            [
                'group' => 'legal',
                'name' => 'terms.view',
                'label' => 'View Terms & Conditions',
                'description' => 'View Terms and Conditions document and revisions',
            ],
            [
                'group' => 'legal',
                'name' => 'terms.edit',
                'label' => 'Edit Terms & Conditions',
                'description' => 'Update Terms and Conditions legal text and version',
            ],
            [
                'group' => 'legal',
                'name' => 'privacy.view',
                'label' => 'View Privacy Policy',
                'description' => 'View Privacy Policy document and revisions',
            ],
            [
                'group' => 'legal',
                'name' => 'privacy.edit',
                'label' => 'Edit Privacy Policy',
                'description' => 'Update Privacy Policy legal text and version',
            ],

            // System Settings
            [
                'group' => 'settings',
                'name' => 'settings.view',
                'label' => 'View Settings',
                'description' => 'View ERP system settings and parameters',
            ],
            [
                'group' => 'settings',
                'name' => 'settings.edit',
                'label' => 'Edit Settings',
                'description' => 'Update ERP configuration and company profile',
            ],
        ];

        $groups = PermissionGroup::all()->keyBy('slug');
        $validNames = array_column($permissions, 'name');

        foreach ($permissions as $item) {
            $group = $groups->get($item['group']);
            if (! $group) {
                continue;
            }

            Permission::updateOrCreate(
                ['name' => $item['name']],
                [
                    'permission_group_id' => $group->id,
                    'label' => $item['label'],
                    'description' => $item['description'],
                ]
            );
        }

        // Clean up any removed permissions
        Permission::whereNotIn('name', $validNames)->delete();
    }
}
