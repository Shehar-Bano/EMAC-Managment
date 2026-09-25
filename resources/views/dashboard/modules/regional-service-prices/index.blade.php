<x-dashboard.layout :title="'Regional Service Pricing — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">Regional Pricing</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Regional Service Pricing</h1>
            <p class="text-xs text-slate-500 mt-1">Configure localized base pricing per service category, subcategory, and region</p>
        </div>

        <div class="flex items-center gap-2.5">
            <x-button href="{{ route('dashboard.regions.index') }}" variant="secondary" size="sm">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Service Regions
            </x-button>

            <x-button href="{{ route('dashboard.regional-service-prices.export', request()->query()) }}" variant="secondary" size="sm">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export CSV
            </x-button>

            @can('regional_prices.create')
                <x-button href="{{ route('dashboard.regional-service-prices.create') }}" variant="primary" size="sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Service Price
                </x-button>
            @endcan
        </div>
    </x-slot:header>

    {{-- Filter Bar --}}
    <x-filter-bar
        :action="route('dashboard.regional-service-prices.index')"
        :resetUrl="route('dashboard.regional-service-prices.index')"
        nameLabel="Search Notes or Pricing"
        searchPlaceholder="Search notes / price..."
        :hasStatus="true"
        :hasDates="true"
    >
        <x-slot:extraFilters>
            {{-- Region Filter --}}
            <div class="w-32 sm:w-36">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Region</label>
                <select
                    name="region_id"
                    class="w-full px-2.5 py-1.5 text-xs bg-slate-50/60 border border-slate-300 rounded-lg focus:bg-white focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] transition-colors text-slate-700"
                >
                    <option value="all">All Regions</option>
                    @foreach ($regions as $r)
                        <option value="{{ $r->id }}" {{ request('region_id') == $r->id ? 'selected' : '' }}>
                            {{ $r->name }} ({{ $r->currency }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Category Filter --}}
            <div class="w-32 sm:w-36">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Category</label>
                <select
                    name="category_id"
                    class="w-full px-2.5 py-1.5 text-xs bg-slate-50/60 border border-slate-300 rounded-lg focus:bg-white focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] transition-colors text-slate-700"
                >
                    <option value="all">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </x-slot:extraFilters>
    </x-filter-bar>

    {{-- Bulk Action Floating Toolbar --}}
    @can('regional_prices.bulk-delete')
        <div class="bulk-action-bar hidden items-center justify-between p-3 mb-3 rounded-xl bg-white text-slate-900 shadow-xl transition-all animate-fade-in border-2 border-[#C5A059]">
            <div class="flex items-center gap-2.5 text-xs font-semibold">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gradient-to-r from-[#C5A059] to-[#D4AF37] text-slate-950 text-[11px] font-bold selected-count-badge">0</span>
                <span>pricing records selected on this page</span>
            </div>
            <form id="bulk-delete-form" method="POST" action="{{ route('dashboard.regional-service-prices.bulk-delete') }}">
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

    {{-- Main Table Card --}}
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <x-table
            :headers="[
                'Sr. No',
                'Region',
                'Category',
                'Subcategory Service',
                'Base Price',
                'Status',
                ['label' => 'Actions', 'align' => 'right']
            ]"
            :hasSelectAll="auth()->user()->can('regional_prices.bulk-delete')"
        >
            @forelse ($prices as $item)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    {{-- Checkbox --}}
                    @can('regional_prices.bulk-delete')
                        <td class="w-10 px-3 py-2 text-center">
                            <input
                                type="checkbox"
                                name="selected_ids[]"
                                value="{{ $item->id }}"
                                class="table-row-checkbox rounded-sm border-slate-300 text-[#C5A059] focus:ring-[#C5A059] w-3.5 h-3.5 cursor-pointer"
                            >
                        </td>
                    @endcan

                    {{-- Sr. No --}}
                    <td class="w-14 px-3.5 py-2 font-mono text-[11px] text-slate-500 font-semibold">
                        {{ $prices->firstItem() ? ($prices->firstItem() + $loop->index) : $loop->iteration }}
                    </td>

                    {{-- Region --}}
                    <td class="px-3.5 py-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#C5A059]"></span>
                            <span class="font-bold text-xs text-slate-900">{{ $item->region?->name ?? '—' }}</span>
                            @if ($item->region?->code)
                                <span class="px-1.5 py-0.5 rounded bg-slate-100 font-mono text-[10px] text-slate-600 font-bold border border-slate-200">
                                    {{ $item->region->code }}
                                </span>
                            @endif
                        </div>
                    </td>

                    {{-- Category --}}
                    <td class="px-3.5 py-2">
                        <div class="flex items-center gap-1.5 text-xs text-slate-800 font-semibold">
                            <span>{{ $item->category?->icon ?? '📁' }}</span>
                            <span>{{ $item->category?->name ?? '—' }}</span>
                        </div>
                    </td>

                    {{-- Subcategory Service --}}
                    <td class="px-3.5 py-2">
                        <div class="flex items-center gap-1.5 text-xs text-slate-700">
                            <span>{{ $item->subcategory?->icon ?? '🛠️' }}</span>
                            <span class="font-medium">{{ $item->subcategory?->name ?? '—' }}</span>
                        </div>
                        @if ($item->notes)
                            <p class="text-[11px] text-slate-400 mt-0.5 line-clamp-1 max-w-xs">{{ $item->notes }}</p>
                        @endif
                    </td>

                    {{-- Base Price --}}
                    <td class="px-3.5 py-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-amber-50 text-[#8F6B20] border border-[#C5A059]/40">
                            {{ $item->currency }} {{ number_format((float) $item->price, 2) }}
                        </span>
                    </td>

                    {{-- Status Toggle --}}
                    <td class="px-3.5 py-2">
                        @can('regional_prices.status')
                            <form method="POST" action="{{ route('dashboard.regional-service-prices.toggle-status', $item) }}" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <button
                                    type="submit"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-wide transition-all cursor-pointer {{ $item->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-300 hover:bg-emerald-100' : 'bg-slate-100 text-slate-600 border border-slate-300 hover:bg-slate-200' }}"
                                    title="Click to toggle status"
                                >
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $item->status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    {{ ucfirst($item->status) }}
                                </button>
                            </form>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $item->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-300' : 'bg-slate-100 text-slate-600 border border-slate-300' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $item->status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                {{ ucfirst($item->status) }}
                            </span>
                        @endcan
                    </td>

                    {{-- Actions --}}
                    <td class="px-3.5 py-2 text-right">
                        <x-action-dropdown>
                            @can('regional_prices.view')
                                <a href="{{ route('dashboard.regional-service-prices.show', $item) }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-slate-700 hover:bg-[#C5A059]/10 hover:text-[#B8903B] transition-colors">
                                    <svg class="w-3.5 h-3.5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <span>View Price</span>
                                </a>
                            @endcan

                            @can('regional_prices.edit')
                                <a href="{{ route('dashboard.regional-service-prices.edit', $item) }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-slate-700 hover:bg-[#C5A059]/10 hover:text-[#B8903B] transition-colors">
                                    <svg class="w-3.5 h-3.5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    <span>Edit Price</span>
                                </a>
                            @endcan

                            @can('regional_prices.delete')
                                <form method="POST" action="{{ route('dashboard.regional-service-prices.destroy', $item) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="button"
                                        data-confirm-delete="Are you sure you want to archive price configuration for {{ $item->subcategory?->name }} in {{ $item->region?->name }}?"
                                        class="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer text-left font-medium"
                                    >
                                        <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        <span>Archive Price</span>
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
                            <span class="text-3xl mb-2">💵</span>
                            <p class="text-sm font-semibold text-slate-700">No regional pricing configured</p>
                            <p class="text-xs text-slate-500 mt-1">Select a region and service category to add localized pricing.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-table>

        {{-- Pagination footer --}}
        <x-pagination :paginator="$prices" />
    </div>

    {{-- Bulk Delete Script Helper --}}
    @can('regional_prices.bulk-delete')
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
