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
        return [
            'id' => $this->id,
            'region_id' => $this->region_id,
            'region_name' => $this->region?->name ?? $this->state,
            'region_code' => $this->region?->code,
            'currency' => $this->region?->currency,
            'region' => $this->whenLoaded('region', fn () => new RegionResource($this->region)),
            'country' => $this->country,
            'state' => $this->state ?? $this->region?->name,
            'city' => $this->city,
            'address' => $this->address,
            'is_primary' => (bool) $this->is_primary,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
