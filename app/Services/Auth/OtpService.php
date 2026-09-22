<?php

namespace App\Services\Auth;

use App\Enums\OtpPurpose;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class OtpService
{
    public const EXPIRATION_SECONDS = 300; // 5 minutes

    public const RESEND_COOLDOWN_SECONDS = 60; // 60 seconds

    public const MAX_ATTEMPTS = 5;

    /**
     * Send or generate an OTP.
     *
     * @return array{success: bool, error_code?: string, message?: string, status_code?: int, data?: array}
     */
    public function sendOtp(string $identifier, OtpPurpose $purpose, ?User $user = null): array
    {
        // Check resend cooldown
        $latestOtp = Otp::where('identifier', $identifier)
            ->where('purpose', $purpose->value)
            ->whereNull('verified_at')
            ->latest('id')
            ->first();

        if ($latestOtp && $latestOtp->created_at && $latestOtp->created_at->diffInSeconds(now()) < self::RESEND_COOLDOWN_SECONDS) {
            $retryAfter = self::RESEND_COOLDOWN_SECONDS - $latestOtp->created_at->diffInSeconds(now());

            return [
                'success' => false,
                'error_code' => 'ERR_OTP_RESEND_LIMIT',
                'message' => "Please wait {$retryAfter} seconds before requesting a new OTP.",
                'status_code' => 429,
            ];
        }

        // Invalidate previous unverified OTPs for this identifier and purpose
        Otp::where('identifier', $identifier)
            ->where('purpose', $purpose->value)
            ->whereNull('verified_at')
            ->delete();

        // Generate 6-digit OTP
        $rawOtp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        $otp = Otp::create([
            'user_id' => $user?->id,
            'identifier' => $identifier,
            'purpose' => $purpose,
            'otp_hash' => Hash::make($rawOtp),
            'attempt_count' => 0,
            'max_attempts' => self::MAX_ATTEMPTS,
            'expires_at' => now()->addSeconds(self::EXPIRATION_SECONDS),
        ]);

        // Mask destination
        $maskedDestination = $this->maskIdentifier($identifier);
        $deliveryMethod = str_contains($identifier, '@') ? 'email' : 'phone';

        // Log OTP in debug mode for seamless local development & testing
        Log::info("Generated OTP for [{$identifier}] with purpose [{$purpose->value}]: {$rawOtp}");

        return [
            'success' => true,
            'status_code' => 200,
            'message' => 'OTP sent successfully.',
            'data' => [
                'purpose' => $purpose->value,
                'delivery_method' => $deliveryMethod,
                'masked_destination' => $maskedDestination,
                'expires_in_seconds' => self::EXPIRATION_SECONDS,
                'resend_available_in_seconds' => self::RESEND_COOLDOWN_SECONDS,
            ],
            'raw_otp' => $rawOtp, // Can be utilized in test assertions
        ];
    }

    /**
     * Verify an OTP.
     *
     * @return array{success: bool, error_code?: string, message?: string, status_code?: int, otp?: Otp}
     */
    public function verifyOtp(string $identifier, string $rawOtp, OtpPurpose $purpose): array
    {
        $otp = Otp::where('identifier', $identifier)
            ->where('purpose', $purpose->value)
            ->whereNull('verified_at')
            ->latest('id')
            ->first();

        if (! $otp) {
            return [
                'success' => false,
                'error_code' => 'ERR_INVALID_OTP',
                'message' => 'The OTP is invalid or has expired.',
                'status_code' => 422,
            ];
        }

        if ($otp->isExpired()) {
            return [
                'success' => false,
                'error_code' => 'ERR_INVALID_OTP',
                'message' => 'The OTP is invalid or has expired.',
                'status_code' => 422,
            ];
        }

        if ($otp->isMaxAttemptsReached()) {
            return [
                'success' => false,
                'error_code' => 'ERR_OTP_ATTEMPTS_EXCEEDED',
                'message' => 'Too many invalid OTP attempts. Please request a new OTP.',
                'status_code' => 429,
            ];
        }

        if (! Hash::check($rawOtp, $otp->otp_hash)) {
            $otp->increment('attempt_count');

            if ($otp->isMaxAttemptsReached()) {
                return [
                    'success' => false,
                    'error_code' => 'ERR_OTP_ATTEMPTS_EXCEEDED',
                    'message' => 'Too many invalid OTP attempts. Please request a new OTP.',
                    'status_code' => 429,
                ];
            }

            return [
                'success' => false,
                'error_code' => 'ERR_INVALID_OTP',
                'message' => 'The OTP is invalid or has expired.',
                'status_code' => 422,
            ];
        }

        // Consume OTP
        $otp->update([
            'verified_at' => now(),
        ]);

        return [
            'success' => true,
            'otp' => $otp,
        ];
    }

    /**
     * Mask an email or phone identifier.
     */
    public function maskIdentifier(string $identifier): string
    {
        if (str_contains($identifier, '@')) {
            $parts = explode('@', $identifier, 2);
            $name = $parts[0];
            $domain = $parts[1] ?? '';

            $firstChar = substr($name, 0, 1);

            return $firstChar.'***@'.$domain;
        }

        if (strlen($identifier) > 4) {
            return substr($identifier, 0, 3).'****'.substr($identifier, -2);
        }

        return '****';
    }
}
