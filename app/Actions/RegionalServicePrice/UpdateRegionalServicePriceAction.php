<?php

namespace App\Actions\RegionalServicePrice;

use App\Models\Region;
use App\Models\RegionalServicePrice;
use Illuminate\Support\Facades\DB;

class UpdateRegionalServicePriceAction
{
    /**
     * Update an existing regional service price entry.
     */
    public function execute(RegionalServicePrice $priceModel, array $data): RegionalServicePrice
    {
        return DB::transaction(function () use ($priceModel, $data) {
            $currency = $data['currency'] ?? $priceModel->currency;
            if (empty($currency) && ! empty($data['region_id'])) {
                $region = Region::find($data['region_id']);
                $currency = $region?->currency ?? 'USD';
            }

            $priceModel->update([
                'region_id' => $data['region_id'] ?? $priceModel->region_id,
                'category_id' => $data['category_id'] ?? $priceModel->category_id,
                'subcategory_id' => $data['subcategory_id'] ?? $priceModel->subcategory_id,
                'price' => $data['price'] ?? $priceModel->price,
                'currency' => $currency,
                'notes' => array_key_exists('notes', $data) ? $data['notes'] : $priceModel->notes,
                'status' => $data['status'] ?? $priceModel->status,
            ]);

            return $priceModel->fresh();
        });
    }
}
