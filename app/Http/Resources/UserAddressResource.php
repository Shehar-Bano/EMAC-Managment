<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $region = $this->relationLoaded('region') ? $this->region : null;

        return [
            'id' => $this->id,
            'region_id' => $this->region_id,
            'region_name' => $region?->name ?? $this->state,
            'region_code' => $region?->code,
            'currency' => $region?->currency,
            'region' => $region ? new RegionResource($region) : null,
            'country' => $this->country,
            'state' => $this->state ?? $region?->name,
            'city' => $this->city,
            'zipcode' => $this->zipcode,
            'address' => $this->address,
            'is_primary' => (bool) $this->is_primary,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
