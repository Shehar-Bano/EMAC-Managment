<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\AccountStatus;
use App\Enums\AuthSource;
use App\Enums\OtpPurpose;
use App\Enums\ProfileStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RefreshTokenRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\SocialLoginRequest;
use App\Http\Resources\Auth\UserAuthResource;
use App\Models\Region;
use App\Models\Role;
use App\Models\User;
use App\Services\Auth\OtpService;
use App\Services\Auth\SocialAuthService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    /**
     * Customer registration.
     */
    public function register(RegisterRequest $request, OtpService $otpService): JsonResponse
    {
        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => strtolower($request->email),
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'customer',
                'source' => AuthSource::EMAIL,
                'account_status' => AccountStatus::PENDING,
                'profile_status' => ProfileStatus::INCOMPLETE,
            ]);

            // Save address into user_addresses table
            if ($request->has('addresses') && is_array($request->addresses) && count($request->addresses) > 0) {
                foreach ($request->addresses as $index => $addr) {
                    if (! empty($addr['address']) || ! empty($addr['city']) || ! empty($addr['country']) || ! empty($addr['state']) || ! empty($addr['zipcode']) || ! empty($addr['region_id'])) {
                        $isPrimary = isset($addr['is_primary']) ? (bool) $addr['is_primary'] : ($index === 0);
                        $regionId = ! empty($addr['region_id']) ? (int) $addr['region_id'] : null;
                        $region = $regionId ? Region::find($regionId) : null;

                        $user->addresses()->create([
                            'region_id' => $regionId,
                            'country' => $addr['country'] ?? null,
                            'state' => $addr['state'] ?? $region?->name,
                            'city' => $addr['city'] ?? null,
                            'zipcode' => $addr['zipcode'] ?? null,
                            'address' => $addr['address'] ?? null,
                            'is_primary' => $isPrimary,
                        ]);
                    }
                }
            } elseif ($request->filled('address') || $request->filled('region_id') || $request->filled('zipcode')) {
                $regionId = $request->filled('region_id') ? (int) $request->region_id : null;
                $region = $regionId ? Region::find($regionId) : null;

                $user->addresses()->create([
                    'region_id' => $regionId,
                    'country' => $request->country ?? null,
                    'state' => $request->state ?? $region?->name,
                    'city' => $request->city ?? null,
                    'zipcode' => $request->zipcode ?? null,
                    'address' => $request->address ?? '',
                    'is_primary' => true,
                ]);
            }

            // Assign customer role in pivot table
            $customerRole = Role::where('slug', 'customer')->first();
            if ($customerRole) {
                $user->roles()->syncWithoutDetaching([$customerRole->id]);
            }

            return $user;
        });

        // Generate registration OTP
        $otpResult = $otpService->sendOtp($user->email, OtpPurpose::REGISTRATION, $user);

        return ApiResponse::success(
            data: [
                'user_id' => $user->id,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => 'customer',
                'account_status' => AccountStatus::PENDING->value,
                'profile_status' => ProfileStatus::INCOMPLETE->value,
                'otp_required' => true,
                'otp_purpose' => OtpPurpose::REGISTRATION->value,
                'otp' => $otpResult['raw_otp'] ?? null,
            ],
            message: 'Registration successful. Please verify your account using the OTP sent to your registered contact.',
            statusCode: 201
        );
    }

    /**
     * Customer login.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::withTrashed()->where('email', strtolower($request->email))->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return ApiResponse::error(
                message: 'The provided credentials are invalid.',
                errorCode: 'ERR_INVALID_CREDENTIALS',
                statusCode: 401
            );
        }

        // Check if user is soft deleted, inactive, or suspended/blocked/deleted
        $isInactive = $user->trashed()
            || $user->status !== 'active'
            || $user->account_status === AccountStatus::SUSPENDED
            || $user->account_status === AccountStatus::BLOCKED
            || $user->account_status === AccountStatus::DELETED;

        if ($isInactive) {
            return ApiResponse::error(
                message: 'Your account is inactive.',
                errorCode: 'ERR_ACCOUNT_INACTIVE',
                statusCode: 403
            );
        }

        // Check account verification status
        if ($user->account_status === AccountStatus::PENDING) {
            return ApiResponse::error(
                message: 'Your account has not been verified. Please verify your account using the OTP.',
                errorCode: 'ERR_ACCOUNT_NOT_VERIFIED',
                statusCode: 403
            );
        }

        $user->update(['last_login_at' => now()]);
        $token = $user->createToken('customer-access-token')->plainTextToken;

        return ApiResponse::success(
            data: [
                'user' => (new UserAuthResource($user))->resolve(),
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 86400,
            ],
            message: 'Login successful.',
            statusCode: 200
        );
    }

    /**
     * Social login (Google).
     */
    public function social(SocialLoginRequest $request, SocialAuthService $socialAuthService): JsonResponse
    {
        $result = $socialAuthService->authenticate(
            provider: $request->provider,
            idToken: $request->id_token,
            role: $request->role
        );

        if (! $result['success']) {
            return ApiResponse::error(
                message: $result['message'],
                errorCode: $result['error_code'],
                statusCode: $result['status_code'] ?? 422
            );
        }

        $user = $result['user'];
        $user->update(['last_login_at' => now()]);
        $token = $user->createToken('customer-access-token')->plainTextToken;

        return ApiResponse::success(
            data: [
                'user' => (new UserAuthResource($user))->resolve(),
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 86400,
            ],
            message: 'Google authentication successful.',
            statusCode: 200
        );
    }

    /**
     * Refresh access token.
     */
    public function refresh(RefreshTokenRequest $request): JsonResponse
    {
        $user = $request->user();

        // If not authenticated via header Bearer token, check refresh_token string
        if (! $user && $request->refresh_token) {
            $tokenRecord = PersonalAccessToken::findToken($request->refresh_token);
            if ($tokenRecord) {
                $user = $tokenRecord->tokenable;
                $tokenRecord->delete();
            }
        }

        if (! $user) {
            return ApiResponse::error(
                message: 'Unauthenticated.',
                errorCode: 'ERR_INVALID_CREDENTIALS',
                statusCode: 401
            );
        }

        $isInactive = $user->trashed()
            || $user->status !== 'active'
            || $user->account_status === AccountStatus::SUSPENDED
            || $user->account_status === AccountStatus::BLOCKED
            || $user->account_status === AccountStatus::DELETED;

        if ($isInactive) {
            return ApiResponse::error(
                message: 'Your account is inactive.',
                errorCode: 'ERR_ACCOUNT_INACTIVE',
                statusCode: 403
            );
        }

        if ($request->user()?->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        $newToken = $user->createToken('customer-access-token')->plainTextToken;

        return ApiResponse::success(
            data: [
                'access_token' => $newToken,
                'token_type' => 'Bearer',
                'expires_in' => 86400,
            ],
            message: 'Access token refreshed successfully.',
            statusCode: 200
        );
    }

    /**
     * Logout user.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return ApiResponse::success(
            data: null,
            message: 'Logout successful.',
            statusCode: 200
        );
    }
}
