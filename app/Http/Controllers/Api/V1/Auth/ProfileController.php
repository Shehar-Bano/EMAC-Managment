<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\ProfileStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\Auth\CustomerProfileResource;
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
        $user = $request->user()->load('addresses');

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

            // Handle Avatar Upload
            if ($request->hasFile('avatar')) {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $user->avatar = $request->file('avatar')->store('avatars', 'public');
            }

            // Handle Multiple Addresses in user_addresses table
            if (isset($validated['addresses']) && is_array($validated['addresses'])) {
                $user->addresses()->delete();

                foreach ($validated['addresses'] as $index => $addr) {
                    if (! empty($addr['address']) || ! empty($addr['city']) || ! empty($addr['country']) || ! empty($addr['state'])) {
                        $isPrimary = isset($addr['is_primary']) ? (bool) $addr['is_primary'] : ($index === 0);

                        $user->addresses()->create([
                            'country' => $addr['country'] ?? null,
                            'state' => $addr['state'] ?? null,
                            'city' => $addr['city'] ?? null,
                            'address' => $addr['address'] ?? null,
                            'is_primary' => $isPrimary,
                        ]);

                        if ($isPrimary && ! empty($addr['address'])) {
                            $user->address = $addr['address'];
                        }
                    }
                }
            } elseif (! empty($validated['address']) && $user->addresses()->count() === 0) {
                $user->addresses()->create([
                    'address' => $validated['address'],
                    'is_primary' => true,
                ]);
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

        $user->load('addresses');

        return ApiResponse::success(
            data: (new CustomerProfileResource($user))->resolve(),
            message: 'Profile updated successfully.',
            statusCode: 200
        );
    }
}
