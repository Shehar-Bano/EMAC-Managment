<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\UserAddressResource;
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
        $primaryAddress = $this->addresses->firstWhere('is_primary', true) ?? $this->addresses->first();

        $source = $this->resource->getAttribute('source');
        $accountStatus = $this->resource->getAttribute('account_status');
        $profileStatus = $this->resource->getAttribute('profile_status');
        $emailVerifiedAt = $this->resource->getAttribute('email_verified_at');
        $phoneVerifiedAt = $this->resource->getAttribute('phone_verified_at');
        $avatar = $this->resource->getAttribute('avatar');
        $createdAt = $this->resource->getAttribute('created_at');
        $updatedAt = $this->resource->getAttribute('updated_at');

        return [
            'id' => $this->resource->getAttribute('id'),
            'name' => $this->resource->getAttribute('name'),
            'email' => $this->resource->getAttribute('email'),
            'phone' => $this->resource->getAttribute('phone'),
            'address' => $this->resource->getAttribute('address') ?? $primaryAddress?->address,
            'addresses' => UserAddressResource::collection($this->addresses),
            'role' => $this->resource->getAttribute('role') ?? 'customer',
            'source' => $source instanceof \BackedEnum ? $source->value : ($source ?? 'email'),
            'account_status' => $accountStatus instanceof \BackedEnum ? $accountStatus->value : ($accountStatus ?? 'verified'),
            'profile_status' => $profileStatus instanceof \BackedEnum ? $profileStatus->value : ($profileStatus ?? 'incomplete'),
            'email_verified_at' => $emailVerifiedAt instanceof \DateTimeInterface ? $emailVerifiedAt->format(\DateTimeInterface::ATOM) : null,
            'phone_verified_at' => $phoneVerifiedAt instanceof \DateTimeInterface ? $phoneVerifiedAt->format(\DateTimeInterface::ATOM) : null,
            'avatar_url' => $this->avatar_url,
            'created_at' => $createdAt instanceof \DateTimeInterface ? $createdAt->format(\DateTimeInterface::ATOM) : null,
            'updated_at' => $updatedAt instanceof \DateTimeInterface ? $updatedAt->format(\DateTimeInterface::ATOM) : null,
        ];
    }
}
