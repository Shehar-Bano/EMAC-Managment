<x-dashboard.layout :title="'Service Price: ' . ($regionalServicePrice->subcategory?->name ?? 'Price') . ' — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <a href="{{ route('dashboard.regional-service-prices.index') }}" class="hover:text-[#C5A059]">Regional Pricing</a>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">{{ $regionalServicePrice->subcategory?->name ?? 'Price Details' }} ({{ $regionalServicePrice->region?->name }})</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                    {{ $regionalServicePrice->subcategory?->name ?? 'Service Price' }}
                </h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $regionalServicePrice->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-300' : 'bg-slate-100 text-slate-600 border border-slate-300' }}">
                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $regionalServicePrice->status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                    {{ ucfirst($regionalServicePrice->status) }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Localized service base rate for {{ $regionalServicePrice->region?->name }}</p>
        </div>

        <div class="flex items-center gap-2">
            @can('regional_prices.edit')
                <x-button href="{{ route('dashboard.regional-service-prices.edit', $regionalServicePrice) }}" variant="primary" size="sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit Price
                </x-button>
            @endcan

            <x-button href="{{ route('dashboard.regional-service-prices.index') }}" variant="secondary" size="sm">
                &larr; Back to Regional Pricing
            </x-button>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Price Overview Card --}}
        <div class="lg:col-span-2 space-y-6">
            <x-card title="Service Pricing Overview">
                <div class="p-4 rounded-xl bg-amber-50/70 border border-[#C5A059]/40 mb-5 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-600 font-semibold block">Configured Regional Base Rate</span>
                        <div class="text-2xl sm:text-3xl font-black text-slate-900 font-mono mt-0.5">
                            {{ $regionalServicePrice->currency }} {{ number_format((float) $regionalServicePrice->price, 2) }}
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider font-bold">Region Code</span>
                        <div class="text-xs font-mono font-bold text-[#8F6B20]">
                            {{ $regionalServicePrice->region?->code ?? '—' }} ({{ $regionalServicePrice->region?->name }})
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                        <span class="text-slate-500 font-medium block">Parent Category</span>
                        <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                            {{ $regionalServicePrice->category?->icon }} {{ $regionalServicePrice->category?->name ?? '—' }}
                        </span>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                        <span class="text-slate-500 font-medium block">Subcategory Service</span>
                        <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                            {{ $regionalServicePrice->subcategory?->icon }} {{ $regionalServicePrice->subcategory?->name ?? '—' }}
                        </span>
                    </div>
                </div>

                @if ($regionalServicePrice->notes)
                    <div class="mt-5 pt-4 border-t border-slate-200">
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pricing Notes & Guidance</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $regionalServicePrice->notes }}</p>
                    </div>
                @endif
            </x-card>
        </div>

        {{-- Metadata Column --}}
        <div class="space-y-6">
            <x-card title="Metadata">
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span class="text-slate-500">Record ID</span>
                        <span class="font-mono text-slate-800 font-bold">#{{ $regionalServicePrice->id }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span class="text-slate-500">Created At</span>
                        <span class="text-slate-700">{{ $regionalServicePrice->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between py-1.5">
                        <span class="text-slate-500">Last Updated</span>
                        <span class="text-slate-700">{{ $regionalServicePrice->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

</x-dashboard.layout>
