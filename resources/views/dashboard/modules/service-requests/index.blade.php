<x-dashboard.layout :title="'Customer Service Requests — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">Customer Requests</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Customer Service Requests</h1>
            <p class="text-xs text-slate-500 mt-1">Review incoming service requests from customers and issue price quotations directly</p>
        </div>

        <div class="flex items-center gap-2.5">
            <x-button href="{{ route('dashboard.service-requests.export', request()->query()) }}" variant="secondary" size="sm">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export Requests CSV
            </x-button>
        </div>
    </x-slot:header>

    {{-- Quick Quotation & Priority Metrics --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-4">
        <a href="{{ route('dashboard.service-requests.index') }}" class="p-3 rounded-2xl bg-white border border-slate-200/90 shadow-2xs hover:border-[#C5A059] transition-all">
            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Requests</div>
            <div class="text-xl font-black text-slate-900 mt-1">{{ $stats['total'] }}</div>
        </a>
        <a href="{{ route('dashboard.service-requests.index', ['quote_status' => 'awaiting']) }}" class="p-3 rounded-2xl bg-white border border-amber-200/80 shadow-2xs hover:border-amber-400 transition-all">
            <div class="text-[10px] font-bold uppercase tracking-wider text-amber-600 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                Awaiting Quote
            </div>
            <div class="text-xl font-black text-amber-600 mt-1">{{ $stats['awaiting_quote'] }}</div>
        </a>
        <a href="{{ route('dashboard.service-requests.index', ['quote_status' => 'sent']) }}" class="p-3 rounded-2xl bg-white border border-emerald-200/80 shadow-2xs hover:border-emerald-400 transition-all">
            <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Quotes Dispatched</div>
            <div class="text-xl font-black text-emerald-600 mt-1">{{ $stats['quotes_sent'] }}</div>
        </a>
        <a href="{{ route('dashboard.service-requests.index', ['priority' => 'emergency']) }}" class="p-3 rounded-2xl bg-white border border-rose-200/80 shadow-2xs hover:border-rose-400 transition-all">
            <div class="text-[10px] font-bold uppercase tracking-wider text-rose-600 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping"></span>
                Emergency
            </div>
            <div class="text-xl font-black text-rose-600 mt-1">{{ $stats['emergency'] }}</div>
        </a>
        <a href="{{ route('dashboard.service-requests.index', ['priority' => 'high']) }}" class="p-3 rounded-2xl bg-white border border-amber-200/80 shadow-2xs hover:border-amber-400 transition-all">
            <div class="text-[10px] font-bold uppercase tracking-wider text-amber-600">High Priority</div>
            <div class="text-xl font-black text-amber-600 mt-1">{{ $stats['high'] }}</div>
        </a>
    </div>

    {{-- Filter Bar --}}
    <x-filter-bar
        :action="route('dashboard.service-requests.index')"
        :resetUrl="route('dashboard.service-requests.index')"
        nameLabel="Search Query"
        searchPlaceholder="Search customer, location, description..."
        :hasStatus="false"
        :hasDates="true"
    >
        <x-slot:extraFilters>
            {{-- Quote Status Filter --}}
            <div class="w-36 sm:w-40">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Quote Status</label>
                <select
                    name="quote_status"
                    class="w-full px-2.5 py-1.5 text-xs bg-slate-50/60 border border-slate-300 rounded-lg focus:bg-white focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] transition-colors text-slate-700 font-medium"
                >
                    <option value="all">All Requests</option>
                    <option value="awaiting" {{ request('quote_status') === 'awaiting' ? 'selected' : '' }}>⏳ Awaiting Quote</option>
                    <option value="sent" {{ request('quote_status') === 'sent' ? 'selected' : '' }}>📄 Quote Dispatched</option>
                </select>
            </div>

            {{-- Priority Filter --}}
            <div class="w-32 sm:w-36">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Priority</label>
                <select
                    name="priority"
                    class="w-full px-2.5 py-1.5 text-xs bg-slate-50/60 border border-slate-300 rounded-lg focus:bg-white focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] transition-colors text-slate-700 font-medium"
                >
                    <option value="all">All Priorities</option>
                    <option value="emergency" {{ request('priority') === 'emergency' ? 'selected' : '' }}>🚨 Emergency</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>🔥 High</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>⚡ Medium</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>🟢 Low</option>
                </select>
            </div>
        </x-slot:extraFilters>
    </x-filter-bar>

    {{-- Bulk Action Floating Toolbar --}}
    @can('service_requests.bulk-delete')
        <div class="bulk-action-bar hidden items-center justify-between p-3 mb-3 rounded-xl bg-white text-slate-900 shadow-xl transition-all animate-fade-in border-2 border-[#C5A059]">
            <div class="flex items-center gap-2.5 text-xs font-semibold">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gradient-to-r from-[#C5A059] to-[#D4AF37] text-slate-950 text-[11px] font-bold selected-count-badge">0</span>
                <span>request(s) selected on this page</span>
            </div>
            <form id="bulk-delete-form" method="POST" action="{{ route('dashboard.service-requests.bulk-delete') }}">
                @csrf
                @method('DELETE')
                <div id="bulk-hidden-inputs"></div>
                <button
                    type="button"
                    onclick="handleBulkDeleteSubmit()"
                    class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-lg bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition-colors cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Delete Selected
                </button>
            </form>
        </div>
    @endcan

    {{-- Requests Table Card --}}
    <x-card :padding="false">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3.5 w-10 text-center">
                            <input
                                type="checkbox"
                                id="select-all-checkbox"
                                class="rounded border-slate-300 text-[#C5A059] focus:ring-[#C5A059] cursor-pointer"
                            >
                        </th>
                        <th class="py-3 px-3.5">Customer & Request</th>
                        <th class="py-3 px-3.5">Property & Location</th>
                        <th class="py-3 px-3.5">Schedule</th>
                        <th class="py-3 px-3.5">Priority</th>
                        <th class="py-3 px-3.5">Media</th>
                        <th class="py-3 px-3.5">Quotation Status</th>
                        <th class="py-3 px-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse ($requests as $requestItem)
                        @php
                            $priorityVal = $requestItem->priority instanceof \BackedEnum ? $requestItem->priority->value : $requestItem->priority;
                            $reqFormatted = '#REQ-' . str_pad($requestItem->id, 5, '0', STR_PAD_LEFT);
                            $customerName = $requestItem->user?->name ?? 'Customer';
                        @endphp
                        <tr class="hover:bg-amber-50/20 transition-colors">
                            <td class="py-3 px-3.5 text-center">
                                <input
                                    type="checkbox"
                                    name="selected_ids[]"
                                    value="{{ $requestItem->id }}"
                                    class="row-checkbox rounded border-slate-300 text-[#C5A059] focus:ring-[#C5A059] cursor-pointer"
                                >
                            </td>

                            {{-- Customer Info --}}
                            <td class="py-3 px-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-900 text-[#C5A059] flex items-center justify-center font-bold text-xs shrink-0 border border-amber-500/20 shadow-2xs">
                                        {{ strtoupper(substr($customerName, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('dashboard.service-requests.show', $requestItem) }}" class="font-bold text-slate-900 hover:text-[#8F6B20] transition-colors">
                                            {{ $customerName }}
                                        </a>
                                        <div class="text-[11px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                            <span class="font-mono font-semibold text-slate-600">{{ $reqFormatted }}</span>
                                            <span>&bull;</span>
                                            <span>{{ $requestItem->user?->phone ?? $requestItem->user?->email ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Property & Location --}}
                            <td class="py-3 px-3.5 max-w-xs">
                                <div class="font-semibold text-slate-800 truncate" title="{{ $requestItem->property_information }}">
                                    {{ $requestItem->property_information }}
                                </div>
                                <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5 truncate" title="{{ $requestItem->address?->address }}">
                                    <svg class="w-3 h-3 text-[#C5A059] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span class="truncate">{{ $requestItem->address?->address ?? 'Location not specified' }}</span>
                                </div>
                            </td>

                            {{-- Schedule --}}
                            <td class="py-3 px-3.5">
                                <div class="font-bold text-slate-900">
                                    {{ $requestItem->preferred_service_date?->format('M d, Y') ?? 'Immediate' }}
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $requestItem->preferred_service_time ?? 'Flexible' }}
                                </div>
                            </td>

                            {{-- Priority --}}
                            <td class="py-3 px-3.5">
                                @if ($priorityVal === 'emergency')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-ping"></span>
                                        Emergency
                                    </span>
                                @elseif ($priorityVal === 'high')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                        High
                                    </span>
                                @elseif ($priorityVal === 'medium')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-300">
                                        Medium
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-300">
                                        Low
                                    </span>
                                @endif
                            </td>

                            {{-- Media Attachments --}}
                            <td class="py-3 px-3.5">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1 text-[11px] {{ $requestItem->photographs->count() > 0 ? 'text-amber-700 font-bold' : 'text-slate-400' }}">
                                        <svg class="w-3.5 h-3.5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ $requestItem->photographs->count() }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-[11px] {{ $requestItem->videos->count() > 0 ? 'text-purple-700 font-bold' : 'text-slate-400' }}">
                                        <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        {{ $requestItem->videos->count() }}
                                    </span>
                                </div>
                            </td>

                            {{-- Quotation Status & Summary --}}
                            <td class="py-3 px-3.5">
                                @if ($requestItem->latestQuote)
                                    <div>
                                        <div class="flex items-center gap-1.5">
                                            <a href="{{ route('dashboard.quotes.show', $requestItem->latestQuote) }}" class="font-mono font-bold text-xs text-slate-900 hover:text-[#8F6B20]">
                                                {{ $requestItem->latestQuote->quote_number }}
                                            </a>
                                            <span class="text-xs font-black text-[#8F6B20] font-mono">
                                                ${{ number_format($requestItem->latestQuote->total_price, 2) }}
                                            </span>
                                        </div>
                                        <div class="mt-0.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold border {{ $requestItem->latestQuote->status->badgeClasses() }}">
                                                {{ $requestItem->latestQuote->status->label() }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-300 shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Awaiting Quote
                                        </span>
                                        @can('quotes.create')
                                            <div>
                                                <button
                                                    type="button"
                                                    data-request-id="{{ $requestItem->id }}"
                                                    data-request-number="{{ $reqFormatted }}"
                                                    data-customer-name="{{ $customerName }}"
                                                    data-description="{{ $requestItem->description }}"
                                                    onclick="openQuoteModalFromButton(this)"
                                                    class="text-[11px] text-[#8F6B20] hover:text-[#C5A059] font-bold hover:underline cursor-pointer flex items-center gap-1"
                                                >
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                    <span>Send Quote</span>
                                                </button>
                                            </div>
                                        @endcan
                                    </div>
                                @endif
                            </td>

                            {{-- Actions Dropdown --}}
                            <td class="px-3.5 py-2 text-right">
                                <x-action-dropdown>
                                    @can('service_requests.view')
                                        <a href="{{ route('dashboard.service-requests.show', $requestItem) }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-slate-700 hover:bg-[#C5A059]/10 hover:text-[#B8903B] transition-colors">
                                            <svg class="w-3.5 h-3.5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            <span>View Details</span>
                                        </a>
                                    @endcan

                                    @can('quotes.create')
                                        <button
                                            type="button"
                                            data-request-id="{{ $requestItem->id }}"
                                            data-request-number="{{ $reqFormatted }}"
                                            data-customer-name="{{ $customerName }}"
                                            data-description="{{ $requestItem->description }}"
                                            onclick="openQuoteModalFromButton(this)"
                                            class="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-[#8F6B20] hover:bg-[#C5A059]/10 transition-colors cursor-pointer text-left font-bold"
                                        >
                                            <svg class="w-3.5 h-3.5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <span>{{ $requestItem->latestQuote ? 'Send Another Quote' : 'Create & Send Quote' }}</span>
                                        </button>
                                    @endcan

                                    @if ($requestItem->latestQuote)
                                        @can('quotes.view')
                                            <a href="{{ route('dashboard.quotes.show', $requestItem->latestQuote) }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-indigo-700 hover:bg-indigo-50 transition-colors font-medium">
                                                <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                                <span>View Quote ({{ $requestItem->latestQuote->quote_number }})</span>
                                            </a>
                                        @endcan
                                    @endif

                                    @can('service_requests.delete')
                                        <form method="POST" action="{{ route('dashboard.service-requests.destroy', $requestItem) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="button"
                                                data-confirm-delete="Are you sure you want to delete this service request record?"
                                                class="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer text-left font-medium"
                                            >
                                                <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                <span>Delete Request</span>
                                            </button>
                                        </form>
                                    @endcan
                                </x-action-dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    </div>
                                    <div class="text-sm font-semibold text-slate-700">No service requests found</div>
                                    <div class="text-xs text-slate-400 mt-0.5">Customer requests submitted via mobile or web will appear here.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($requests->hasPages())
            <div class="p-3.5 border-t border-slate-200">
                {{ $requests->links() }}
            </div>
        @endif
    </x-card>

    {{-- Include Create Quote Modal Popup --}}
    @include('dashboard.modules.quotes.partials.create-modal')

</x-dashboard.layout>
