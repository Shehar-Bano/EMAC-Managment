<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            [
                'name' => 'Grand Cayman',
                'slug' => 'grand-cayman',
                'code' => 'GC',
                'currency' => 'KYD',
                'description' => 'Grand Cayman, Cayman Islands — Primary trade and property maintenance service region.',
                'status' => 'active',
            ],
            [
                'name' => 'Florida',
                'slug' => 'florida',
                'code' => 'FL',
                'currency' => 'USD',
                'description' => 'Florida, United States — Certified trade contractor and plumbing operations region.',
                'status' => 'active',
            ],
            [
                'name' => 'Jamaica',
                'slug' => 'jamaica',
                'code' => 'JM',
                'currency' => 'USD',
                'description' => 'Jamaica, West Indies — Commercial, residential repairs, and facilities dispatch region.',
                'status' => 'active',
            ],
        ];

        foreach ($regions as $regionData) {
            Region::withTrashed()->updateOrCreate(
                ['slug' => $regionData['slug']],
                [
                    'name' => $regionData['name'],
                    'code' => $regionData['code'],
                    'currency' => $regionData['currency'],
                    'description' => $regionData['description'],
                    'status' => $regionData['status'],
                    'deleted_at' => null, // Ensure un-deleted if re-seeding
                ]
            );
        }
    }
}
