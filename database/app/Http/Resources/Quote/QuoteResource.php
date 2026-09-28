<?php

namespace App\Http\Resources\Quote;

use App\Enums\QuoteStatus;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Quote
 */
class QuoteResource extends JsonResource
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
            'quote_number' => $this->quote_number,
            'service_request_id' => $this->service_request_id,
            'service_description' => $this->service_description,
            'costs' => [
                'labor' => (float) $this->labor_cost,
                'materials' => (float) $this->materials_cost,
                'equipment' => (float) $this->equipment_cost,
                'trip_charge' => (float) $this->trip_charge,
                'additional_charges' => (float) $this->additional_charges,
                'subtotal' => (float) $this->subtotal,
                'discount' => (float) $this->discount,
                'tax_rate' => (float) $this->tax_rate,
                'tax_amount' => (float) $this->tax_amount,
                'total_price' => (float) $this->total_price,
            ],
            'terms_and_conditions' => $this->terms_and_conditions,
            'expires_at' => $this->expires_at?->format('Y-m-d'),
            'status' => $this->status instanceof \BackedEnum ? $this->status->value : $this->status,
            'status_label' => $this->status instanceof QuoteStatus ? $this->status->label() : $this->status,
            'customer_notes' => $this->customer_notes,
            'created_at' => $this->created_at?->toIso8601String(),
            'approved_at' => $this->approved_at?->toIso8601String(),
            'declined_at' => $this->declined_at?->toIso8601String(),
            'sender' => $this->relationLoaded('sender') && $this->sender ? [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
            ] : null,
        ];
    }
}
