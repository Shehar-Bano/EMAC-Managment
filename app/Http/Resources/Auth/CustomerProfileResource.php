<?php

namespace App\Http\Resources\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class CustomerProfileResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'role' => $this->role ?? 'customer',
            'source' => $this->source instanceof \BackedEnum ? $this->source->value : ($this->source ?? 'email'),
            'account_status' => $this->account_status instanceof \BackedEnum ? $this->account_status->value : ($this->account_status ?? 'verified'),
            'profile_status' => $this->profile_status instanceof \BackedEnum ? $this->profile_status->value : ($this->profile_status ?? 'incomplete'),
            'email_verified_at' => $this->email_verified_at?->toISOString(),
            'phone_verified_at' => $this->phone_verified_at?->toISOString(),
            'profile' => [
                'profile_image' => $this->avatar ? asset('storage/'.$this->avatar) : null,
            ],
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
