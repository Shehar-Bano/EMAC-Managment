<?php

namespace App\Actions\Category;

use App\Models\Category;

class ToggleCategoryStatusAction
{
    /**
     * Toggle the active/inactive status of a category.
     */
    public function execute(Category $category): Category
    {
        $category->status = $category->status === 'active' ? 'inactive' : 'active';
        $category->save();

        return $category;
    }
}
