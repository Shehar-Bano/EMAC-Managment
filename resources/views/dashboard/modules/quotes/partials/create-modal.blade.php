{{-- Create / Send Quote Modal Popup --}}
<div
    id="create-quote-modal"
    class="fixed inset-0 z-50 hidden overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
>
    {{-- Backdrop --}}
    <div
        id="quote-modal-backdrop"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-300 opacity-0"
        onclick="closeQuoteModal()"
    ></div>

    {{-- Modal Dialog Container --}}
    <div class="flex min-h-full items-center justify-center p-4 sm:p-8 text-center my-6 sm:my-10">
        <div
            id="quote-modal-panel"
            class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all duration-300 opacity-0 translate-y-4 w-full max-w-3xl border border-slate-200/90"
        >
            {{-- Modal Header (Clean Light Theme with Warm Gold Accents & Generous Side Padding) --}}
            <div style="padding: 24px 36px;" class="bg-gradient-to-b from-[#FAF8F4] to-white flex items-center justify-between border-b border-slate-200">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-2xl bg-[#C5A059]/15 border border-[#C5A059]/30 flex items-center justify-center text-[#8F6B20] shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Issue Price Quotation</h3>
                            <span id="quote-modal-request-badge" class="text-xs font-mono font-bold bg-[#C5A059]/15 text-[#8F6B20] px-2.5 py-0.5 rounded-lg border border-[#C5A059]/30 shadow-2xs">#REQ-00000</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                            <span>Customer:</span>
                            <strong id="quote-modal-customer-name" class="font-bold text-slate-900">Customer Name</strong>
                        </p>
                    </div>
                </div>
                <button
                    type="button"
                    onclick="closeQuoteModal()"
                    class="rounded-xl p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer"
                    title="Close Modal"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Form Body (With Explicit Inner Padding Container: 36px Sides) --}}
            <form id="create-quote-form" method="POST" action="{{ route('dashboard.quotes.store') }}">
                @csrf
                <input type="hidden" name="service_request_id" id="quote-modal-request-id" value="">

                <div style="padding: 28px 36px 36px 36px;" class="space-y-6">
                    {{-- Scope of Work / Service Description --}}
                    <div>
                        <label for="quote-service-description" class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-2">
                            Service Description & Scope of Work <span class="text-rose-500">*</span>
                        </label>
                        <textarea
                            name="service_description"
                            id="quote-service-description"
                            rows="3"
                            required
                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-xs text-slate-900 placeholder-slate-400 focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] transition-all leading-relaxed shadow-2xs"
                            placeholder="Provide detailed breakdown of the diagnostics, service, repairs, and deliverables..."
                        ></textarea>
                    </div>

                    {{-- Itemized Pricing Section --}}
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900 mb-3.5 flex items-center gap-1.5 pb-2 border-b border-slate-100">
                            <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Itemized Cost Components</span>
                        </h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                            {{-- Labor --}}
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1.5">Labor Cost ($)</label>
                                <div class="flex rounded-xl shadow-2xs overflow-hidden border border-slate-300 focus-within:border-[#C5A059] focus-within:ring-1 focus-within:ring-[#C5A059]">
                                    <span class="inline-flex items-center px-3.5 bg-slate-100/90 text-slate-600 font-bold text-xs border-r border-slate-200 select-none">$</span>
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="labor_cost"
                                        id="quote-labor-cost"
                                        value="0.00"
                                        class="quote-calc-input block w-full border-0 px-3.5 py-2.5 text-xs font-mono font-bold text-slate-800 text-right focus:outline-none focus:ring-0"
                                    >
                                </div>
                            </div>

                            {{-- Materials --}}
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1.5">Materials Cost ($)</label>
                                <div class="flex rounded-xl shadow-2xs overflow-hidden border border-slate-300 focus-within:border-[#C5A059] focus-within:ring-1 focus-within:ring-[#C5A059]">
                                    <span class="inline-flex items-center px-3.5 bg-slate-100/90 text-slate-600 font-bold text-xs border-r border-slate-200 select-none">$</span>
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="materials_cost"
                                        id="quote-materials-cost"
                                        value="0.00"
                                        class="quote-calc-input block w-full border-0 px-3.5 py-2.5 text-xs font-mono font-bold text-slate-800 text-right focus:outline-none focus:ring-0"
                                    >
                                </div>
                            </div>

                            {{-- Equipment --}}
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1.5">Equipment ($)</label>
                                <div class="flex rounded-xl shadow-2xs overflow-hidden border border-slate-300 focus-within:border-[#C5A059] focus-within:ring-1 focus-within:ring-[#C5A059]">
                                    <span class="inline-flex items-center px-3.5 bg-slate-100/90 text-slate-600 font-bold text-xs border-r border-slate-200 select-none">$</span>
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="equipment_cost"
                                        id="quote-equipment-cost"
                                        value="0.00"
                                        class="quote-calc-input block w-full border-0 px-3.5 py-2.5 text-xs font-mono font-bold text-slate-800 text-right focus:outline-none focus:ring-0"
                                    >
                                </div>
                            </div>

                            {{-- Trip / Service Charge --}}
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1.5">Trip / Service Charge ($)</label>
                                <div class="flex rounded-xl shadow-2xs overflow-hidden border border-slate-300 focus-within:border-[#C5A059] focus-within:ring-1 focus-within:ring-[#C5A059]">
                                    <span class="inline-flex items-center px-3.5 bg-slate-100/90 text-slate-600 font-bold text-xs border-r border-slate-200 select-none">$</span>
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="trip_charge"
                                        id="quote-trip-charge"
                                        value="0.00"
                                        class="quote-calc-input block w-full border-0 px-3.5 py-2.5 text-xs font-mono font-bold text-slate-800 text-right focus:outline-none focus:ring-0"
                                    >
                                </div>
                            </div>

                            {{-- Additional Charges --}}
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1.5">Additional Charges ($)</label>
                                <div class="flex rounded-xl shadow-2xs overflow-hidden border border-slate-300 focus-within:border-[#C5A059] focus-within:ring-1 focus-within:ring-[#C5A059]">
                                    <span class="inline-flex items-center px-3.5 bg-slate-100/90 text-slate-600 font-bold text-xs border-r border-slate-200 select-none">$</span>
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="additional_charges"
                                        id="quote-additional-charges"
                                        value="0.00"
                                        class="quote-calc-input block w-full border-0 px-3.5 py-2.5 text-xs font-mono font-bold text-slate-800 text-right focus:outline-none focus:ring-0"
                                    >
                                </div>
                            </div>

                            {{-- Discount --}}
                            <div>
                                <label class="block text-[11px] font-bold text-emerald-700 mb-1.5">Discounts ($)</label>
                                <div class="flex rounded-xl shadow-2xs overflow-hidden border border-emerald-300 focus-within:border-emerald-500 focus-within:ring-1 focus-within:ring-emerald-500">
                                    <span class="inline-flex items-center px-3.5 bg-emerald-100 text-emerald-800 font-bold text-xs border-r border-emerald-200 select-none">-$</span>
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="discount"
                                        id="quote-discount"
                                        value="0.00"
                                        class="quote-calc-input block w-full border-0 bg-emerald-50/20 px-3.5 py-2.5 text-xs font-mono font-bold text-emerald-800 text-right focus:outline-none focus:ring-0"
                                    >
                                </div>
                            </div>

                            {{-- Tax Rate --}}
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1.5">Tax Rate (%)</label>
                                <div class="flex rounded-xl shadow-2xs overflow-hidden border border-slate-300 focus-within:border-[#C5A059] focus-within:ring-1 focus-within:ring-[#C5A059]">
                                    <input
                                        type="number"
                                        step="0.1"
                                        min="0"
                                        max="100"
                                        name="tax_rate"
                                        id="quote-tax-rate"
                                        value="0.0"
                                        class="quote-calc-input block w-full border-0 px-3.5 py-2.5 text-xs font-mono font-bold text-slate-800 text-right focus:outline-none focus:ring-0"
                                    >
                                    <span class="inline-flex items-center px-3.5 bg-slate-100/90 text-slate-600 font-bold text-xs border-l border-slate-200 select-none">%</span>
                                </div>
                            </div>

                            {{-- Expiration Date --}}
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-bold text-slate-700 mb-1.5">
                                    Quote Expiration Date <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="date"
                                    name="expires_at"
                                    id="quote-expires-at"
                                    required
                                    value="{{ date('Y-m-d', strtotime('+14 days')) }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800 focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] shadow-2xs"
                                >
                            </div>
                        </div>
                    </div>

                    {{-- Live Financial Calculation Summary Card (Luxury Light Gold Card) --}}
                    <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-[#FAF8F4] via-[#F5EFE6] to-[#FAF8F4] border border-[#C5A059]/40 shadow-xs">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 text-center">
                            <div class="p-3.5 bg-white rounded-xl border border-slate-200/90 shadow-2xs">
                                <div class="text-[10px] uppercase font-bold tracking-wider text-slate-500">Subtotal</div>
                                <div id="quote-summary-subtotal" class="text-sm sm:text-base font-black text-slate-900 font-mono mt-1">$0.00</div>
                            </div>
                            <div class="p-3.5 bg-white rounded-xl border border-emerald-200 shadow-2xs">
                                <div class="text-[10px] uppercase font-bold tracking-wider text-emerald-700">Discount</div>
                                <div id="quote-summary-discount" class="text-sm sm:text-base font-black text-emerald-600 font-mono mt-1">-$0.00</div>
                            </div>
                            <div class="p-3.5 bg-white rounded-xl border border-slate-200/90 shadow-2xs">
                                <div class="text-[10px] uppercase font-bold tracking-wider text-slate-500">Tax Amount</div>
                                <div id="quote-summary-tax" class="text-sm sm:text-base font-black text-slate-900 font-mono mt-1">+$0.00</div>
                            </div>
                            <div class="p-3.5 bg-gradient-to-br from-[#FAF5EB] to-[#F5EAD4] border-2 border-[#C5A059] rounded-xl shadow-xs">
                                <div class="text-[10px] uppercase font-black tracking-wider text-[#8F6B20]">Total Price</div>
                                <div id="quote-summary-total" class="text-base sm:text-lg font-black text-slate-950 font-mono mt-0.5">$0.00</div>
                            </div>
                        </div>
                    </div>

                    {{-- Terms & Conditions and Admin Notes --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        <div>
                            <label for="quote-terms" class="block text-[11px] font-bold text-slate-700 mb-1.5">Terms and Conditions</label>
                            <textarea
                                name="terms_and_conditions"
                                id="quote-terms"
                                rows="3"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-700 focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] shadow-2xs"
                            >1. Valid until expiration date.
2. Includes all labor & materials specified.
3. Extra work quoted separately.</textarea>
                        </div>

                        <div>
                            <label for="quote-admin-notes" class="block text-[11px] font-bold text-slate-700 mb-1.5">Internal Notes (Optional)</label>
                            <textarea
                                name="admin_notes"
                                id="quote-admin-notes"
                                rows="3"
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-700 focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] shadow-2xs"
                                placeholder="Special team notes or supplier references..."
                            ></textarea>
                        </div>
                    </div>

                    {{-- Modal Footer Actions --}}
                    <div class="pt-5 border-t border-slate-200 flex items-center justify-end gap-3">
                        <button
                            type="button"
                            onclick="closeQuoteModal()"
                            class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 transition-colors cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            style="background-color: #9E7B31; background: linear-gradient(135deg, #C5A059 0%, #8F6B20 100%); color: #ffffff;"
                            class="px-6 py-2.5 rounded-xl text-white text-xs font-bold shadow-md hover:shadow-lg hover:brightness-110 transition-all cursor-pointer flex items-center gap-2"
                        >
                            <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            <span class="text-white font-bold">Generate & Send Quote</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openQuoteModalFromButton(btn) {
        const requestId = btn.getAttribute('data-request-id');
        const reqFormatted = btn.getAttribute('data-request-number');
        const customerName = btn.getAttribute('data-customer-name');
        const defaultDescription = btn.getAttribute('data-description');
        openQuoteModal(requestId, reqFormatted, customerName, defaultDescription);
    }

    function openQuoteModal(requestId, reqFormatted, customerName, defaultDescription) {
        document.getElementById('quote-modal-request-id').value = requestId;
        document.getElementById('quote-modal-request-badge').innerText = reqFormatted || ('#REQ-' + String(requestId).padStart(5, '0'));
        document.getElementById('quote-modal-customer-name').innerText = customerName || 'Valued Customer';
        
        const descElem = document.getElementById('quote-service-description');
        if (descElem && defaultDescription) {
            descElem.value = defaultDescription;
        }

        const modal = document.getElementById('create-quote-modal');
        const backdrop = document.getElementById('quote-modal-backdrop');
        const panel = document.getElementById('quote-modal-panel');

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            panel.classList.remove('opacity-0', 'translate-y-4');
            panel.classList.add('opacity-100', 'translate-y-0');
        }, 20);

        recalculateQuoteSummary();
    }

    function closeQuoteModal() {
        const modal = document.getElementById('create-quote-modal');
        const backdrop = document.getElementById('quote-modal-backdrop');
        const panel = document.getElementById('quote-modal-panel');

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'translate-y-0');
        panel.classList.add('opacity-0', 'translate-y-4');

        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }, 250);
    }

    function recalculateQuoteSummary() {
        const labor = parseFloat(document.getElementById('quote-labor-cost')?.value) || 0;
        const materials = parseFloat(document.getElementById('quote-materials-cost')?.value) || 0;
        const equipment = parseFloat(document.getElementById('quote-equipment-cost')?.value) || 0;
        const trip = parseFloat(document.getElementById('quote-trip-charge')?.value) || 0;
        const additional = parseFloat(document.getElementById('quote-additional-charges')?.value) || 0;
        const discount = parseFloat(document.getElementById('quote-discount')?.value) || 0;
        const taxRate = parseFloat(document.getElementById('quote-tax-rate')?.value) || 0;

        const subtotal = labor + materials + equipment + trip + additional;
        const netBeforeTax = Math.max(0, subtotal - discount);
        const taxAmount = (netBeforeTax * (taxRate / 100));
        const total = netBeforeTax + taxAmount;

        const formatCurrency = (val) => '$' + val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        if (document.getElementById('quote-summary-subtotal')) {
            document.getElementById('quote-summary-subtotal').innerText = formatCurrency(subtotal);
        }
        if (document.getElementById('quote-summary-discount')) {
            document.getElementById('quote-summary-discount').innerText = '-' + formatCurrency(discount);
        }
        if (document.getElementById('quote-summary-tax')) {
            document.getElementById('quote-summary-tax').innerText = '+' + formatCurrency(taxAmount);
        }
        if (document.getElementById('quote-summary-total')) {
            document.getElementById('quote-summary-total').innerText = formatCurrency(total);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.quote-calc-input').forEach(input => {
            input.addEventListener('input', recalculateQuoteSummary);
            input.addEventListener('change', recalculateQuoteSummary);
        });
    });
</script>
