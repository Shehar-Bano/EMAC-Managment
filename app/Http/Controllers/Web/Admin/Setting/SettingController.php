<?php

namespace App\Http\Controllers\Web\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SettingController extends Controller
{
    /**
     * Display the system settings configuration panel.
     */
    public function index(): View
    {
        $this->authorize('settings.view');

        $settings = [
            'app_name' => setting('app_name', config('app.name', 'EMAC Management')),
            'app_env' => config('app.env', 'production'),
            'app_url' => config('app.url', 'http://localhost:8000'),
            'timezone' => setting('timezone', config('app.timezone', 'Asia/Karachi')),
            'locale' => config('app.locale', 'en'),
            'session_lifetime' => (int) setting('session_lifetime', config('session.lifetime', 120)),
            'dashboard_logo' => setting('dashboard_logo'),
            'website_logo' => setting('website_logo'),
            'support_email' => setting('support_email', 'info@emacdevelopment.com'),
            'support_phone' => setting('support_phone', '+1 (800) 555-0199'),
        ];

        $superAdmin = auth()->user() ?? User::where('role', 'super-admin')->first();

        return view('dashboard.modules.settings.index', compact('settings', 'superAdmin'));
    }

    /**
     * Update system settings and dynamic branding logos.
     */
    public function update(Request $request): RedirectResponse
    {
        $this->authorize('settings.edit');

        $validated = $request->validate([
            'app_name' => ['required', 'string', 'max:100'],
            'timezone' => ['required', 'string'],
            'session_lifetime' => ['required', 'integer', 'min:15', 'max:1440'],
            'support_email' => ['nullable', 'email', 'max:100'],
            'support_phone' => ['nullable', 'string', 'max:50'],
            'dashboard_logo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'website_logo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
        ]);

        // 1. Handle Dashboard Logo Upload
        if ($request->hasFile('dashboard_logo')) {
            $oldDashboardLogo = setting('dashboard_logo');
            if ($oldDashboardLogo && Storage::disk('public')->exists($oldDashboardLogo)) {
                Storage::disk('public')->delete($oldDashboardLogo);
            }

            $dashboardPath = $request->file('dashboard_logo')->store('branding', 'public');
            Setting::set('dashboard_logo', $dashboardPath, 'file', 'branding');
        }

        // 2. Handle Website Logo Upload
        if ($request->hasFile('website_logo')) {
            $oldWebsiteLogo = setting('website_logo');
            if ($oldWebsiteLogo && Storage::disk('public')->exists($oldWebsiteLogo)) {
                Storage::disk('public')->delete($oldWebsiteLogo);
            }

            $websitePath = $request->file('website_logo')->store('branding', 'public');
            Setting::set('website_logo', $websitePath, 'file', 'branding');
        }

        // 3. Save General Parameters
        Setting::set('app_name', $validated['app_name'], 'string', 'general');
        Setting::set('timezone', $validated['timezone'], 'string', 'general');
        Setting::set('session_lifetime', (int) $validated['session_lifetime'], 'integer', 'general');
        Setting::set('support_email', $validated['support_email'] ?? '', 'string', 'contact');
        Setting::set('support_phone', $validated['support_phone'] ?? '', 'string', 'contact');

        return redirect()
            ->route('dashboard.settings.index')
            ->with('success', 'System parameters and branding logos updated successfully.');
    }

    /**
     * Update Super Admin authentication credentials from Settings.
     */
    public function updateCredentials(Request $request): RedirectResponse
    {
        $this->authorize('settings.edit');

        /** @var User $user */
        $user = auth()->user() ?? User::where('role', 'super-admin')->firstOrFail();

        $validated = $request->validate([
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'admin_phone' => ['nullable', 'string', 'max:30'],
            'current_password' => ['nullable', 'required_with:new_password', 'string'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // If updating password, verify current password first
        if ($request->filled('new_password')) {
            if (! Hash::check($request->current_password, $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => 'The provided current password does not match our records.',
                ]);
            }

            $user->password = Hash::make($validated['new_password']);
        }

        $user->name = $validated['admin_name'];
        $user->email = $validated['admin_email'];
        $user->phone = $validated['admin_phone'] ?? null;
        $user->save();

        return redirect()
            ->route('dashboard.settings.index')
            ->with('success', 'Super Admin profile and login credentials updated successfully.');
    }

    /**
     * Remove custom uploaded logo and reset back to default vector SVG.
     */
    public function removeLogo(Request $request, string $type): RedirectResponse
    {
        $this->authorize('settings.edit');

        if (! in_array($type, ['dashboard_logo', 'website_logo'], true)) {
            abort(404);
        }

        $oldLogo = setting($type);
        if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
            Storage::disk('public')->delete($oldLogo);
        }

        Setting::set($type, null, 'file', 'branding');

        $label = $type === 'dashboard_logo' ? 'Dashboard' : 'Website';

        return redirect()
            ->route('dashboard.settings.index')
            ->with('success', "{$label} logo reset to default SVG successfully.");
    }
}
