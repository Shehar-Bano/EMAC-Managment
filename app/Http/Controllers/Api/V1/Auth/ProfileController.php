<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\ProfileStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\Auth\CustomerProfileResource;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Retrieve authenticated customer's profile.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

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
        $validated = array_filter($request->validated(), fn ($val) => $val !== null);

        $user->fill($validated);

        // Server-side profile completion rule
        if (! empty($user->name) && ! empty($user->phone) && ! empty($user->address)) {
            $user->profile_status = ProfileStatus::COMPLETE;
        } else {
            $user->profile_status = ProfileStatus::INCOMPLETE;
        }

        $user->save();

        return ApiResponse::success(
            data: (new CustomerProfileResource($user))->resolve(),
            message: 'Profile updated successfully.',
            statusCode: 200
        );
    }
}
