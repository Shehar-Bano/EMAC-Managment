<?php

namespace App\Http\Middleware;

use App\Enums\AccountStatus;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            $rawAttributes = $user->getAttributes();
            $deletedAt = array_key_exists('deleted_at', $rawAttributes) ? $rawAttributes['deleted_at'] : null;
            $status = array_key_exists('status', $rawAttributes) ? $rawAttributes['status'] : 'active';
            $accountStatus = $user->account_status;

            $isInactive = $deletedAt !== null
                || $status !== 'active'
                || $accountStatus === AccountStatus::SUSPENDED
                || $accountStatus === AccountStatus::BLOCKED
                || $accountStatus === AccountStatus::DELETED;

            if ($isInactive) {
                if ($request->is('api/*') || $request->expectsJson()) {
                    $user->currentAccessToken()?->delete();

                    return ApiResponse::error(
                        message: 'Your account is inactive.',
                        errorCode: 'ERR_ACCOUNT_INACTIVE',
                        statusCode: 403
                    );
                }

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Your account is inactive.',
                ]);
            }
        }

        return $next($request);
    }
}
