<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\ProfileStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\Auth\CustomerProfileResource;
use App\Models\Region;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Retrieve authenticated customer's profile.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load(['addresses.region']);

        return ApiResponse::success(
            data: (new CustomerProfileResource($user))->resolve(),
            message: 'Profile retrieved successfully.',
            statusCode: 200
        );
    }

    /**
     * Complete or update profile.
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        DB::transaction(function () use ($user, $validated, $request) {
            if (array_key_exists('name', $validated) && $validated['name'] !== null) {
                $user->name = $validated['name'];
            }
            if (array_key_exists('phone', $validated) && $validated['phone'] !== null) {
                $user->phone = $validated['phone'];
            }
            if (array_key_exists('address', $validated) && $validated['address'] !== null) {
                $user->address = $validated['address'];
            }

            // Handle Profile Image / Avatar Upload
            $imageFile = $request->file('image') ?? $request->file('avatar') ?? $request->file('profile_image');
            if ($imageFile) {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $user->avatar = $imageFile->store('avatars', 'public');
            }

            // Handle Multiple Addresses in user_addresses table
            if (isset($validated['addresses']) && is_array($validated['addresses'])) {
                $user->addresses()->delete();

                foreach ($validated['addresses'] as $index => $addr) {
                    $hasContent = ! empty($addr['address']) || ! empty($addr['city']) || ! empty($addr['country']) || ! empty($addr['state']) || ! empty($addr['zipcode']) || ! empty($addr['region_id']);
                    if ($hasContent) {
                        $isPrimary = isset($addr['is_primary']) ? (bool) $addr['is_primary'] : ($index === 0);
                        $regionId = ! empty($addr['region_id']) ? (int) $addr['region_id'] : null;
                        $region = $regionId ? Region::find($regionId) : null;
                        $stateName = $addr['state'] ?? $region?->name;

                        $user->addresses()->create([
                            'region_id' => $regionId,
                            'country' => $addr['country'] ?? null,
                            'state' => $stateName,
                            'city' => $addr['city'] ?? null,
                            'zipcode' => $addr['zipcode'] ?? null,
                            'address' => $addr['address'] ?? null,
                            'is_primary' => $isPrimary,
                        ]);

                        if ($isPrimary && ! empty($addr['address'])) {
                            $user->address = $addr['address'];
                        }
                    }
                }
            } elseif (! empty($validated['address']) || ! empty($validated['region_id']) || ! empty($validated['zipcode'])) {
                $regionId = ! empty($validated['region_id']) ? (int) $validated['region_id'] : null;
                $region = $regionId ? Region::find($regionId) : null;
                $stateName = $validated['state'] ?? $region?->name;

                if ($user->addresses()->count() === 0) {
                    $user->addresses()->create([
                        'region_id' => $regionId,
                        'country' => $validated['country'] ?? null,
                        'state' => $stateName,
                        'city' => $validated['city'] ?? null,
                        'zipcode' => $validated['zipcode'] ?? null,
                        'address' => $validated['address'] ?? '',
                        'is_primary' => true,
                    ]);
                }
            }

            // Check profile completion status
            $hasAddress = ! empty($user->address) || $user->addresses()->exists();
            if (! empty($user->name) && ! empty($user->phone) && $hasAddress) {
                $user->profile_status = ProfileStatus::COMPLETE;
            } else {
                $user->profile_status = ProfileStatus::INCOMPLETE;
            }

            $user->save();
        });

        $user->load(['addresses.region']);

        return ApiResponse::success(
            data: (new CustomerProfileResource($user))->resolve(),
            message: 'Profile updated successfully.',
            statusCode: 200
        );
    }
}
