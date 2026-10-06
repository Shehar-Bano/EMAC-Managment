<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionalServicePriceResource extends JsonResource
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
            'region_name' => $this->relationLoaded('region') ? $this->region?->name : null,
            'region_code' => $this->relationLoaded('region') ? $this->region?->code : null,
            'category_id' => $this->category_id,
            'category_name' => $this->relationLoaded('category') ? $this->category?->name : null,
            'subcategory_id' => $this->subcategory_id,
            'subcategory_name' => $this->relationLoaded('subcategory') ? $this->subcategory?->name : null,
            'price' => (float) $this->price,
            'formatted_price' => $this->currency.' '.number_format((float) $this->price, 2),
            'currency' => $this->currency,
            'notes' => $this->notes,
            'status' => $this->status,
            'region' => new RegionResource($this->whenLoaded('region')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'subcategory' => new SubcategoryResource($this->whenLoaded('subcategory')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
