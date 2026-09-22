<?php

namespace App\Http\Controllers\Web\Admin\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display the system settings configuration panel.
     */
    public function index(): View
    {
        $this->authorize('settings.view');

        $settings = [
            'app_name' => config('app.name', 'EMAC Management'),
            'app_env' => config('app.env', 'production'),
            'app_url' => config('app.url', 'http://localhost:8000'),
            'timezone' => config('app.timezone', 'Asia/Karachi'),
            'locale' => config('app.locale', 'en'),
            'session_lifetime' => config('session.lifetime', 120),
            'pagination_default' => 10,
        ];

        return view('dashboard.modules.settings.index', compact('settings'));
    }

    /**
     * Update system settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $this->authorize('settings.edit');

        $request->validate([
            'app_name' => ['required', 'string', 'max:100'],
            'timezone' => ['required', 'string'],
            'session_lifetime' => ['required', 'integer', 'min:15', 'max:1440'],
        ]);

        return redirect()
            ->route('dashboard.settings.index')
            ->with('success', 'System parameters updated successfully.');
    }
}
