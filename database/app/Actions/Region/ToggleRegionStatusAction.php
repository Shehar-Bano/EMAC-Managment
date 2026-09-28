<?php

namespace App\Actions\Region;

use App\Models\Region;

class ToggleRegionStatusAction
{
    /**
     * Toggle the active/inactive status of a region.
     */
    public function execute(Region $region): Region
    {
        $region->status = ($region->status === 'active') ? 'inactive' : 'active';
        $region->save();

        return $region;
    }
}
