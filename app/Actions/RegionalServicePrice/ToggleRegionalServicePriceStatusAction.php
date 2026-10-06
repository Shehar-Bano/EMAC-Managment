<?php

namespace App\Actions\RegionalServicePrice;

use App\Models\RegionalServicePrice;

class ToggleRegionalServicePriceStatusAction
{
    /**
     * Toggle the active/inactive status of a regional service price.
     */
    public function execute(RegionalServicePrice $priceModel): RegionalServicePrice
    {
        $priceModel->status = ($priceModel->status === 'active') ? 'inactive' : 'active';
        $priceModel->save();

        return $priceModel;
    }
}
