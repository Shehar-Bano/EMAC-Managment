<x-dashboard.layout :title="'System Settings & Branding — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">System Settings</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">System Configuration & Branding</h1>
            <p class="text-xs text-slate-500 mt-1">Manage platform branding logos, administrator credentials, contact channels, and system policies</p>
        </div>
    </x-slot:header>

    <div class="max-w-5xl space-y-8">

        {{-- Section 1: Dynamic Branding & Logos --}}
        <form method="POST" action="{{ route('dashboard.settings.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                {{-- Logos Card --}}
                <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-6">
                    <div class="pb-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-amber-50 border border-[#C5A059]/30 text-[#8F6B20] flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </span>
                                <span>Dynamic Branding & Logos</span>
                            </h2>
                            <p class="text-xs text-slate-400 mt-1">Upload separate, high-resolution logos for the ERP Dashboard and the Public Website</p>
                        </div>
                        <span class="text-[11px] font-bold text-[#8F6B20] bg-amber-50 border border-[#C5A059]/30 px-3 py-1 rounded-full w-fit">
                            PNG, JPG, SVG, WEBP (Max 2MB)
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- 1. Dashboard Logo --}}
                        <div class="p-5 rounded-2xl bg-gradient-to-b from-[#FAF8F4] to-white border border-slate-200/90 space-y-4 shadow-2xs">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">ERP Dashboard Logo</h3>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Sidebar, admin header, and login screens</p>
                                </div>
                                <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">
                                    Dashboard
                                </span>
                            </div>

                            {{-- Live Preview Box --}}
                            <div class="p-4 rounded-xl bg-white border border-slate-200 flex flex-col items-center justify-center min-h-[100px] text-center relative group">
                                @if ($settings['dashboard_logo'])
                                    <img
                                        src="{{ asset('storage/' . $settings['dashboard_logo']) }}"
                                        alt="Dashboard Logo"
                                        class="max-h-12 max-w-[200px] object-contain"
                                    >
                                    <div class="mt-2 text-[10px] font-semibold text-emerald-600 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <span>Custom Dashboard Logo Active</span>
                                    </div>
                                @else
                                    <x-logo context="dashboard" :theme="'dark'" size="md" />
                                    <div class="mt-2 text-[10px] font-semibold text-slate-400">
                                        Default Vector SVG Logo Active
                                    </div>
                                @endif
                            </div>

                            {{-- Upload Input --}}
                            <div>
                                <label for="dashboard_logo" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Upload New Dashboard Logo
                                </label>
                                <input
                                    type="file"
                                    name="dashboard_logo"
                                    id="dashboard_logo"
                                    accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                    class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800 file:cursor-pointer border border-slate-300 rounded-xl bg-white focus:outline-none focus:border-[#C5A059] transition-all"
                                >
                                <p class="text-[10px] text-slate-400 mt-1">Recommended size: Transparent PNG or SVG, max height 50px.</p>
                            </div>

                            {{-- Reset Button if custom logo uploaded --}}
                            @if ($settings['dashboard_logo'])
                                <div class="pt-2 border-t border-slate-100 flex justify-end">
                                    <button
                                        type="button"
                                        onclick="document.getElementById('remove-dashboard-logo-form').submit()"
                                        class="text-[11px] font-bold text-rose-600 hover:text-rose-700 hover:underline cursor-pointer flex items-center gap-1"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        <span>Reset to Default SVG</span>
                                    </button>
                                </div>
                            @endif
                        </div>

                        {{-- 2. Website Logo --}}
                        <div class="p-5 rounded-2xl bg-gradient-to-b from-[#FAF8F4] to-white border border-slate-200/90 space-y-4 shadow-2xs">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">Public Website Logo</h3>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Navbar, customer portals, and footer</p>
                                </div>
                                <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-amber-50 text-[#8F6B20] border border-[#C5A059]/30">
                                    Website
                                </span>
                            </div>

                            {{-- Live Preview Box --}}
                            <div class="p-4 rounded-xl bg-white border border-slate-200 flex flex-col items-center justify-center min-h-[100px] text-center relative group">
                                @if ($settings['website_logo'])
                                    <img
                                        src="{{ asset('storage/' . $settings['website_logo']) }}"
                                        alt="Website Logo"
                                        class="max-h-12 max-w-[200px] object-contain"
                                    >
                                    <div class="mt-2 text-[10px] font-semibold text-emerald-600 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <span>Custom Website Logo Active</span>
                                    </div>
                                @else
                                    <x-logo context="website" :theme="'dark'" size="md" />
                                    <div class="mt-2 text-[10px] font-semibold text-slate-400">
                                        Default Vector SVG Logo Active
                                    </div>
                                @endif
                            </div>

                            {{-- Upload Input --}}
                            <div>
                                <label for="website_logo" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Upload New Website Logo
                                </label>
                                <input
                                    type="file"
                                    name="website_logo"
                                    id="website_logo"
                                    accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                    class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:bg-slate-800 file:cursor-pointer border border-slate-300 rounded-xl bg-white focus:outline-none focus:border-[#C5A059] transition-all"
                                >
                                <p class="text-[10px] text-slate-400 mt-1">Recommended size: Transparent PNG or SVG, max height 60px.</p>
                            </div>

                            {{-- Reset Button if custom logo uploaded --}}
                            @if ($settings['website_logo'])
                                <div class="pt-2 border-t border-slate-100 flex justify-end">
                                    <button
                                        type="button"
                                        onclick="document.getElementById('remove-website-logo-form').submit()"
                                        class="text-[11px] font-bold text-rose-600 hover:text-rose-700 hover:underline cursor-pointer flex items-center gap-1"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        <span>Reset to Default SVG</span>
                                    </button>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>

                {{-- General Platform & Contact Parameters Card --}}
                <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-6">
                    <div class="pb-4 border-b border-slate-100">
                        <h2 class="text-base font-bold text-slate-900 flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 flex items-center justify-center">
                                <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </span>
                            <span>General Platform & Contact Parameters</span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">Configure global application title, localization timezone, session duration, and customer contact info</p>
                    </div>

                    <div class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <x-input
                                label="Application Name"
                                name="app_name"
                                :value="$settings['app_name']"
                                required
                            />

                            <div>
                                <label for="timezone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    System Timezone <span class="text-rose-500">*</span>
                                </label>
                                <select
                                    name="timezone"
                                    id="timezone"
                                    required
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-xs font-semibold text-slate-800 focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] transition-all"
                                >
                                    <option value="Asia/Karachi" {{ $settings['timezone'] === 'Asia/Karachi' ? 'selected' : '' }}>Asia/Karachi (UTC+05:00)</option>
                                    <option value="America/New_York" {{ $settings['timezone'] === 'America/New_York' ? 'selected' : '' }}>America/New_York (EST/EDT)</option>
                                    <option value="America/Cayman" {{ $settings['timezone'] === 'America/Cayman' ? 'selected' : '' }}>America/Cayman (EST)</option>
                                    <option value="America/Jamaica" {{ $settings['timezone'] === 'America/Jamaica' ? 'selected' : '' }}>America/Jamaica (EST)</option>
                                    <option value="UTC" {{ $settings['timezone'] === 'UTC' ? 'selected' : '' }}>UTC (Universal Time)</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <x-input
                                label="Session Lifetime (Minutes)"
                                name="session_lifetime"
                                type="number"
                                :value="$settings['session_lifetime']"
                                required
                                hint="Inactive duration before automatic logout."
                            />

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Environment Mode
                                </label>
                                <input
                                    type="text"
                                    disabled
                                    value="{{ strtoupper($settings['app_env']) }}"
                                    class="block w-full rounded-xl border border-slate-200 bg-slate-100 text-slate-600 px-3.5 py-2.5 text-xs font-mono font-bold"
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-3 border-t border-slate-100">
                            <x-input
                                label="Public Support Email"
                                name="support_email"
                                type="email"
                                :value="$settings['support_email']"
                                placeholder="info@emacdevelopment.com"
                            />

                            <x-input
                                label="Public Support Phone"
                                name="support_phone"
                                type="text"
                                :value="$settings['support_phone']"
                                placeholder="+1 (800) 555-0199"
                            />
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                        <button
                            type="submit"
                            style="background: linear-gradient(135deg, #D4AF37 0%, #B8903B 50%, #8F6B20 100%);"
                            class="px-6 py-2.5 rounded-xl text-xs font-extrabold text-white hover:brightness-110 shadow-md transition-all cursor-pointer flex items-center gap-2"
                        >
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>Save Platform Settings & Logos</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>

        {{-- Section 2: Super Admin Profile & Login Credentials --}}
        <div class="p-6 sm:p-8 rounded-3xl bg-white border-2 border-[#C5A059]/40 shadow-xs space-y-6 relative overflow-hidden">
            <div class="pb-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-slate-900 text-[#C5A059] flex items-center justify-center font-bold text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </span>
                        <span>Super Administrator Login Credentials</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Update the master administrative account name, login email address, and security password</p>
                </div>
                <span class="text-[11px] font-mono font-bold text-[#8F6B20] bg-[#FAF8F4] border border-[#C5A059]/40 px-3 py-1 rounded-full w-fit">
                    Master Admin Account
                </span>
            </div>

            <form method="POST" action="{{ route('dashboard.settings.credentials') }}" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Profile Info --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <x-input
                        label="Administrator Full Name"
                        name="admin_name"
                        :value="old('admin_name', $superAdmin?->name)"
                        required
                    />

                    <x-input
                        label="Master Login Email"
                        name="admin_email"
                        type="email"
                        :value="old('admin_email', $superAdmin?->email)"
                        required
                    />

                    <x-input
                        label="Contact Phone"
                        name="admin_phone"
                        type="text"
                        :value="old('admin_phone', $superAdmin?->phone)"
                    />
                </div>

                {{-- Password Change Sub-Card --}}
                <div class="p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-[#FAF8F4] via-white to-[#F5EFE6] border border-[#C5A059]/30 space-y-4 shadow-2xs">
                    <div class="flex items-center justify-between pb-2 border-b border-[#C5A059]/20">
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#8F6B20]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                <span>Change Administrator Password</span>
                            </h3>
                            <p class="text-[11px] text-slate-500 mt-0.5">Leave these fields blank if you do not wish to change your password.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div x-data="{ show: false }">
                            <label for="current_password" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Current Password
                            </label>
                            <div class="relative">
                                <input
                                    :type="show ? 'text' : 'password'"
                                    name="current_password"
                                    id="current_password"
                                    class="w-full pl-3.5 pr-10 py-2 text-xs rounded-xl border border-slate-300 focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] bg-white transition-all font-medium text-slate-800"
                                    placeholder="Required if changing password"
                                >
                                <button
                                    type="button"
                                    @click="show = !show"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer focus:outline-none"
                                    tabindex="-1"
                                    title="Toggle password visibility"
                                >
                                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <svg x-show="show" x-cloak class="w-4 h-4 text-[#8F6B20]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                                </button>
                            </div>
                            @error('current_password')
                                <p class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-data="{ show: false }">
                            <label for="new_password" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                                New Password
                            </label>
                            <div class="relative">
                                <input
                                    :type="show ? 'text' : 'password'"
                                    name="new_password"
                                    id="new_password"
                                    class="w-full pl-3.5 pr-10 py-2 text-xs rounded-xl border border-slate-300 focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] bg-white transition-all font-medium text-slate-800"
                                    placeholder="Min 8 characters"
                                >
                                <button
                                    type="button"
                                    @click="show = !show"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer focus:outline-none"
                                    tabindex="-1"
                                    title="Toggle password visibility"
                                >
                                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <svg x-show="show" x-cloak class="w-4 h-4 text-[#8F6B20]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                                </button>
                            </div>
                            @error('new_password')
                                <p class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-data="{ show: false }">
                            <label for="new_password_confirmation" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Confirm New Password
                            </label>
                            <div class="relative">
                                <input
                                    :type="show ? 'text' : 'password'"
                                    name="new_password_confirmation"
                                    id="new_password_confirmation"
                                    class="w-full pl-3.5 pr-10 py-2 text-xs rounded-xl border border-slate-300 focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] bg-white transition-all font-medium text-slate-800"
                                    placeholder="Re-type new password"
                                >
                                <button
                                    type="button"
                                    @click="show = !show"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer focus:outline-none"
                                    tabindex="-1"
                                    title="Toggle password visibility"
                                >
                                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <svg x-show="show" x-cloak class="w-4 h-4 text-[#8F6B20]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <button
                        type="submit"
                        class="px-6 py-2.5 rounded-xl text-xs font-extrabold bg-slate-900 hover:bg-slate-800 text-white shadow-md transition-all cursor-pointer flex items-center gap-2"
                    >
                        <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <span>Update Super Admin Credentials</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

    {{-- Hidden Forms for Resetting Logos --}}
    @if ($settings['dashboard_logo'])
        <form id="remove-dashboard-logo-form" method="POST" action="{{ route('dashboard.settings.remove-logo', 'dashboard_logo') }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endif

    @if ($settings['website_logo'])
        <form id="remove-website-logo-form" method="POST" action="{{ route('dashboard.settings.remove-logo', 'website_logo') }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endif

</x-dashboard.layout>
