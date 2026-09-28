<?php

namespace App\Http\Resources\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserAuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role ?? 'customer',
        ];

        if ($this->source) {
            $data['source'] = $this->source instanceof \BackedEnum ? $this->source->value : $this->source;
        }

        $data['account_status'] = $this->account_status instanceof \BackedEnum ? $this->account_status->value : ($this->account_status ?? 'verified');
        $data['profile_status'] = $this->profile_status instanceof \BackedEnum ? $this->profile_status->value : ($this->profile_status ?? 'incomplete');

        return $data;
    }
}
