<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\AccountStatus;
use App\Enums\OtpPurpose;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Models\User;
use App\Services\Auth\OtpService;
use App\Services\Auth\PasswordResetService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class OtpController extends Controller
{
    /**
     * Send or resend OTP.
     */
    public function send(SendOtpRequest $request, OtpService $otpService): JsonResponse
    {
        $purpose = OtpPurpose::from($request->purpose);
        $identifier = strtolower(trim($request->identifier));

        $user = User::where('email', $identifier)->orWhere('phone', $identifier)->first();

        $result = $otpService->sendOtp($identifier, $purpose, $user);

        if (! $result['success']) {
            return ApiResponse::error(
                message: $result['message'],
                errorCode: $result['error_code'],
                statusCode: $result['status_code'] ?? 422
            );
        }

        return ApiResponse::success(
            data: $result['data'],
            message: $result['message'],
            statusCode: 200
        );
    }

    /**
     * Verify OTP.
     */
    public function verify(
        VerifyOtpRequest $request,
        OtpService $otpService,
        PasswordResetService $passwordResetService
    ): JsonResponse {
        $purpose = OtpPurpose::from($request->purpose);
        $identifier = strtolower(trim($request->identifier));
        $rawOtp = trim($request->otp);

        $verifyResult = $otpService->verifyOtp($identifier, $rawOtp, $purpose);

        if (! $verifyResult['success']) {
            return ApiResponse::error(
                message: $verifyResult['message'],
                errorCode: $verifyResult['error_code'],
                statusCode: $verifyResult['status_code'] ?? 422
            );
        }

        $user = User::where('email', $identifier)->orWhere('phone', $identifier)->first();

        // 1. Registration OTP verification
        if ($purpose === OtpPurpose::REGISTRATION) {
            if ($user) {
                $user->update([
                    'account_status' => AccountStatus::VERIFIED,
                    'email_verified_at' => now(),
                ]);
            }

            return ApiResponse::success(
                data: [
                    'user_id' => $user?->id,
                    'account_status' => AccountStatus::VERIFIED->value,
                    'profile_status' => $user?->profile_status?->value ?? 'incomplete',
                    'otp_purpose' => OtpPurpose::REGISTRATION->value,
                    'login_required' => true,
                ],
                message: 'Account verified successfully. You can now log in.',
                statusCode: 200
            );
        }

        // 2. Forgot Password OTP verification
        if ($purpose === OtpPurpose::FORGOT_PASSWORD) {
            if (! $user) {
                return ApiResponse::error(
                    message: 'The OTP is invalid or has expired.',
                    errorCode: 'ERR_INVALID_OTP',
                    statusCode: 422
                );
            }

            $resetToken = $passwordResetService->createResetToken($user, 'forgot_password');

            return ApiResponse::success(
                data: [
                    'reset_token' => $resetToken,
                    'purpose' => OtpPurpose::FORGOT_PASSWORD->value,
                    'expires_in_seconds' => PasswordResetService::TOKEN_EXPIRATION_SECONDS,
                ],
                message: 'OTP verified successfully.',
                statusCode: 200
            );
        }

        // 3. Authenticated Change Password (reset_password purpose)
        if ($purpose === OtpPurpose::RESET_PASSWORD) {
            $currentUser = $request->user() ?: $user;

            if (! $currentUser) {
                return ApiResponse::error(
                    message: 'The OTP is invalid or has expired.',
                    errorCode: 'ERR_INVALID_OTP',
                    statusCode: 422
                );
            }

            $changeToken = $passwordResetService->createResetToken($currentUser, 'reset_password');

            return ApiResponse::success(
                data: [
                    'password_change_token' => $changeToken,
                    'purpose' => OtpPurpose::RESET_PASSWORD->value,
                    'expires_in_seconds' => PasswordResetService::TOKEN_EXPIRATION_SECONDS,
                ],
                message: 'OTP verified successfully.',
                statusCode: 200
            );
        }

        return ApiResponse::success(
            data: null,
            message: 'OTP verified successfully.',
            statusCode: 200
        );
    }
}
