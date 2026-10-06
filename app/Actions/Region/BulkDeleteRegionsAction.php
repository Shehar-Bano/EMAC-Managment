<?php

namespace App\Actions\Region;

use App\Models\Region;
use Illuminate\Support\Facades\DB;

class BulkDeleteRegionsAction
{
    /**
     * Soft delete multiple regions by IDs.
     */
    public function execute(array $ids): int
    {
        return DB::transaction(function () use ($ids) {
            $regions = Region::whereIn('id', $ids)->get();
            $count = 0;

            foreach ($regions as $region) {
                if ($region->delete()) {
                    $count++;
                }
            }

            return $count;
        });
    }
}
