<x-dashboard.layout :title="'Quotes & Estimates — EMAC Development'">
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Price Quotations & Estimates</h1>
            <p class="text-xs text-slate-500 mt-1">Manage issued cost proposals, approvals, customer questions, and payment terms</p>
        </div>
        <div>
            <a
                href="{{ route('dashboard.service-requests.index') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#C5A059] hover:bg-[#B8903B] text-slate-950 text-xs font-bold shadow-xs hover:shadow-md transition-all"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Issue Quote from Customer Requests</span>
            </a>
        </div>
    </div>

    {{-- Filter Status Tabs & Search --}}
    <div class="mb-6 space-y-4">
        {{-- Status Filter Tabs --}}
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3">
            <a
                href="{{ route('dashboard.quotes.index', array_merge($filters, ['status' => 'all'])) }}"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ ($filters['status'] ?? 'all') === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
            >
                All Quotes ({{ $statusCounts['all'] }})
            </a>
            <a
                href="{{ route('dashboard.quotes.index', array_merge($filters, ['status' => 'pending'])) }}"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ ($filters['status'] ?? '') === 'pending' ? 'bg-amber-500 text-white shadow-xs' : 'bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100' }}"
            >
                Pending Review ({{ $statusCounts['pending'] }})
            </a>
            <a
                href="{{ route('dashboard.quotes.index', array_merge($filters, ['status' => 'approved'])) }}"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ ($filters['status'] ?? '') === 'approved' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100' }}"
            >
                Approved ({{ $statusCounts['approved'] }})
            </a>
            <a
                href="{{ route('dashboard.quotes.index', array_merge($filters, ['status' => 'declined'])) }}"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ ($filters['status'] ?? '') === 'declined' ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-50 text-rose-800 border border-rose-200 hover:bg-rose-100' }}"
            >
                Declined ({{ $statusCounts['declined'] }})
            </a>
            <a
                href="{{ route('dashboard.quotes.index', array_merge($filters, ['status' => 'ask_for_question'])) }}"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all {{ ($filters['status'] ?? '') === 'ask_for_question' ? 'bg-purple-600 text-white shadow-xs' : 'bg-purple-50 text-purple-800 border border-purple-200 hover:bg-purple-100' }}"
            >
                Customer Questions ({{ $statusCounts['ask_for_question'] }})
            </a>
        </div>

        {{-- Search Input --}}
        <form method="GET" action="{{ route('dashboard.quotes.index') }}" class="flex items-center gap-2">
            @if(isset($filters['status']) && $filters['status'] !== 'all')
                <input type="hidden" name="status" value="{{ $filters['status'] }}">
            @endif
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input
                    type="text"
                    name="search"
                    value="{{ $filters['search'] ?? '' }}"
                    placeholder="Search by quote #, customer name, email, or scope..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 placeholder-slate-400 focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] bg-white transition-all"
                >
            </div>
            <button
                type="submit"
                class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer"
            >
                Search
            </button>
            @if(!empty($filters['search']))
                <a
                    href="{{ route('dashboard.quotes.index', ['status' => $filters['status'] ?? 'all']) }}"
                    class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-colors"
                >
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Quotes Table Card --}}
    <x-card :padding="false">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3.5">Quote #</th>
                        <th class="py-3 px-3.5">Customer & Request</th>
                        <th class="py-3 px-3.5">Scope of Work</th>
                        <th class="py-3 px-3.5 text-right">Total Price</th>
                        <th class="py-3 px-3.5">Expiration</th>
                        <th class="py-3 px-3.5">Status</th>
                        <th class="py-3 px-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse ($quotes as $quote)
                        <tr class="hover:bg-amber-50/20 transition-colors">
                            {{-- Quote # --}}
                            <td class="py-3 px-3.5 font-mono font-bold text-slate-900">
                                <a href="{{ route('dashboard.quotes.show', $quote) }}" class="hover:text-[#8F6B20] transition-colors">
                                    {{ $quote->quote_number }}
                                </a>
                                <div class="text-[10px] text-slate-400 font-sans font-normal">{{ $quote->created_at->format('M d, Y') }}</div>
                            </td>

                            {{-- Customer & Request --}}
                            <td class="py-3 px-3.5">
                                <div class="font-bold text-slate-900">{{ $quote->user?->name ?? 'Customer' }}</div>
                                <div class="text-[11px] text-slate-500 flex items-center gap-1.5 mt-0.5">
                                    <a href="{{ route('dashboard.service-requests.show', $quote->service_request_id) }}" class="font-mono text-[#8F6B20] hover:underline">
                                        #REQ-{{ str_pad($quote->service_request_id, 5, '0', STR_PAD_LEFT) }}
                                    </a>
                                    <span>&bull;</span>
                                    <span>{{ $quote->user?->email }}</span>
                                </div>
                            </td>

                            {{-- Scope of Work --}}
                            <td class="py-3 px-3.5 max-w-xs truncate text-slate-700">
                                {{ $quote->service_description }}
                            </td>

                            {{-- Total Price --}}
                            <td class="py-3 px-3.5 text-right font-mono font-bold text-slate-900">
                                <span class="text-[#8F6B20] text-sm">${{ number_format($quote->total_price, 2) }}</span>
                                @if($quote->discount > 0)
                                    <div class="text-[10px] text-emerald-600 font-sans">-${{ number_format($quote->discount, 2) }} disc.</div>
                                @endif
                            </td>

                            {{-- Expiration --}}
                            <td class="py-3 px-3.5">
                                <span class="{{ $quote->expires_at && $quote->expires_at->isPast() ? 'text-rose-600 font-bold' : 'text-slate-600' }}">
                                    {{ $quote->expires_at ? $quote->expires_at->format('M d, Y') : 'N/A' }}
                                </span>
                            </td>

                            {{-- Status Badge --}}
                            <td class="py-3 px-3.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $quote->status->badgeClasses() }}">
                                    {{ $quote->status->label() }}
                                </span>
                            </td>

                            {{-- Actions Dropdown --}}
                            <td class="px-3.5 py-2 text-right">
                                <x-action-dropdown>
                                    @can('quotes.view')
                                        <a href="{{ route('dashboard.quotes.show', $quote) }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-slate-700 hover:bg-[#C5A059]/10 hover:text-[#B8903B] transition-colors">
                                            <svg class="w-3.5 h-3.5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            <span>View Quote</span>
                                        </a>

                                        <a href="{{ route('dashboard.quotes.print', $quote) }}" target="_blank" class="flex items-center gap-2 px-3 py-1.5 text-xs text-slate-700 hover:bg-[#C5A059]/10 hover:text-[#B8903B] transition-colors">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            <span>Print / PDF</span>
                                        </a>
                                    @endcan

                                    @can('quotes.delete')
                                        <form method="POST" action="{{ route('dashboard.quotes.destroy', $quote) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="button"
                                                data-confirm-delete="Are you sure you want to delete Quote '{{ $quote->quote_number }}'?"
                                                class="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer text-left font-medium"
                                            >
                                                <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                <span>Delete Quote</span>
                                            </button>
                                        </form>
                                    @endcan
                                </x-action-dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div class="text-sm font-semibold text-slate-700">No quotes found</div>
                                    <div class="text-xs text-slate-400 mt-0.5">Generate price quotes for customer service requests to see them here.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($quotes->hasPages())
            <div class="p-3.5 border-t border-slate-200">
                {{ $quotes->links() }}
            </div>
        @endif
    </x-card>
</x-dashboard.layout>
