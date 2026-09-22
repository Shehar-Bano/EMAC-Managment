@props([
    'action' => url()->current(),
    'resetUrl' => url()->current(),
    'nameLabel' => 'Name',
    'statusOptions' => [
        'all' => 'All Statuses',
        'active' => 'Active',
        'inactive' => 'Inactive',
    ],
    'hasStatus' => true,
    'hasDates' => true,
    'hasName' => true,
])

<div class="bg-white rounded-xl border border-slate-200/80 p-3 mb-4 shadow-2xs">
    <form method="GET" action="{{ $action }}" id="filter-form" class="flex flex-wrap items-end gap-2.5">
        {{-- Preserve per_page value across filter submissions --}}
        @if (request()->has('per_page'))
            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
        @endif

        {{-- Name Filter (e.g. Category Name, User Name, Subcategory Name) --}}
        @if ($hasName)
            <div class="grow min-w-[160px] max-w-xs">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">{{ $nameLabel }}</label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Filter by {{ strtolower($nameLabel) }}..."
                    class="w-full px-2.5 py-1.5 text-xs bg-slate-50/60 border border-slate-300 rounded-lg focus:bg-white focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] transition-colors text-slate-700 placeholder:text-slate-400"
                >
            </div>
        @endif

        {{-- Optional Status Filter --}}
        @if ($hasStatus)
            <div class="w-32 sm:w-36">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status</label>
                <select
                    name="status"
                    class="w-full px-2.5 py-1.5 text-xs bg-slate-50/60 border border-slate-300 rounded-lg focus:bg-white focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] transition-colors text-slate-700"
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

        {{-- Optional Start / End Date Filters --}}
        @if ($hasDates)
            <div class="w-32 sm:w-36">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Start Date</label>
                <input
                    type="date"
                    name="from_date"
                    value="{{ request('from_date') }}"
                    class="w-full px-2 py-1.5 text-xs bg-slate-50/60 border border-slate-300 rounded-lg focus:bg-white focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] transition-colors text-slate-700"
                >
            </div>

            <div class="w-32 sm:w-36">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">End Date</label>
                <input
                    type="date"
                    name="to_date"
                    value="{{ request('to_date') }}"
                    class="w-full px-2 py-1.5 text-xs bg-slate-50/60 border border-slate-300 rounded-lg focus:bg-white focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] transition-colors text-slate-700"
                >
            </div>
        @endif

        {{-- Filter & Reset Action Buttons (Always Inside Card) --}}
        <div class="flex items-center gap-1.5 shrink-0 ml-auto">
            <button
                type="submit"
                class="inline-flex items-center justify-center gap-1 px-3 py-1.5 text-xs font-bold text-slate-950 bg-gradient-to-r from-[#C5A059] to-[#D4AF37] hover:from-[#B8903B] hover:to-[#C5A059] rounded-lg shadow-2xs transition-all cursor-pointer"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                Filter
            </button>
            <a
                href="{{ $resetUrl }}"
                class="inline-flex items-center justify-center p-1.5 text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-lg transition-colors cursor-pointer shrink-0"
                title="Reset Filters"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </a>
        </div>
    </form>
</div>
