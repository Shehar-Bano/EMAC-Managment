<?php

namespace App\Actions\Region;

use App\Models\Region;

class DeleteRegionAction
{
    /**
     * Soft delete a single region.
     */
    public function execute(Region $region): bool
    {
        return (bool) $region->delete();
    }
}
