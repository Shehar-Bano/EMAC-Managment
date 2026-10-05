<?php

namespace App\Actions\Subcategory;

use App\Models\Subcategory;
use Illuminate\Support\Facades\DB;

class DeleteSubcategoryAction
{
    /**
     * Delete the specified subcategory.
     */
    public function execute(Subcategory $subcategory): bool
    {
        return DB::transaction(function () use ($subcategory) {
            return (bool) $subcategory->delete();
        });
    }
}
