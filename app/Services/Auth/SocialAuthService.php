<?php

namespace App\Services\Auth;

use App\Enums\AccountStatus;
use App\Enums\AuthSource;
use App\Enums\ProfileStatus;
use App\Models\SocialAccount;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SocialAuthService
{
    /**
     * Authenticate or register a user via social provider.
     *
     * @return array{success: bool, error_code?: string, message?: string, status_code?: int, user?: User}
     */
    public function authenticate(string $provider, string $idToken, string $role = 'customer'): array
    {
        if (strtolower($provider) !== 'google') {
            return [
                'success' => false,
                'error_code' => 'ERR_SOCIAL_PROVIDER_UNSUPPORTED',
                'message' => 'Provider not supported.',
                'status_code' => 422,
            ];
        }

        $payload = $this->verifyGoogleToken($idToken);

        if (! $payload || empty($payload['sub']) || empty($payload['email'])) {
            return [
                'success' => false,
                'error_code' => 'ERR_SOCIAL_TOKEN_INVALID',
                'message' => 'Provider token is invalid or expired.',
                'status_code' => 401,
            ];
        }

        $googleUserId = (string) $payload['sub'];
        $email = strtolower((string) $payload['email']);
        $name = (string) ($payload['name'] ?? explode('@', $email)[0]);

        // 1. Check if social account is already linked
        $socialAccount = SocialAccount::with('user')
            ->where('provider', 'google')
            ->where('provider_user_id', $googleUserId)
            ->first();

        if ($socialAccount && $socialAccount->user) {
            $user = $socialAccount->user;

            if ($user->account_status === AccountStatus::SUSPENDED || $user->account_status === AccountStatus::BLOCKED) {
                return [
                    'success' => false,
                    'error_code' => 'ERR_ACCOUNT_RESTRICTED',
                    'message' => 'Your account is currently restricted.',
                    'status_code' => 403,
                ];
            }

            return [
                'success' => true,
                'user' => $user,
            ];
        }

        // 2. Check if a user with this email already exists
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            // Check if user is linked to another google account or email registered
            return [
                'success' => false,
                'error_code' => 'ERR_ACCOUNT_LINK_REQUIRED',
                'message' => 'An account already exists with this email address. Please log in using your primary method to link your account.',
                'status_code' => 409,
            ];
        }

        // 3. Create new customer with source = google, account_status = verified, profile_status = incomplete
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make(Str::random(32)),
            'role' => $role,
            'source' => AuthSource::GOOGLE,
            'account_status' => AccountStatus::VERIFIED,
            'profile_status' => ProfileStatus::INCOMPLETE,
            'email_verified_at' => now(),
        ]);

        SocialAccount::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_user_id' => $googleUserId,
            'provider_email' => $email,
        ]);

        return [
            'success' => true,
            'user' => $user,
        ];
    }

    /**
     * Verify Google ID token against Google OAuth2 tokeninfo.
     *
     * @return array<string, mixed>|null
     */
    protected function verifyGoogleToken(string $idToken): ?array
    {
        // Allow simulated token in testing / local environment
        if (app()->environment('testing') && str_starts_with($idToken, 'test_google_token_')) {
            $email = substr($idToken, strlen('test_google_token_'));

            return [
                'sub' => 'google_sub_'.md5($email),
                'email' => $email,
                'name' => 'Test Google User',
                'email_verified' => 'true',
            ];
        }

        try {
            $response = Http::timeout(10)->get('https://oauth2.googleapis.com/tokeninfo', [
                'id_token' => $idToken,
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (Exception) {
            return null;
        }

        return null;
    }
}
