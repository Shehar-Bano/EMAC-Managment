<x-dashboard.layout :title="'Region: ' . $region->name . ' — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <a href="{{ route('dashboard.regions.index') }}" class="hover:text-[#C5A059]">Regions</a>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">{{ $region->name }}</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $region->name }}</h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $region->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-300' : 'bg-slate-100 text-slate-600 border border-slate-300' }}">
                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $region->status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                    {{ ucfirst($region->status) }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Regional operational center, localized rates, and service offerings</p>
        </div>

        <div class="flex items-center gap-2">
            @can('regional_prices.create')
                <x-button href="{{ route('dashboard.regional-service-prices.create', ['region_id' => $region->id]) }}" variant="primary" size="sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Service Price
                </x-button>
            @endcan

            @can('regions.edit')
                <x-button href="{{ route('dashboard.regions.edit', $region) }}" variant="secondary" size="sm">
                    Edit Region
                </x-button>
            @endcan

            <x-button href="{{ route('dashboard.regions.index') }}" variant="secondary" size="sm">
                &larr; Back to Regions
            </x-button>
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Region Summary Column --}}
        <div class="space-y-6">
            <x-card title="Region Metadata">
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span class="text-slate-500">Region Name</span>
                        <span class="font-bold text-slate-900">{{ $region->name }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span class="text-slate-500">Region Code</span>
                        <span class="font-mono font-bold text-slate-800">{{ $region->code ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span class="text-slate-500">Slug Identifier</span>
                        <span class="font-mono text-slate-600">{{ $region->slug }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span class="text-slate-500">Currency</span>
                        <span class="font-mono font-bold text-[#8F6B20]">{{ $region->currency }}</span>
                    </div>
                    <div class="flex justify-between py-1.5">
                        <span class="text-slate-500">Created At</span>
                        <span class="text-slate-700">{{ $region->created_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>

                @if ($region->description)
                    <div class="mt-4 pt-3 border-t border-slate-200">
                        <h4 class="text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Operational Coverage</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $region->description }}</p>
                    </div>
                @endif
            </x-card>
        </div>

        {{-- Associated Regional Pricing List --}}
        <div class="lg:col-span-2 space-y-6">
            <x-card title="Configured Service Prices in {{ $region->name }}">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/50 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                                <th class="py-2.5 px-3">Category</th>
                                <th class="py-2.5 px-3">Subcategory Service</th>
                                <th class="py-2.5 px-3">Price Rate</th>
                                <th class="py-2.5 px-3">Status</th>
                                <th class="py-2.5 px-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($region->servicePrices as $item)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-2.5 px-3 font-semibold text-slate-800">
                                        {{ $item->category?->name ?? '—' }}
                                    </td>
                                    <td class="py-2.5 px-3 text-slate-700">
                                        {{ $item->subcategory?->name ?? '—' }}
                                    </td>
                                    <td class="py-2.5 px-3 font-mono font-bold text-[#8F6B20]">
                                        {{ $item->currency }} {{ number_format((float) $item->price, 2) }}
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $item->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-300' : 'bg-slate-100 text-slate-600 border border-slate-300' }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 text-right">
                                        @can('regional_prices.edit')
                                            <a href="{{ route('dashboard.regional-service-prices.edit', $item) }}" class="text-[#C5A059] hover:underline font-semibold text-[11px]">
                                                Edit
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-500">
                                        <p class="text-xs">No specific service prices configured for this region yet.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>

</x-dashboard.layout>
