<?php

namespace App\Actions\Subcategory;

use App\Models\Subcategory;
use Illuminate\Support\Facades\DB;

class BulkDeleteSubcategoriesAction
{
    /**
     * Bulk delete subcategories.
     *
     * @param  list<int>  $subcategoryIds
     * @return array{deleted: int, skipped: int}
     */
    public function execute(array $subcategoryIds): array
    {
        $deletedCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use ($subcategoryIds, &$deletedCount) {
            $deletedCount = Subcategory::whereIn('id', $subcategoryIds)->delete();
        });

        return [
            'deleted' => $deletedCount,
            'skipped' => $skippedCount,
        ];
    }
}
