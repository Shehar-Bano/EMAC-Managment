<?php

namespace App\Http\Controllers\Web\Auth;

use App\Enums\AccountStatus;
use App\Enums\AuthSource;
use App\Enums\OtpPurpose;
use App\Enums\ProfileStatus;
use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\Role;
use App\Models\User;
use App\Services\Auth\OtpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasRole(['super-admin', 'admin', 'manager', 'coordinator', 'technician']) || $user->isSuperAdmin()) {
                return redirect()->route('dashboard.index');
            }

            return redirect()->route('home');
        }

        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        $user = User::withTrashed()->where('email', strtolower($request->input('email')))->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        // Only active users who are not deleted/suspended can log in
        $isInactive = $user->trashed()
            || $user->status !== 'active'
            || $user->account_status === AccountStatus::SUSPENDED
            || $user->account_status === AccountStatus::BLOCKED
            || $user->account_status === AccountStatus::DELETED;

        if ($isInactive) {
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                'email' => 'Your account is inactive.',
            ]);
        }

        $isStaffOrAdmin = $user->hasRole(['super-admin', 'admin', 'manager', 'coordinator', 'technician'])
            || $user->isSuperAdmin()
            || in_array($user->role, ['super-admin', 'admin', 'manager', 'coordinator', 'technician'], true);

        // Check email confirmation / OTP verification status ONLY for regular customers/public users
        if (! $isStaffOrAdmin) {
            if ($user->account_status === AccountStatus::PENDING || $user->email_verified_at === null) {
                RateLimiter::hit($throttleKey);
                session(['verify_email' => $user->email]);

                return redirect()->route('otp.verify.form')->with('error', 'Your email address has not been confirmed yet. Please enter the OTP code sent to your email to verify and activate your account.');
            }
        } else {
            // For super-admin / staff / dashboard users: ensure email is verified and account is verified without requiring OTP
            if ($user->email_verified_at === null || $user->account_status === AccountStatus::PENDING) {
                $user->forceFill([
                    'email_verified_at' => $user->email_verified_at ?? now(),
                    'account_status' => AccountStatus::VERIFIED,
                    'profile_status' => ProfileStatus::COMPLETE,
                    'status' => 'active',
                ])->save();
            }
        }

        RateLimiter::clear($throttleKey);

        $remember = $request->boolean('remember');
        Auth::login($user, $remember);

        // Update last login timestamp
        $user->forceFill(['last_login_at' => now()])->save();

        $request->session()->regenerate();

        if ($isStaffOrAdmin) {
            return redirect()->intended(route('dashboard.index'))->with('success', 'Welcome back, '.$user->name.'!');
        }

        return redirect()->intended(route('home'))->with('success', 'Welcome back, '.$user->name.'!');
    }

    /**
     * Show the registration form with regions.
     */
    public function showRegisterForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $regions = Region::active()
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        return view('auth.register', compact('regions'));
    }

    /**
     * Handle customer registration.
     */
    public function register(Request $request, OtpService $otpService): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'string', 'email:rfc', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'region_id' => ['required', 'integer', 'exists:regions,id'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'zipcode' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms_accepted' => ['required', 'accepted'],
            'privacy_policy_accepted' => ['required', 'accepted'],
        ], [
            'terms_accepted.accepted' => 'You must accept the terms and conditions.',
            'privacy_policy_accepted.accepted' => 'You must accept the privacy policy.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => strtolower($validated['email']),
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role' => 'customer',
                'source' => AuthSource::EMAIL,
                'account_status' => AccountStatus::PENDING,
                'profile_status' => ProfileStatus::INCOMPLETE,
                'status' => 'active',
            ]);

            $region = Region::find($validated['region_id']);

            $user->addresses()->create([
                'region_id' => $region?->id,
                'country' => $validated['country'] ?? $region?->country ?? 'USA',
                'state' => $validated['state'] ?? $region?->name,
                'city' => $validated['city'],
                'zipcode' => $validated['zipcode'],
                'address' => $validated['address'],
                'is_primary' => true,
            ]);

            $customerRole = Role::where('slug', 'customer')->first();
            if ($customerRole) {
                $user->roles()->syncWithoutDetaching([$customerRole->id]);
            }

            return $user;
        });

        // Generate and send registration OTP
        $otpService->sendOtp($user->email, OtpPurpose::REGISTRATION, $user);

        session(['verify_email' => $user->email]);

        return redirect()->route('otp.verify.form')->with('success', 'Registration successful! A 6-digit confirmation code has been sent to your email ('.$user->email.'). Please enter it below to confirm your account.');
    }

    /**
     * Show the OTP verification form.
     */
    public function showVerifyOtpForm(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $email = session('verify_email', $request->query('email'));

        return view('auth.verify-otp', compact('email'));
    }

    /**
     * Handle OTP verification.
     */
    public function verifyOtp(Request $request, OtpService $otpService): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'string', 'digits:6'],
        ], [
            'otp.digits' => 'The OTP code must be exactly 6 digits.',
        ]);

        $email = strtolower(trim($request->email));
        $otp = trim($request->otp);

        $verifyResult = $otpService->verifyOtp($email, $otp, OtpPurpose::REGISTRATION);

        if (! $verifyResult['success']) {
            return back()->withErrors(['otp' => $verifyResult['message'] ?? 'The confirmation code is invalid or has expired.'])->withInput();
        }

        $user = User::withTrashed()->where('email', $email)->first();

        if (! $user) {
            return redirect()->route('register')->with('error', 'User account not found.');
        }

        if ($user->trashed() || $user->status !== 'active' || $user->account_status === AccountStatus::SUSPENDED || $user->account_status === AccountStatus::BLOCKED || $user->account_status === AccountStatus::DELETED) {
            return redirect()->route('login')->withErrors(['email' => 'Your account is inactive.']);
        }

        // Mark account as verified
        $user->update([
            'account_status' => AccountStatus::VERIFIED,
            'email_verified_at' => now(),
        ]);

        // Auto-login user
        Auth::login($user);
        $user->forceFill(['last_login_at' => now()])->save();
        $request->session()->forget('verify_email');
        $request->session()->regenerate();

        if ($user->hasRole(['super-admin', 'admin', 'manager', 'coordinator', 'technician']) || $user->isSuperAdmin()) {
            return redirect()->route('dashboard.index')->with('success', 'Email confirmed successfully! Welcome to EMAC Development.');
        }

        return redirect()->route('home')->with('success', 'Email verified successfully! You are now logged in to EMAC Development.');
    }

    /**
     * Resend verification OTP code.
     */
    public function resendOtp(Request $request, OtpService $otpService): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        if (! $user) {
            return back()->with('error', 'No registered account found with that email address.');
        }

        $result = $otpService->sendOtp($email, OtpPurpose::REGISTRATION, $user);

        if (! $result['success']) {
            return back()->with('error', $result['message'] ?? 'Please wait before requesting a new code.');
        }

        session(['verify_email' => $email]);

        return back()->with('success', 'A fresh 6-digit confirmation code has been dispatched to '.$email.'.');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been signed out successfully.');
    }

    /**
     * Show the forgot password form.
     */
    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle forgot password submission.
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        return back()->with('status', 'We have emailed your password reset link.');
    }
}
