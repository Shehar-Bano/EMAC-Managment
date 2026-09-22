<x-dashboard.layout :title="'System Settings — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">System Settings</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">System Configuration & Policies</h1>
            <p class="text-xs text-slate-500 mt-1">Configure global application parameters, localization, and enterprise security policies</p>
        </div>
    </x-slot:header>

    <div class="max-w-4xl">
        <form method="POST" action="{{ route('dashboard.settings.update') }}">
            @csrf
            @method('PUT')

            <x-card title="General Platform Configuration" subtitle="Core enterprise identifiers and session timeout parameters">
                <div class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <x-input
                            label="Application Name"
                            name="app_name"
                            :value="$settings['app_name']"
                            required
                        />

                        <x-input
                            label="System Timezone"
                            name="timezone"
                            :value="$settings['timezone']"
                            required
                        />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <x-input
                            label="Session Lifetime (Minutes)"
                            name="session_lifetime"
                            type="number"
                            :value="$settings['session_lifetime']"
                            required
                            hint="Default inactive duration before employee re-authentication is required."
                        />

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Environment Mode
                            </label>
                            <input
                                type="text"
                                disabled
                                value="{{ strtoupper($settings['app_env']) }}"
                                class="block w-full rounded-lg border border-slate-200 bg-slate-100 text-slate-600 px-3.5 py-2 text-sm font-mono"
                            >
                        </div>
                    </div>
                </div>

                <x-slot:footer>
                    <div class="flex items-center justify-end gap-3 w-full">
                        <x-button type="submit" variant="primary">
                            Update Settings
                        </x-button>
                    </div>
                </x-slot:footer>
            </x-card>
        </form>
    </div>

</x-dashboard.layout>
