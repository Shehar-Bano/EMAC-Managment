<?php

namespace App\Actions\RegionalServicePrice;

use App\Models\RegionalServicePrice;

class DeleteRegionalServicePriceAction
{
    /**
     * Soft delete a single regional service price.
     */
    public function execute(RegionalServicePrice $priceModel): bool
    {
        return (bool) $priceModel->delete();
    }
}
