<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubcategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $regionParam = $request->get('region_id') ?? $request->get('region') ?? $request->get('region_code') ?? $request->get('region_slug');
        $targetRegionId = is_numeric($regionParam) ? (int) $regionParam : null;

        $matchedPrice = null;
        if ($this->relationLoaded('regionalServicePrices')) {
            if ($targetRegionId) {
                $matchedPrice = $this->regionalServicePrices->firstWhere('region_id', $targetRegionId);
            } elseif ($regionParam) {
                $matchedPrice = $this->regionalServicePrices->first(function ($p) use ($regionParam) {
                    return $p->region?->slug === $regionParam
                        || $p->region?->code === $regionParam
                        || strcasecmp((string) $p->region?->name, (string) $regionParam) === 0;
                });
            }

            if (! $matchedPrice && $this->regionalServicePrices->isNotEmpty()) {
                $matchedPrice = $this->regionalServicePrices->first();
            }
        }

        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'icon' => $this->icon,
            'image' => $this->image,
            'image_url' => $this->image_url,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'price' => $this->when($matchedPrice !== null, fn () => (float) $matchedPrice->price),
            'formatted_price' => $this->when($matchedPrice !== null, fn () => $matchedPrice->currency.' '.number_format((float) $matchedPrice->price, 2)),
            'currency' => $this->when($matchedPrice !== null, fn () => $matchedPrice->currency),
            'region_id' => $this->when($matchedPrice !== null, fn () => $matchedPrice->region_id),
            'region_name' => $this->when($matchedPrice?->relationLoaded('region') && $matchedPrice->region !== null, fn () => $matchedPrice->region?->name),
            'region_code' => $this->when($matchedPrice?->relationLoaded('region') && $matchedPrice->region !== null, fn () => $matchedPrice->region?->code),
            'regional_prices' => RegionalServicePriceResource::collection($this->whenLoaded('regionalServicePrices')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
