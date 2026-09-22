<?php

namespace App\Actions\Category;

use App\Models\Category;
use Illuminate\Support\Facades\DB;

class DeleteCategoryAction
{
    /**
     * Delete a category and handle relations.
     */
    public function execute(Category $category): bool
    {
        return DB::transaction(function () use ($category) {
            // Also soft-delete associated subcategories
            $category->subcategories()->delete();

            return (bool) $category->delete();
        });
    }
}
