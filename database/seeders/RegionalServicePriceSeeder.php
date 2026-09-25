<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\RegionalServicePrice;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class RegionalServicePriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = Region::active()->get();
        $subcategories = Subcategory::with('category')->active()->get();

        if ($regions->isEmpty() || $subcategories->isEmpty()) {
            return;
        }

        // Base prices per subcategory for regional multipliers
        // GC multiplier 1.25, FL multiplier 1.0, JM multiplier 0.85
        $multipliers = [
            'grand-cayman' => ['multiplier' => 1.25, 'currency' => 'KYD'],
            'florida' => ['multiplier' => 1.00, 'currency' => 'USD'],
            'jamaica' => ['multiplier' => 0.85, 'currency' => 'USD'],
        ];

        $basePrices = [
            'General repairs' => 85.00,
            'Drywall repair' => 120.00,
            'Painting' => 150.00,
            'Door repair' => 95.00,
            'Door installation' => 220.00,
            'Window repair' => 110.00,
            'Pressure washing' => 135.00,
            'Fixture installation' => 75.00,
            'Furniture assembly' => 65.00,
            'Water heater replacement/installation' => 450.00,
            'Faucet replacement' => 115.00,
            'Toilet repair/replacement' => 140.00,
            'Sink installation' => 175.00,
            'Garbage disposal installation' => 130.00,
            'Pipe repair' => 160.00,
            'Broken pipe repair' => 250.00,
            'Leak repair' => 145.00,
            'Drain issues' => 125.00,
        ];

        foreach ($regions as $region) {
            $conf = $multipliers[$region->slug] ?? ['multiplier' => 1.0, 'currency' => $region->currency];

            foreach ($subcategories as $sub) {
                $base = $basePrices[$sub->name] ?? 95.00;
                $finalPrice = round($base * $conf['multiplier'], 2);

                RegionalServicePrice::withTrashed()->updateOrCreate(
                    [
                        'region_id' => $region->id,
                        'category_id' => $sub->category_id,
                        'subcategory_id' => $sub->id,
                    ],
                    [
                        'price' => $finalPrice,
                        'currency' => $conf['currency'],
                        'notes' => "Standard {$region->name} regional service base rate.",
                        'status' => 'active',
                        'deleted_at' => null,
                    ]
                );
            }
        }
    }
}
