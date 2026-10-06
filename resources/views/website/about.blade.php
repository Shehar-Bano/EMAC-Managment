<x-website.layout :title="'About Us — EMAC Development, LLC.'">

    {{-- Hero Section --}}
    <section class="bg-gradient-to-b from-white to-slate-50 border-b border-slate-200 py-14 lg:py-18">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-[#C5A059]/40 text-[#8F6B20] text-xs font-semibold mb-3">
                <span>Who We Are</span>
            </div>

            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                About EMAC Development, LLC.
            </h1>

            <p class="mt-3 text-sm sm:text-base text-slate-600 max-w-2xl mx-auto leading-relaxed">
                Dedicated trade professionals delivering licensed handyman repairs, preventative property maintenance, and certified plumbing solutions across Grand Cayman, Florida, and Jamaica.
            </p>
        </div>
    </section>

    {{-- Company Story & Values --}}
    <section class="py-14 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-6 space-y-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#8F6B20]">Our Mission</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
                        Reliable Tradesmanship with Modern Service Standards
                    </h2>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        EMAC Development, LLC. was founded to solve a common property challenge: finding dependable, licensed, and skilled tradesmen who arrive on time, communicate clearly, and stand behind their work.
                    </p>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Whether you are a homeowner needing a small drywall patch or faucet replacement, or a property manager requiring regular turnover repairs and plumbing maintenance, we provide seamless service, transparent estimates, and peace of mind.
                    </p>
                </div>

                <div class="lg:col-span-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-slate-50 p-6 rounded-xl border border-slate-200">
                        <div class="w-10 h-10 rounded-lg bg-amber-50 border border-[#C5A059]/30 text-xl flex items-center justify-center mb-3">
                            🔧
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 mb-1">Handyman Expertise</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Complete residential carpentry, doors, windows, drywall, painting, and fixture installations.</p>
                    </div>

                    <div class="bg-slate-50 p-6 rounded-xl border border-slate-200">
                        <div class="w-10 h-10 rounded-lg bg-amber-50 border border-[#C5A059]/30 text-xl flex items-center justify-center mb-3">
                            🚰
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 mb-1">Certified Plumbing</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Licensed plumbing diagnostics, water heaters, leak repairs, and fixture replacements.</p>
                    </div>

                    <div class="bg-slate-50 p-6 rounded-xl border border-slate-200">
                        <div class="w-10 h-10 rounded-lg bg-amber-50 border border-[#C5A059]/30 text-xl flex items-center justify-center mb-3">
                            🛡️
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 mb-1">Licensed & Insured</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Full coverage and strict safety compliance protecting your home and commercial assets.</p>
                    </div>

                    <div class="bg-slate-50 p-6 rounded-xl border border-slate-200">
                        <div class="w-10 h-10 rounded-lg bg-amber-50 border border-[#C5A059]/30 text-xl flex items-center justify-center mb-3">
                            🤝
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 mb-1">Customer First</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Dedicated support team, upfront pricing, and guaranteed customer satisfaction.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Regional Service Footprint --}}
    <section class="py-14 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="text-xs font-bold uppercase tracking-wider text-[#8F6B20]">Our Service Areas</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Regional Service Markets</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-xl border border-slate-200">
                    <h3 class="text-base font-bold text-slate-900 mb-2">Grand Cayman</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Comprehensive handyman repairs, plumbing maintenance, residential renovations, and recurring property care across the Cayman Islands.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200">
                    <h3 class="text-base font-bold text-slate-900 mb-2">Florida, USA</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Certified plumbing contractor and handyman repairs for residential homeowners, condos, and commercial facilities.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200">
                    <h3 class="text-base font-bold text-slate-900 mb-2">Jamaica</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Property maintenance, carpentry, plumbing diagnostics, and emergency repair dispatch for residential and hospitality clients.
                    </p>
                </div>
            </div>
        </div>
    </section>

</x-website.layout>
