<?php

namespace App\Services\Auth;

use App\Models\PasswordResetAuthorization;
use App\Models\User;
use Illuminate\Support\Str;

class PasswordResetService
{
    public const TOKEN_EXPIRATION_SECONDS = 600; // 10 minutes

    /**
     * Create a new short-lived reset authorization token.
     */
    public function createResetToken(User $user, string $purpose = 'forgot_password'): string
    {
        // Invalidate older unused reset tokens for this user and purpose
        PasswordResetAuthorization::where('user_id', $user->id)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->delete();

        $plainToken = Str::random(64);
        $tokenHash = hash('sha256', $plainToken);

        PasswordResetAuthorization::create([
            'user_id' => $user->id,
            'token_hash' => $tokenHash,
            'purpose' => $purpose,
            'expires_at' => now()->addSeconds(self::TOKEN_EXPIRATION_SECONDS),
        ]);

        return $plainToken;
    }

    /**
     * Validate and consume a reset token.
     *
     * @return array{success: bool, error_code?: string, message?: string, status_code?: int, user?: User}
     */
    public function validateAndConsumeToken(string $plainToken, string $purpose = 'forgot_password', ?User $currentUser = null): array
    {
        $tokenHash = hash('sha256', $plainToken);

        $authQuery = PasswordResetAuthorization::with('user')
            ->where('token_hash', $tokenHash)
            ->where('purpose', $purpose);

        if ($currentUser) {
            $authQuery->where('user_id', $currentUser->id);
        }

        $record = $authQuery->first();

        if (! $record || $record->isConsumed()) {
            return [
                'success' => false,
                'error_code' => 'ERR_RESET_TOKEN_INVALID',
                'message' => 'The password reset token is invalid or has already been used.',
                'status_code' => 422,
            ];
        }

        if ($record->isExpired()) {
            return [
                'success' => false,
                'error_code' => 'ERR_RESET_TOKEN_EXPIRED',
                'message' => 'The password reset token has expired.',
                'status_code' => 422,
            ];
        }

        // Mark as consumed
        $record->update(['consumed_at' => now()]);

        // Invalidate any other active reset tokens for this user
        PasswordResetAuthorization::where('user_id', $record->user_id)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->delete();

        return [
            'success' => true,
            'user' => $record->user,
        ];
    }
}
