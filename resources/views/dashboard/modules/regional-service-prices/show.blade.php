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
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 flex items-center gap-3">
                        <div class="shrink-0 w-9 h-9 rounded-lg bg-white border border-slate-200/80 overflow-hidden flex items-center justify-center">
                            @if ($regionalServicePrice->category?->icon_url && (str_contains($regionalServicePrice->category->icon_url, '/') || str_contains($regionalServicePrice->category->icon_url, '.')))
                                <img src="{{ $regionalServicePrice->category->icon_url }}" alt="{{ $regionalServicePrice->category->name }}" class="w-full h-full object-cover">
                            @elseif ($regionalServicePrice->category?->image_url)
                                <img src="{{ $regionalServicePrice->category->image_url }}" alt="{{ $regionalServicePrice->category->name }}" class="w-full h-full object-cover">
                            @elseif ($regionalServicePrice->category?->icon)
                                <span class="text-sm">{{ $regionalServicePrice->category->icon }}</span>
                            @else
                                <svg class="w-4 h-4 text-[#8F6B20]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            @endif
                        </div>
                        <div>
                            <span class="text-slate-500 font-medium block">Parent Category</span>
                            <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                {{ $regionalServicePrice->category?->name ?? '—' }}
                            </span>
                        </div>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 flex items-center gap-3">
                        <div class="shrink-0 w-9 h-9 rounded-lg bg-white border border-slate-200/80 overflow-hidden flex items-center justify-center">
                            @if ($regionalServicePrice->subcategory?->icon_url && (str_contains($regionalServicePrice->subcategory->icon_url, '/') || str_contains($regionalServicePrice->subcategory->icon_url, '.')))
                                <img src="{{ $regionalServicePrice->subcategory->icon_url }}" alt="{{ $regionalServicePrice->subcategory->name }}" class="w-full h-full object-cover">
                            @elseif ($regionalServicePrice->subcategory?->image_url)
                                <img src="{{ $regionalServicePrice->subcategory->image_url }}" alt="{{ $regionalServicePrice->subcategory->name }}" class="w-full h-full object-cover">
                            @elseif ($regionalServicePrice->subcategory?->icon)
                                <span class="text-sm">{{ $regionalServicePrice->subcategory->icon }}</span>
                            @else
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            @endif
                        </div>
                        <div>
                            <span class="text-slate-500 font-medium block">Subcategory Service</span>
                            <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                {{ $regionalServicePrice->subcategory?->name ?? '—' }}
                            </span>
                        </div>
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
