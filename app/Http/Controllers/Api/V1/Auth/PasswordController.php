<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\OtpPurpose;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordOtpRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use App\Services\Auth\OtpService;
use App\Services\Auth\PasswordResetService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    /**
     * Request OTP for forgot password.
     */
    public function forgotPassword(ForgotPasswordRequest $request, OtpService $otpService): JsonResponse
    {
        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        if ($user) {
            $otpService->sendOtp($email, OtpPurpose::FORGOT_PASSWORD, $user);
        }

        // Always return generic response to prevent email enumeration
        return ApiResponse::success(
            data: [
                'otp_required' => true,
                'purpose' => OtpPurpose::FORGOT_PASSWORD->value,
                'expires_in_seconds' => OtpService::EXPIRATION_SECONDS,
                'resend_available_in_seconds' => OtpService::RESEND_COOLDOWN_SECONDS,
            ],
            message: 'If an account exists for this email, an OTP has been sent.',
            statusCode: 200
        );
    }

    /**
     * Reset forgotten password using short-lived reset token.
     */
    public function resetPassword(
        ResetPasswordRequest $request,
        PasswordResetService $passwordResetService
    ): JsonResponse {
        $validation = $passwordResetService->validateAndConsumeToken(
            plainToken: $request->reset_token,
            purpose: 'forgot_password'
        );

        if (! $validation['success']) {
            return ApiResponse::error(
                message: $validation['message'],
                errorCode: $validation['error_code'],
                statusCode: $validation['status_code'] ?? 422
            );
        }

        $user = $validation['user'];
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Revoke all existing access tokens on password reset
        $user->tokens()->delete();

        return ApiResponse::success(
            data: null,
            message: 'Password reset successfully. Please log in using your new password.',
            statusCode: 200
        );
    }

    /**
     * Request OTP for changing password (authenticated).
     */
    public function requestChangeOtp(ChangePasswordOtpRequest $request, OtpService $otpService): JsonResponse
    {
        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return ApiResponse::error(
                message: 'The current password provided is incorrect.',
                errorCode: 'ERR_INVALID_CREDENTIALS',
                statusCode: 422
            );
        }

        $result = $otpService->sendOtp($user->email, OtpPurpose::RESET_PASSWORD, $user);

        if (! $result['success']) {
            return ApiResponse::error(
                message: $result['message'],
                errorCode: $result['error_code'],
                statusCode: $result['status_code'] ?? 422
            );
        }

        return ApiResponse::success(
            data: [
                'purpose' => OtpPurpose::RESET_PASSWORD->value,
                'expires_in_seconds' => OtpService::EXPIRATION_SECONDS,
                'resend_available_in_seconds' => OtpService::RESEND_COOLDOWN_SECONDS,
            ],
            message: 'OTP sent successfully.',
            statusCode: 200
        );
    }

    /**
     * Change password completion using short-lived password change token.
     */
    public function changePassword(
        ChangePasswordRequest $request,
        PasswordResetService $passwordResetService
    ): JsonResponse {
        $user = $request->user();

        $validation = $passwordResetService->validateAndConsumeToken(
            plainToken: $request->password_change_token,
            purpose: 'reset_password',
            currentUser: $user
        );

        if (! $validation['success']) {
            return ApiResponse::error(
                message: $validation['message'],
                errorCode: $validation['error_code'],
                statusCode: $validation['status_code'] ?? 422
            );
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return ApiResponse::success(
            data: null,
            message: 'Password changed successfully.',
            statusCode: 200
        );
    }
}
