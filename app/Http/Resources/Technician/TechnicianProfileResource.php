<?php

namespace App\Http\Resources\Technician;

use App\Models\User;
use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class TechnicianProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $primaryAddress = $this->addresses->firstWhere('is_primary', true) ?? $this->addresses->first();
        $region = $primaryAddress?->region;

        $operatingRegion = [
            'id' => $region?->id ?? $primaryAddress?->region_id ?? 1,
            'name' => $region?->name ?? ($primaryAddress?->state ?: 'Grand Cayman - Western District'),
            'code' => $region?->code ?? ($primaryAddress?->zipcode ?: 'GCM-WEST'),
            'city' => $primaryAddress?->city ?? 'George Town',
        ];

        // Specializations (Skills / Category)
        $specializations = [];
        if ($this->category) {
            $cat = $this->category;
            $specializations[] = [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'icon' => $cat->icon_url ?? $cat->icon,
                'is_primary' => true,
            ];
        }

        // Services Provided (Sub Skills from usersubskills)
        $servicesProvided = $this->subcategories->map(function ($sub) {
            return [
                'id' => $sub->id,
                'category_id' => $sub->category_id,
                'category_name' => $sub->category?->name ?? '',
                'name' => $sub->name,
                'slug' => $sub->slug,
                'icon' => $sub->icon_url ?? $sub->icon ?? '🔧',
                'is_active' => $sub->status === 'active',
            ];
        })->values()->toArray();

        // Verified at ISO string
        $verifiedAt = $this->verified_at;
        $verifiedAtString = $verifiedAt instanceof DateTimeInterface
            ? $verifiedAt->format(DateTimeInterface::ATOM)
            : ($this->is_verified && $this->created_at ? $this->created_at->format(DateTimeInterface::ATOM) : null);

        return [
            'id' => $this->id,
            'user_id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar_url,
            'role' => $this->role ?? ($this->roles->first()?->slug ?? 'technician'),
            'certification_id' => $this->certification_id,
            'certification_body' => $this->certification_body,
            'is_verified' => (bool) ($this->is_verified ?? true),
            'verified_at' => $verifiedAtString,
            'duty_status' => $this->duty_status ?? 'on_duty',
            'experience_years' => (int) ($this->experience_years ?? 0),
            'bio' => $this->bio,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'statistics' => [
                'rating' => 4.92,
                'total_reviews' => 86,
                'total_jobs_completed' => 142,
                'active_jobs_count' => 1,
                'completion_rate' => 98.6,
            ],
            'operating_region' => $operatingRegion,
            'specializations' => $specializations,
            'services_provided' => $servicesProvided,
        ];
    }
}
