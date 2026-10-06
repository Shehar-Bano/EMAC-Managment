<?php

namespace App\Actions\RegionalServicePrice;

use App\Models\Region;
use App\Models\RegionalServicePrice;
use Illuminate\Support\Facades\DB;

class CreateRegionalServicePriceAction
{
    /**
     * Create a new regional service price entry.
     */
    public function execute(array $data): RegionalServicePrice
    {
        return DB::transaction(function () use ($data) {
            $currency = $data['currency'] ?? null;
            if (! $currency && ! empty($data['region_id'])) {
                $region = Region::find($data['region_id']);
                $currency = $region?->currency ?? 'USD';
            }

            return RegionalServicePrice::create([
                'region_id' => $data['region_id'],
                'category_id' => $data['category_id'],
                'subcategory_id' => $data['subcategory_id'],
                'price' => $data['price'],
                'currency' => $currency ?? 'USD',
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'] ?? 'active',
            ]);
        });
    }
}
