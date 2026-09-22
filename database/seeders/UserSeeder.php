<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        $technicianRole = Role::where('slug', 'technician')->first();
        $customerRole = Role::where('slug', 'customer')->first();

        // 1. Super Admin User
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@emac.test'],
            [
                'name' => 'EMAC Super Admin',
                'phone' => '+1 (555) 019-2834',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        if ($superAdminRole) {
            $superAdmin->roles()->sync([$superAdminRole->id]);
        }

        // 2. Technicians
        $technicians = [
            [
                'name' => 'Alexander Wright',
                'email' => 'technician@emac.test',
                'phone' => '+1 (555) 018-9941',
                'status' => 'active',
            ],
            [
                'name' => 'David Martinez',
                'email' => 'david.m@emac.test',
                'phone' => '+1 (555) 015-3310',
                'status' => 'active',
            ],
            [
                'name' => 'Lucas Miller',
                'email' => 'lucas.m@emac.test',
                'phone' => '+1 (555) 014-7721',
                'status' => 'active',
            ],
            [
                'name' => 'Noah Davis',
                'email' => 'noah.d@emac.test',
                'phone' => '+1 (555) 013-8832',
                'status' => 'active',
            ],
            [
                'name' => 'Ethan Anderson',
                'email' => 'ethan.a@emac.test',
                'phone' => '+1 (555) 012-4491',
                'status' => 'inactive',
            ],
            [
                'name' => 'James Jackson',
                'email' => 'james.j@emac.test',
                'phone' => '+1 (555) 011-5510',
                'status' => 'active',
            ],
        ];

        foreach ($technicians as $t) {
            $u = User::updateOrCreate(
                ['email' => $t['email']],
                [
                    'name' => $t['name'],
                    'phone' => $t['phone'],
                    'password' => Hash::make('password'),
                    'status' => $t['status'],
                    'email_verified_at' => now(),
                ]
            );
            if ($technicianRole) {
                $u->roles()->sync([$technicianRole->id]);
            }
        }

        // 3. Customers
        $customers = [
            [
                'name' => 'Sarah Jenkins',
                'email' => 'customer@emac.test',
                'phone' => '+1 (555) 017-4432',
                'status' => 'active',
            ],
            [
                'name' => 'Michael Chen',
                'email' => 'michael.c@emac.test',
                'phone' => '+1 (555) 016-8821',
                'status' => 'active',
            ],
            [
                'name' => 'Emma Watson',
                'email' => 'emma.w@emac.test',
                'phone' => '+1 (555) 021-9921',
                'status' => 'active',
            ],
            [
                'name' => 'Sophia Taylor',
                'email' => 'sophia.t@emac.test',
                'phone' => '+1 (555) 022-8812',
                'status' => 'active',
            ],
            [
                'name' => 'Oliver Brown',
                'email' => 'oliver.b@emac.test',
                'phone' => '+1 (555) 023-7743',
                'status' => 'inactive',
            ],
            [
                'name' => 'Ava Wilson',
                'email' => 'ava.w@emac.test',
                'phone' => '+1 (555) 024-6632',
                'status' => 'active',
            ],
            [
                'name' => 'Isabella Moore',
                'email' => 'isabella.m@emac.test',
                'phone' => '+1 (555) 025-5521',
                'status' => 'active',
            ],
            [
                'name' => 'Mia Thomas',
                'email' => 'mia.t@emac.test',
                'phone' => '+1 (555) 026-4410',
                'status' => 'inactive',
            ],
            [
                'name' => 'Charlotte White',
                'email' => 'charlotte.w@emac.test',
                'phone' => '+1 (555) 027-3309',
                'status' => 'active',
            ],
            [
                'name' => 'Benjamin Harris',
                'email' => 'benjamin.h@emac.test',
                'phone' => '+1 (555) 028-2298',
                'status' => 'active',
            ],
        ];

        foreach ($customers as $c) {
            $u = User::updateOrCreate(
                ['email' => $c['email']],
                [
                    'name' => $c['name'],
                    'phone' => $c['phone'],
                    'password' => Hash::make('password'),
                    'status' => $c['status'],
                    'email_verified_at' => now(),
                ]
            );
            if ($customerRole) {
                $u->roles()->sync([$customerRole->id]);
            }
        }
    }
}
