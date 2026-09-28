<?php

namespace App\Actions\Region;

use App\Models\Region;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpdateRegionAction
{
    /**
     * Update an existing region.
     */
    public function execute(Region $region, array $data): Region
    {
        return DB::transaction(function () use ($region, $data) {
            $region->update([
                'name' => $data['name'] ?? $region->name,
                'slug' => ! empty($data['slug']) ? Str::slug($data['slug']) : ($data['name'] !== $region->name ? Str::slug($data['name']) : $region->slug),
                'code' => isset($data['code']) ? strtoupper($data['code']) : $region->code,
                'currency' => $data['currency'] ?? $region->currency,
                'description' => array_key_exists('description', $data) ? $data['description'] : $region->description,
                'status' => $data['status'] ?? $region->status,
            ]);

            return $region->fresh();
        });
    }
}
