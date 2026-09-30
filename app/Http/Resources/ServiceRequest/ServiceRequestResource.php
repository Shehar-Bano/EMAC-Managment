<?php

namespace App\Http\Resources\ServiceRequest;

use App\Http\Resources\Quote\QuoteResource;
use App\Http\Resources\UserAddressResource;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ServiceRequest
 */
class ServiceRequestResource extends JsonResource
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
            'category_id' => $this->category_id,
            'subcategory_id' => $this->subcategory_id,
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
                'icon' => $this->category->icon_url,
                'icon_url' => $this->category->icon_url,
                'image' => $this->category->image_url,
                'image_url' => $this->category->image_url,
            ] : null,
            'subcategory' => $this->subcategory ? [
                'id' => $this->subcategory->id,
                'name' => $this->subcategory->name,
                'slug' => $this->subcategory->slug,
                'icon' => $this->subcategory->icon_url,
                'icon_url' => $this->subcategory->icon_url,
                'image' => $this->subcategory->image_url,
                'image_url' => $this->subcategory->image_url,
            ] : null,
            'description' => $this->description,
            'property_information' => $this->property_information,
            'preferred_service_date' => $this->preferred_service_date?->format('Y-m-d'),
            'preferred_service_time' => $this->preferred_service_time,
            'priority' => $this->priority instanceof \BackedEnum ? $this->priority->value : $this->priority,
            'additional_notes' => $this->additional_notes,
            'status' => $this->status instanceof \BackedEnum ? $this->status->value : $this->status,
            'type' => $this->type ?? 'app',
            'location' => $this->address ? (new UserAddressResource($this->address))->resolve() : null,
            'user' => $this->relationLoaded('user') ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
            ] : null,
            'quote' => $this->relationLoaded('latestQuote') && $this->latestQuote
                ? (new QuoteResource($this->latestQuote))->resolve()
                : ($this->relationLoaded('quotes') && $this->quotes->isNotEmpty()
                    ? (new QuoteResource($this->quotes->first()))->resolve()
                    : null),
            'photographs' => $this->photographs->map(fn ($photo) => [
                'id' => $photo->id,
                'file_path' => $photo->file_path,
                'file_url' => $photo->file_url,
                'file_name' => $photo->file_name,
                'file_size' => $photo->file_size,
                'mime_type' => $photo->mime_type,
            ]),
            'videos' => $this->videos->map(fn ($video) => [
                'id' => $video->id,
                'file_path' => $video->file_path,
                'file_url' => $video->file_url,
                'file_name' => $video->file_name,
                'file_size' => $video->file_size,
                'mime_type' => $video->mime_type,
                'duration' => $video->duration,
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
