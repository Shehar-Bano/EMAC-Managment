<?php

namespace App\Actions\RegionalServicePrice;

use App\Models\RegionalServicePrice;
use Illuminate\Support\Facades\DB;

class BulkDeleteRegionalServicePricesAction
{
    /**
     * Soft delete multiple regional service prices by IDs.
     */
    public function execute(array $ids): int
    {
        return DB::transaction(function () use ($ids) {
            $prices = RegionalServicePrice::whereIn('id', $ids)->get();
            $count = 0;

            foreach ($prices as $price) {
                if ($price->delete()) {
                    $count++;
                }
            }

            return $count;
        });
    }
}
