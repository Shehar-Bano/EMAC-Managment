<x-dashboard.layout :title="'Service Regions — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">Regions</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Service Regions & Operational Markets</h1>
            <p class="text-xs text-slate-500 mt-1">Manage localized operational areas, currencies, and regional price adjustments</p>
        </div>

        <div class="flex items-center gap-2.5">
            <x-button href="{{ route('dashboard.regional-service-prices.index') }}" variant="secondary" size="sm">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Manage Regional Pricing
            </x-button>

            <x-button href="{{ route('dashboard.regions.export', request()->query()) }}" variant="secondary" size="sm">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export CSV
            </x-button>

            @can('regions.create')
                <x-button href="{{ route('dashboard.regions.create') }}" variant="primary" size="sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Region
                </x-button>
            @endcan
        </div>
    </x-slot:header>

    {{-- Filter Bar --}}
    <x-filter-bar
        :action="route('dashboard.regions.index')"
        :resetUrl="route('dashboard.regions.index')"
        nameLabel="Region Name / Code"
        searchPlaceholder="Search regions..."
        :hasStatus="true"
        :hasDates="true"
    />

    {{-- Bulk Action Floating Toolbar --}}
    @can('regions.bulk-delete')
        <div class="bulk-action-bar hidden items-center justify-between p-3 mb-3 rounded-xl bg-white text-slate-900 shadow-xl transition-all animate-fade-in border-2 border-[#C5A059]">
            <div class="flex items-center gap-2.5 text-xs font-semibold">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gradient-to-r from-[#C5A059] to-[#D4AF37] text-slate-950 text-[11px] font-bold selected-count-badge">0</span>
                <span>regions selected on this page</span>
            </div>
            <form id="bulk-delete-form" method="POST" action="{{ route('dashboard.regions.bulk-delete') }}">
                @csrf
                @method('DELETE')
                <div id="bulk-hidden-inputs"></div>
                <button
                    type="button"
                    onclick="handleBulkDeleteSubmit()"
                    class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-lg bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition-colors cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Archive Selected
                </button>
            </form>
        </div>
    @endcan

    {{-- Main Regions Table Card --}}
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <x-table
            :headers="[
                'Sr. No',
                'Region Name',
                'Code / Slug',
                'Currency',
                'Pricing Entries',
                'Status',
                ['label' => 'Actions', 'align' => 'right']
            ]"
            :hasSelectAll="auth()->user()->can('regions.bulk-delete')"
        >
            @forelse ($regions as $region)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    {{-- Checkbox --}}
                    @can('regions.bulk-delete')
                        <td class="w-10 px-3 py-2 text-center">
                            <input
                                type="checkbox"
                                name="selected_ids[]"
                                value="{{ $region->id }}"
                                class="table-row-checkbox rounded-sm border-slate-300 text-[#C5A059] focus:ring-[#C5A059] w-3.5 h-3.5 cursor-pointer"
                            >
                        </td>
                    @endcan

                    {{-- Sr. No --}}
                    <td class="w-14 px-3.5 py-2 font-mono text-[11px] text-slate-500 font-semibold">
                        {{ $regions->firstItem() ? ($regions->firstItem() + $loop->index) : $loop->iteration }}
                    </td>

                    {{-- Region Name & Description --}}
                    <td class="px-3.5 py-2">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 border border-[#C5A059]/30 flex items-center justify-center shrink-0 text-slate-700 font-bold text-xs">
                                📍
                            </div>
                            <div>
                                <a href="{{ route('dashboard.regions.show', $region) }}" class="font-bold text-slate-900 hover:text-[#C5A059] text-xs transition-colors">
                                    {{ $region->name }}
                                </a>
                                @if ($region->description)
                                    <p class="text-[11px] text-slate-500 line-clamp-1 max-w-xs">{{ $region->description }}</p>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Code & Slug --}}
                    <td class="px-3.5 py-2">
                        <div class="flex items-center gap-1.5">
                            @if ($region->code)
                                <span class="px-2 py-0.5 rounded bg-slate-100 font-mono text-[11px] font-bold text-slate-700 border border-slate-200">
                                    {{ $region->code }}
                                </span>
                            @endif
                            <span class="text-[11px] font-mono text-slate-500">{{ $region->slug }}</span>
                        </div>
                    </td>

                    {{-- Currency --}}
                    <td class="px-3.5 py-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-amber-50 text-[#8F6B20] border border-[#C5A059]/30">
                            {{ $region->currency }}
                        </span>
                    </td>

                    {{-- Pricing Entries Count --}}
                    <td class="px-3.5 py-2">
                        <a href="{{ route('dashboard.regional-service-prices.index', ['region_id' => $region->id]) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-700 hover:text-[#C5A059]">
                            <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[11px] font-mono font-bold">
                                {{ $region->service_prices_count }}
                            </span>
                            <span class="text-[11px] text-slate-500">services</span>
                        </a>
                    </td>

                    {{-- Status Toggle Button --}}
                    <td class="px-3.5 py-2">
                        @can('regions.status')
                            <form method="POST" action="{{ route('dashboard.regions.toggle-status', $region) }}" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <button
                                    type="submit"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-wide transition-all cursor-pointer {{ $region->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-300 hover:bg-emerald-100' : 'bg-slate-100 text-slate-600 border border-slate-300 hover:bg-slate-200' }}"
                                    title="Click to toggle status"
                                >
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $region->status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    {{ ucfirst($region->status) }}
                                </button>
                            </form>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $region->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-300' : 'bg-slate-100 text-slate-600 border border-slate-300' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $region->status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                {{ ucfirst($region->status) }}
                            </span>
                        @endcan
                    </td>

                    {{-- Actions --}}
                    <td class="px-3.5 py-2 text-right">
                        <x-action-dropdown>
                            @can('regions.view')
                                <a href="{{ route('dashboard.regions.show', $region) }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-slate-700 hover:bg-[#C5A059]/10 hover:text-[#B8903B] transition-colors">
                                    <svg class="w-3.5 h-3.5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <span>View Region</span>
                                </a>
                            @endcan

                            @can('regions.edit')
                                <a href="{{ route('dashboard.regions.edit', $region) }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-slate-700 hover:bg-[#C5A059]/10 hover:text-[#B8903B] transition-colors">
                                    <svg class="w-3.5 h-3.5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    <span>Edit Region</span>
                                </a>
                            @endcan

                            @can('regional_prices.create')
                                <a href="{{ route('dashboard.regional-service-prices.create', ['region_id' => $region->id]) }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-slate-700 hover:bg-[#C5A059]/10 hover:text-[#B8903B] transition-colors">
                                    <svg class="w-3.5 h-3.5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    <span>Add Service Price</span>
                                </a>
                            @endcan

                            @can('regions.delete')
                                <form method="POST" action="{{ route('dashboard.regions.destroy', $region) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="button"
                                        data-confirm-delete="Are you sure you want to archive region '{{ $region->name }}'?"
                                        class="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer text-left font-medium"
                                    >
                                        <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        <span>Archive Region</span>
                                    </button>
                                </form>
                            @endcan
                        </x-action-dropdown>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                        <div class="flex flex-col items-center justify-center">
                            <span class="text-3xl mb-2">📍</span>
                            <p class="text-sm font-semibold text-slate-700">No regions found</p>
                            <p class="text-xs text-slate-500 mt-1">Try adjusting your search criteria or create a new region.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-table>

        {{-- Pagination footer --}}
        <x-pagination :paginator="$regions" />
    </div>

    {{-- Bulk Delete Script Helper --}}
    @can('regions.bulk-delete')
        <script>
            function handleBulkDeleteSubmit() {
                const checkedBoxes = document.querySelectorAll('.table-row-checkbox:checked');
                if (checkedBoxes.length === 0) return;

                const count = checkedBoxes.length;
                window.ERP.confirmBulkDelete(() => {
                    const hiddenContainer = document.getElementById('bulk-hidden-inputs');
                    hiddenContainer.innerHTML = '';
                    checkedBoxes.forEach(cb => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'selected_ids[]';
                        input.value = cb.value;
                        hiddenContainer.appendChild(input);
                    });
                    document.getElementById('bulk-delete-form').submit();
                }, { count: count });
            }
        </script>
    @endcan
</x-dashboard.layout>
