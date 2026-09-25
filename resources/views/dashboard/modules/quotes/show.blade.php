<x-dashboard.layout :title="'Quote ' . $quote->quote_number . ' — EMAC Development'">
    {{-- Top Action Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('dashboard.service-requests.index') }}" class="hover:text-[#8F6B20] transition-colors">Customer Requests</a>
                <span>/</span>
                <a href="{{ route('dashboard.service-requests.show', $quote->service_request_id) }}" class="hover:text-[#8F6B20] transition-colors">#REQ-{{ str_pad($quote->service_request_id, 5, '0', STR_PAD_LEFT) }}</a>
                <span>/</span>
                <span class="text-slate-800 font-bold font-mono">{{ $quote->quote_number }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <h1 class="text-xl font-bold tracking-tight text-slate-900 flex items-center gap-2">
                    <span>Quote</span>
                    <span class="font-mono text-sm font-bold text-slate-800 bg-slate-100 px-2.5 py-0.5 rounded-lg border border-slate-200">{{ $quote->quote_number }}</span>
                </h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $quote->status->badgeClasses() }}">
                    {{ $quote->status->label() }}
                </span>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2">
            <a
                href="{{ route('dashboard.quotes.print', $quote) }}"
                target="_blank"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-xs transition-all"
            >
                <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Print / Export</span>
            </a>

            <a
                href="{{ route('dashboard.service-requests.show', $quote->service_request_id) }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition-all"
            >
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Back to Request</span>
            </a>

            @can('quotes.delete')
                <form method="POST" action="{{ route('dashboard.quotes.destroy', $quote) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button
                        type="button"
                        data-confirm-delete="Are you sure you want to delete Quote {{ $quote->quote_number }}?"
                        class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl border border-rose-200 transition-colors cursor-pointer"
                        title="Delete Quote"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </form>
            @endcan
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Left 2 Columns: Quote Invoice Details --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Quote Header & Financial Summary Card --}}
            <x-card>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 gap-4">
                    <div>
                        <div class="text-[10px] uppercase font-extrabold tracking-widest text-[#8F6B20]">Official Price Quotation</div>
                        <div class="text-sm font-bold text-slate-900 font-mono mt-0.5">{{ $quote->quote_number }}</div>
                        <div class="text-xs text-slate-400 mt-0.5">Issued On: <span class="font-semibold text-slate-600">{{ $quote->created_at->format('M d, Y · h:i A') }}</span></div>
                    </div>
                    <div class="sm:text-right">
                        <div class="text-xs text-slate-400">Valid Until</div>
                        <div class="text-sm font-bold {{ $quote->expires_at && $quote->expires_at->isPast() ? 'text-rose-600' : 'text-slate-800' }}">
                            {{ $quote->expires_at ? $quote->expires_at->format('F d, Y') : 'No Expiry Set' }}
                            @if ($quote->expires_at && $quote->expires_at->isPast())
                                <span class="text-[10px] uppercase font-extrabold text-rose-500 block">(Expired)</span>
                            @endif
                        </div>
                        <div class="text-xs text-slate-400 mt-1">Sent By: <span class="font-semibold text-slate-700">{{ $quote->sender?->name ?? 'System Administrator' }}</span></div>
                    </div>
                </div>

                {{-- Scope of Work --}}
                <div class="py-6 border-b border-slate-100">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Scope of Work & Service Description</h3>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-xs text-slate-800 leading-relaxed whitespace-pre-line">
                        {{ $quote->service_description }}
                    </div>
                </div>

                {{-- Itemized Table --}}
                <div class="py-6">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Itemized Pricing Breakdown</h3>
                    
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                                <tr>
                                    <th class="py-2.5 px-4">#</th>
                                    <th class="py-2.5 px-4">Component / Cost Item</th>
                                    <th class="py-2.5 px-4 text-right">Amount ($)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                                <tr>
                                    <td class="py-2.5 px-4 text-slate-400 font-mono">1</td>
                                    <td class="py-2.5 px-4 font-semibold text-slate-800">Labor & Technician Services</td>
                                    <td class="py-2.5 px-4 text-right font-mono font-bold">${{ number_format($quote->labor_cost, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-4 text-slate-400 font-mono">2</td>
                                    <td class="py-2.5 px-4 font-semibold text-slate-800">Materials, Parts & Supplies</td>
                                    <td class="py-2.5 px-4 text-right font-mono font-bold">${{ number_format($quote->materials_cost, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-4 text-slate-400 font-mono">3</td>
                                    <td class="py-2.5 px-4 font-semibold text-slate-800">Specialized Machinery & Equipment</td>
                                    <td class="py-2.5 px-4 text-right font-mono font-bold">${{ number_format($quote->equipment_cost, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-4 text-slate-400 font-mono">4</td>
                                    <td class="py-2.5 px-4 font-semibold text-slate-800">Trip & Logistics / Service Call Charge</td>
                                    <td class="py-2.5 px-4 text-right font-mono font-bold">${{ number_format($quote->trip_charge, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-4 text-slate-400 font-mono">5</td>
                                    <td class="py-2.5 px-4 font-semibold text-slate-800">Additional Charges & Permits</td>
                                    <td class="py-2.5 px-4 text-right font-mono font-bold">${{ number_format($quote->additional_charges, 2) }}</td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-slate-50/80 divide-y divide-slate-200 border-t border-slate-200">
                                <tr>
                                    <td colspan="2" class="py-2.5 px-4 text-right font-bold text-slate-600">Subtotal</td>
                                    <td class="py-2.5 px-4 text-right font-mono font-bold text-slate-800">${{ number_format($quote->subtotal, 2) }}</td>
                                </tr>
                                @if ($quote->discount > 0)
                                    <tr>
                                        <td colspan="2" class="py-2 px-4 text-right font-bold text-emerald-600">Special Promotional Discount</td>
                                        <td class="py-2 px-4 text-right font-mono font-bold text-emerald-600">-${{ number_format($quote->discount, 2) }}</td>
                                    </tr>
                                @endif
                                @if ($quote->tax_amount > 0 || $quote->tax_rate > 0)
                                    <tr>
                                        <td colspan="2" class="py-2 px-4 text-right font-bold text-slate-600">Applicable Taxes ({{ number_format($quote->tax_rate, 1) }}%)</td>
                                        <td class="py-2 px-4 text-right font-mono font-bold text-slate-800">+${{ number_format($quote->tax_amount, 2) }}</td>
                                    </tr>
                                @endif
                                <tr class="bg-gradient-to-r from-amber-500/10 to-amber-500/20 text-slate-900">
                                    <td colspan="2" class="py-3.5 px-4 text-right font-extrabold uppercase text-xs tracking-wider text-slate-900">Total Price Quotation</td>
                                    <td class="py-3.5 px-4 text-right font-mono font-black text-base text-[#8F6B20]">${{ number_format($quote->total_price, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Terms & Conditions --}}
                @if ($quote->terms_and_conditions)
                    <div class="pt-4 border-t border-slate-100">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Terms and Conditions</h4>
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 whitespace-pre-line leading-relaxed">
                            {{ $quote->terms_and_conditions }}
                        </div>
                    </div>
                @endif
            </x-card>
        </div>

        {{-- Right 1 Column: Customer Details, Status Workflow & Notes --}}
        <div class="space-y-6">
            
            {{-- Status Management Card --}}
            <x-card title="Quote Status & Workflow">
                <form method="POST" action="{{ route('dashboard.quotes.status', $quote) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Update Status <span class="text-rose-500">*</span>
                        </label>
                        <select
                            name="status"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs font-bold text-slate-800 focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059]"
                        >
                            <option value="pending" {{ $quote->status->value === 'pending' ? 'selected' : '' }}>Pending Review</option>
                            <option value="approved" {{ $quote->status->value === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="declined" {{ $quote->status->value === 'declined' ? 'selected' : '' }}>Declined</option>
                            <option value="ask_for_question" {{ $quote->status->value === 'ask_for_question' ? 'selected' : '' }}>Customer Asked Question</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Customer / Response Notes</label>
                        <textarea
                            name="customer_notes"
                            rows="2"
                            class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-xs text-slate-800 focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059]"
                            placeholder="Reason for decline or specific customer question..."
                        >{{ $quote->customer_notes }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Internal Admin Notes</label>
                        <textarea
                            name="admin_notes"
                            rows="2"
                            class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-xs text-slate-800 focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059]"
                            placeholder="Private team notes..."
                        >{{ $quote->admin_notes }}</textarea>
                    </div>

                    <button
                        type="submit"
                        class="w-full py-2 px-4 rounded-xl bg-[#C5A059] hover:bg-[#B8903B] text-slate-950 text-xs font-bold shadow-xs hover:shadow-md transition-all cursor-pointer"
                    >
                        Update Quote Status
                    </button>
                </form>
            </x-card>

            {{-- Customer Card --}}
            <x-card title="Customer Information">
                <div class="flex items-center gap-3 mb-4 pb-4 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-full bg-slate-900 text-[#C5A059] flex items-center justify-center font-bold text-sm border border-amber-500/20 shadow-xs">
                        {{ strtoupper(substr($quote->user?->name ?? 'C', 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-bold text-slate-900 text-sm">{{ $quote->user?->name ?? 'Customer' }}</div>
                        <div class="text-xs text-slate-500">{{ $quote->user?->email }}</div>
                    </div>
                </div>

                <div class="space-y-2.5 text-xs text-slate-600">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Phone Number:</span>
                        <span class="font-semibold text-slate-800">{{ $quote->user?->phone ?? 'Not provided' }}</span>
                    </div>
                    <div class="flex items-start justify-between gap-2">
                        <span class="text-slate-400 shrink-0">Service Address:</span>
                        <span class="font-semibold text-slate-800 text-right">{{ $quote->serviceRequest?->address?->address ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-start justify-between gap-2">
                        <span class="text-slate-400 shrink-0">Property Details:</span>
                        <span class="font-semibold text-slate-800 text-right">{{ $quote->serviceRequest?->property_information ?? 'N/A' }}</span>
                    </div>
                </div>
            </x-card>

            {{-- Service Request Link Card --}}
            <x-card title="Associated Service Request">
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-800">#REQ-{{ str_pad($quote->service_request_id, 5, '0', STR_PAD_LEFT) }}</span>
                        <span class="font-semibold text-amber-700">{{ ucfirst(str_replace('_', ' ', $quote->serviceRequest?->status?->value ?? 'pending')) }}</span>
                    </div>
                    <p class="text-slate-600 line-clamp-2">{{ $quote->serviceRequest?->description }}</p>
                    <div class="pt-2 border-t border-slate-200">
                        <a
                            href="{{ route('dashboard.service-requests.show', $quote->service_request_id) }}"
                            class="text-xs font-bold text-[#8F6B20] hover:text-[#C5A059] flex items-center gap-1 transition-colors"
                        >
                            <span>Open Full Service Request</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
            </x-card>

        </div>
    </div>
</x-dashboard.layout>
