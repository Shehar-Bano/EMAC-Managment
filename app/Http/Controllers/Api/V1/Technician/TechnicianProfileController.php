<?php

namespace App\Http\Controllers\Api\V1\Technician;

use App\Http\Controllers\Controller;
use App\Http\Requests\Technician\UpdateTechnicianProfileRequest;
use App\Http\Resources\Technician\TechnicianProfileResource;
use App\Models\Category;
use App\Models\Region;
use App\Support\ApiResponse;
use DateTimeInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TechnicianProfileController extends Controller
{
    /**
     * Retrieve authenticated technician's profile with specializations and sub-skills.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load([
            'addresses.region',
            'category',
            'subcategories.category',
            'roles',
        ]);

        return ApiResponse::success(
            data: (new TechnicianProfileResource($user))->resolve(),
            message: 'Technician profile retrieved successfully.',
            statusCode: 200
        );
    }

    /**
     * Update contact info, bio, experience, emergency contact, operating territory, skills, and photo.
     */
    public function update(UpdateTechnicianProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        DB::transaction(function () use ($user, $validated, $request) {
            $fillableKeys = [
                'name',
                'email',
                'phone',
                'duty_status',
                'experience_years',
                'bio',
                'emergency_contact_name',
                'emergency_contact_phone',
                'certification_id',
                'certification_body',
                'category_id',
            ];

            foreach ($fillableKeys as $key) {
                if (array_key_exists($key, $validated) && $validated[$key] !== null) {
                    $user->{$key} = $validated[$key];
                }
            }

            // Handle Profile Image Upload (Up to 5MB)
            $imageFile = $request->file('avatar')
                ?? $request->file('image')
                ?? $request->file('photo')
                ?? $request->file('profile_image');

            if ($imageFile) {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $user->avatar = $imageFile->store('avatars', 'public');
            }

            $user->save();

            // Sync Subcategories (Sub-Skills)
            if (isset($validated['subcategories']) && is_array($validated['subcategories'])) {
                $user->subcategories()->sync($validated['subcategories']);
            } elseif (isset($validated['subcategory_ids']) && is_array($validated['subcategory_ids'])) {
                $user->subcategories()->sync($validated['subcategory_ids']);
            }

            // Handle Multiple Addresses if provided
            if (isset($validated['addresses']) && is_array($validated['addresses'])) {
                $existingAddresses = $user->addresses()->get();
                $retainedIds = [];

                foreach ($validated['addresses'] as $index => $addr) {
                    $hasContent = ! empty($addr['address']) || ! empty($addr['city']) || ! empty($addr['country']) || ! empty($addr['state']) || ! empty($addr['zipcode']) || ! empty($addr['region_id']);
                    if ($hasContent) {
                        $isPrimary = isset($addr['is_primary']) ? (bool) $addr['is_primary'] : ($index === 0);
                        $regionId = ! empty($addr['region_id']) ? (int) $addr['region_id'] : null;
                        $region = $regionId ? Region::find($regionId) : null;
                        $stateName = $addr['state'] ?? $region?->name;

                        $addressAttributes = [
                            'region_id' => $regionId,
                            'country' => $addr['country'] ?? 'Cayman Islands',
                            'state' => $stateName,
                            'city' => $addr['city'] ?? 'George Town',
                            'zipcode' => $addr['zipcode'] ?? $region?->code,
                            'address' => $addr['address'] ?? null,
                            'is_primary' => $isPrimary,
                        ];

                        $targetAddress = null;
                        if (! empty($addr['id'])) {
                            $targetAddress = $existingAddresses->firstWhere('id', (int) $addr['id']);
                        }

                        if (! $targetAddress) {
                            $targetAddress = $existingAddresses->first(fn ($a) => ! in_array($a->id, $retainedIds, true));
                        }

                        if ($targetAddress) {
                            $targetAddress->update($addressAttributes);
                            $retainedIds[] = $targetAddress->id;
                        } else {
                            $newAddress = $user->addresses()->create($addressAttributes);
                            $retainedIds[] = $newAddress->id;
                        }

                        if ($isPrimary && ! empty($addr['address'])) {
                            $user->address = $addr['address'];
                        }
                    }
                }

                if (! empty($retainedIds)) {
                    $user->addresses()->whereNotIn('id', $retainedIds)->delete();
                }
            } else {
                // Handle Operating Territory / Primary Address update
                $operatingRegionId = $validated['operating_territory_id']
                    ?? $validated['operating_region_id']
                    ?? $validated['region_id']
                    ?? ($request->input('operating_region.id') ?? $request->input('operating_region.region_id'));

                $city = $validated['city'] ?? $request->input('operating_region.city');
                $state = $validated['state'] ?? $request->input('operating_region.name');
                $address = $validated['address'] ?? $request->input('operating_region.address');
                $zipcode = $validated['zipcode'] ?? $request->input('operating_region.code') ?? $request->input('operating_region.zipcode');
                $country = $validated['country'] ?? $request->input('operating_region.country');

                if ($operatingRegionId !== null || $city !== null || $address !== null || $zipcode !== null || $state !== null) {
                    $regionId = ! empty($operatingRegionId) ? (int) $operatingRegionId : null;
                    $region = $regionId ? Region::find($regionId) : null;
                    $stateName = $state ?? $region?->name;

                    $addressAttributes = [
                        'region_id' => $regionId,
                        'country' => $country ?? 'Cayman Islands',
                        'state' => $stateName,
                        'city' => $city ?? 'George Town',
                        'zipcode' => $zipcode ?? $region?->code,
                        'address' => $address ?? ($user->address ?? 'Primary Operating Address'),
                        'is_primary' => true,
                    ];

                    $primaryAddress = $user->addresses()->where('is_primary', true)->first() ?? $user->addresses()->first();

                    if ($primaryAddress) {
                        $primaryAddress->update($addressAttributes);
                    } else {
                        $user->addresses()->create($addressAttributes);
                    }

                    if (! empty($address)) {
                        $user->address = $address;
                    }
                }
            }
        });

        $user->load([
            'addresses.region',
            'category',
            'subcategories.category',
            'roles',
        ]);

        return ApiResponse::success(
            data: (new TechnicianProfileResource($user))->resolve(),
            message: 'Technician profile updated successfully.',
            statusCode: 200
        );
    }

    /**
     * Real-time duty toggle switch (on_duty, off_duty, break).
     */
    public function updateDutyStatus(Request $request): JsonResponse
    {
        $rawStatus = $request->input('duty_status');
        $normalizedStatus = strtolower(trim(str_replace([' ', '-'], '_', (string) $rawStatus)));

        if (! in_array($normalizedStatus, ['on_duty', 'off_duty', 'break'], true)) {
            return ApiResponse::error(
                message: 'Invalid duty status. Allowed values: on_duty, off_duty, break.',
                errorCode: 'INVALID_DUTY_STATUS',
                statusCode: 422,
                errors: [
                    'duty_status' => ['The duty status must be one of: on_duty, off_duty, break.'],
                ]
            );
        }

        $user = $request->user();
        $user->duty_status = $normalizedStatus;
        $user->save();

        $updatedAtString = $user->updated_at instanceof DateTimeInterface
            ? $user->updated_at->format(DateTimeInterface::ATOM)
            : now()->format(DateTimeInterface::ATOM);

        return ApiResponse::success(
            data: [
                'technician_id' => $user->id,
                'duty_status' => $user->duty_status,
                'updated_at' => $updatedAtString,
            ],
            message: "Duty status changed to {$user->duty_status}.",
            statusCode: 200
        );
    }

    /**
     * Retrieve services catalogue with is_assigned flags for technician.
     */
    public function getServices(Request $request): JsonResponse
    {
        $user = $request->user();
        $assignedIds = $user->subcategories()->pluck('subcategories.id')->toArray();

        $categories = Category::with(['subcategories' => fn ($q) => $q->where('status', 'active')->orderBy('sort_order')->orderBy('name')])
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $data = $categories->map(function ($cat) use ($assignedIds) {
            return [
                'category_id' => $cat->id,
                'category_name' => $cat->name,
                'subcategories' => $cat->subcategories->map(function ($sub) use ($assignedIds) {
                    return [
                        'id' => $sub->id,
                        'name' => $sub->name,
                        'slug' => $sub->slug,
                        'icon' => $sub->icon_url ?? $sub->icon ?? '🔧',
                        'is_assigned' => in_array($sub->id, $assignedIds, true),
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();

        return ApiResponse::success(
            data: $data,
            message: 'Services catalogue retrieved successfully.',
            statusCode: 200
        );
    }

    /**
     * Update active sub-services assigned to the technician.
     */
    public function updateServices(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'subcategories' => ['nullable', 'array'],
            'subcategories.*' => ['integer', 'exists:subcategories,id'],
            'subcategory_ids' => ['nullable', 'array'],
            'subcategory_ids.*' => ['integer', 'exists:subcategories,id'],
        ]);

        $subIds = $validated['subcategories'] ?? $validated['subcategory_ids'] ?? [];

        $user->subcategories()->sync($subIds);

        $assignedSubcategories = $user->subcategories()->with('category')->get()->map(fn ($sub) => [
            'id' => $sub->id,
            'category_id' => $sub->category_id,
            'category_name' => $sub->category?->name ?? '',
            'name' => $sub->name,
            'slug' => $sub->slug,
            'icon' => $sub->icon_url ?? $sub->icon ?? '🔧',
            'is_assigned' => true,
        ])->values()->toArray();

        return ApiResponse::success(
            data: [
                'technician_id' => $user->id,
                'assigned_count' => count($assignedSubcategories),
                'services' => $assignedSubcategories,
            ],
            message: 'Technician sub-services updated successfully.',
            statusCode: 200
        );
    }

    /**
     * Dedicated Multipart Profile Photo Upload (JPEG, PNG, WEBP, max 5MB).
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $imageFile = $request->file('avatar')
            ?? $request->file('image')
            ?? $request->file('photo')
            ?? $request->file('file');

        if (! $imageFile) {
            return ApiResponse::error(
                message: 'No avatar image file provided. Please provide an avatar file.',
                errorCode: 'AVATAR_REQUIRED',
                statusCode: 422
            );
        }

        $user = $request->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->avatar = $imageFile->store('avatars', 'public');
        $user->save();

        return ApiResponse::success(
            data: [
                'avatar_url' => $user->avatar_url,
            ],
            message: 'Avatar uploaded successfully.',
            statusCode: 200
        );
    }
}
