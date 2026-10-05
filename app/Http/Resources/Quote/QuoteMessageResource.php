<?php

namespace App\Http\Resources\Quote;

use App\Models\QuoteMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin QuoteMessage
 */
class QuoteMessageResource extends JsonResource
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
            'quote_id' => $this->quote_id,
            'user_id' => $this->user_id,
            'sender_type' => $this->sender_type,
            'sender_name' => $this->user?->name ?? ($this->sender_type === 'admin' ? 'EMAC Support' : 'Customer'),
            'message' => $this->message,
            'attachments' => $this->attachments,
            'is_internal' => (bool) $this->is_internal,
            'created_at' => $this->created_at?->toIso8601String(),
            'created_at_human' => $this->created_at?->diffForHumans(),
        ];
    }
}
