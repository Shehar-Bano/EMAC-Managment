<?php

namespace App\Actions\Category;

use App\Models\Category;
use Illuminate\Support\Facades\DB;

class BulkDeleteCategoriesAction
{
    /**
     * Execute bulk category deletion.
     *
     * @param  list<int>  $categoryIds
     * @return array{deleted: int, skipped: int}
     */
    public function execute(array $categoryIds): array
    {
        $categories = Category::whereIn('id', $categoryIds)->get();

        $deletedCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use ($categories, &$deletedCount) {
            foreach ($categories as $category) {
                $category->subcategories()->delete();
                $category->delete();
                $deletedCount++;
            }
        });

        return [
            'deleted' => $deletedCount,
            'skipped' => $skippedCount,
        ];
    }
}
