<x-dashboard.layout :title="'Create Region — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <a href="{{ route('dashboard.regions.index') }}" class="hover:text-[#C5A059]">Regions</a>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">Add New Region</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Add New Service Region</h1>
            <p class="text-xs text-slate-500 mt-1">Configure geographical territory, ISO code, currency, and operational status</p>
        </div>

        <div>
            <x-button href="{{ route('dashboard.regions.index') }}" variant="secondary" size="sm">
                &larr; Back to Regions
            </x-button>
        </div>
    </x-slot:header>

    <div class="max-w-3xl">
        <form method="POST" action="{{ route('dashboard.regions.store') }}" class="space-y-6">
            @csrf

            <x-card title="Region Details" subtitle="Provide geographical details and currency configuration">
                <div class="space-y-4">
                    {{-- Name --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Region Name *</label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="e.g. Grand Cayman, Florida, Jamaica"
                            required
                            class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none transition-colors"
                        >
                        @error('name')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        {{-- Slug (Optional) --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Slug Identifier</label>
                            <input
                                type="text"
                                name="slug"
                                value="{{ old('slug') }}"
                                placeholder="e.g. grand-cayman (auto-generated)"
                                class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none font-mono transition-colors"
                            >
                            @error('slug')
                                <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Code --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Region Code</label>
                            <input
                                type="text"
                                name="code"
                                value="{{ old('code') }}"
                                placeholder="e.g. GC, FL, JM"
                                maxlength="10"
                                class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none uppercase font-mono transition-colors"
                            >
                            @error('code')
                                <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Currency --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Currency Code</label>
                            <input
                                type="text"
                                name="currency"
                                value="{{ old('currency', 'USD') }}"
                                placeholder="USD, KYD, CAD"
                                maxlength="10"
                                class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none uppercase font-mono transition-colors"
                            >
                            @error('currency')
                                <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Description / Operational Coverage</label>
                        <textarea
                            name="description"
                            rows="3"
                            placeholder="Briefly describe territory coverage, key service hubs, or operational details..."
                            class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none transition-colors"
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="pt-2 w-full sm:w-1/2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Status *</label>
                        <select
                            name="status"
                            class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none transition-colors"
                        >
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active (Available for Pricing & Inquiries)</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive (Temporarily Disabled)</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 pt-5 border-t border-slate-200 flex items-center justify-end gap-3">
                    <x-button href="{{ route('dashboard.regions.index') }}" variant="secondary" size="sm">
                        Cancel
                    </x-button>
                    <x-button type="submit" variant="primary" size="sm">
                        Save Region
                    </x-button>
                </div>
            </x-card>
        </form>
    </div>

</x-dashboard.layout>
