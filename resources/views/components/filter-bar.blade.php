@props([
    'action' => url()->current(),
    'resetUrl' => url()->current(),
    'searchPlaceholder' => 'Search by name, email, or keyword...',
    'statusOptions' => [
        'all' => 'All Statuses',
        'active' => 'Active',
        'inactive' => 'Inactive',
    ],
    'hasStatus' => true,
    'hasDates' => true,
])

<div class="bg-white rounded-xl border border-slate-200/80 p-4 mb-5 shadow-2xs">
    <form method="GET" action="{{ $action }}" class="space-y-3" id="filter-form">
        {{-- Preserve per_page value across filter submissions --}}
        @if (request()->has('per_page'))
            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
            {{-- Search Bar --}}
            <div class="{{ $hasStatus && $hasDates ? 'lg:col-span-4' : 'lg:col-span-5' }}">
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Search</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="{{ $searchPlaceholder }}"
                        class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50/50 border border-slate-300 rounded-lg focus:bg-white focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] transition-colors placeholder:text-slate-400"
                    >
                </div>
            </div>

            {{-- Optional Status Filter --}}
            @if ($hasStatus)
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Status</label>
                    <select
                        name="status"
                        class="w-full px-3 py-2 text-sm bg-slate-50/50 border border-slate-300 rounded-lg focus:bg-white focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] transition-colors"
                    >
                        @foreach ($statusOptions as $key => $label)
                            <option value="{{ $key }}" {{ request('status', 'all') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Optional Extra Filters Slot --}}
            @if (isset($extraFilters))
                {{ $extraFilters }}
            @endif

            {{-- Optional Date Range Filters --}}
            @if ($hasDates)
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">From Date</label>
                    <input
                        type="date"
                        name="from_date"
                        value="{{ request('from_date') }}"
                        class="w-full px-3 py-2 text-sm bg-slate-50/50 border border-slate-300 rounded-lg focus:bg-white focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] transition-colors text-slate-600"
                    >
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">To Date</label>
                    <input
                        type="date"
                        name="to_date"
                        value="{{ request('to_date') }}"
                        class="w-full px-3 py-2 text-sm bg-slate-50/50 border border-slate-300 rounded-lg focus:bg-white focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] transition-colors text-slate-600"
                    >
                </div>
            @endif

            {{-- Filter & Reset Actions --}}
            <div class="lg:col-span-2 flex items-center gap-2">
                <button
                    type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-sm font-bold text-slate-950 bg-gradient-to-r from-[#C5A059] to-[#D4AF37] hover:from-[#B8903B] hover:to-[#C5A059] rounded-lg shadow-2xs transition-all cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Filter
                </button>
                <a
                    href="{{ $resetUrl }}"
                    class="inline-flex items-center justify-center p-2 text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-lg transition-colors cursor-pointer"
                    title="Reset Filters"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </a>
            </div>
        </div>
    </form>
</div>
