<?php

namespace App\Actions\Region;

use App\Models\Region;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateRegionAction
{
    /**
     * Create a new region with automatic slug and code generation.
     */
    public function execute(array $data): Region
    {
        return DB::transaction(function () use ($data) {
            return Region::create([
                'name' => $data['name'],
                'slug' => ! empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']),
                'code' => ! empty($data['code']) ? strtoupper($data['code']) : null,
                'currency' => $data['currency'] ?? 'USD',
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 'active',
            ]);
        });
    }
}
