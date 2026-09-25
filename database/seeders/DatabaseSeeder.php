<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionGroupSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            RegionSeeder::class,
            RegionalServicePriceSeeder::class,
            LegalDocumentSeeder::class,
        ]);
    }
}
