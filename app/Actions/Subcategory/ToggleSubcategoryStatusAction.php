<?php

namespace App\Actions\Subcategory;

use App\Models\Subcategory;

class ToggleSubcategoryStatusAction
{
    /**
     * Toggle active/inactive status of subcategory.
     */
    public function execute(Subcategory $subcategory): Subcategory
    {
        $subcategory->status = $subcategory->status === 'active' ? 'inactive' : 'active';
        $subcategory->save();

        return $subcategory;
    }
}
