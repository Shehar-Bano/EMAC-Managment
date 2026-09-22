<x-website.layout :title="'EMAC Development, LLC. | Professional Handyman & Plumbing Services'">

    {{-- Hero Section --}}
    <section class="bg-gradient-to-b from-white to-slate-50 border-b border-slate-200 py-16 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                {{-- Left Text Column --}}
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-[#C5A059]/40 text-[#8F6B20] text-xs font-semibold">
                        <span>Grand Cayman • Florida • Jamaica</span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        Reliable Handyman & Certified Plumbing Services
                    </h1>

                    <p class="text-base sm:text-lg text-slate-600 max-w-2xl leading-relaxed">
                        From minor household fixes and preventative property maintenance to complete plumbing repairs and fixture replacements. Professional, prompt, and licensed service you can count on.
                    </p>

                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 px-6 py-3.5 text-sm font-bold text-slate-900 bg-[#C5A059] hover:bg-[#b8934b] rounded-xl shadow-sm transition-colors">
                            <span>Request a Free Quote</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                        <a href="{{ route('services') }}" class="inline-flex items-center gap-2 px-6 py-3.5 text-sm font-semibold text-slate-700 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-300 rounded-xl transition-colors">
                            <span>Browse All Services</span>
                        </a>
                    </div>

                    {{-- Trust Indicators --}}
                    <div class="pt-6 border-t border-slate-200 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs text-slate-600">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#C5A059] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-medium">Licensed & Insured</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#C5A059] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-medium">Prompt Response</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#C5A059] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="font-medium">Upfront Pricing</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#C5A059] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="font-medium">100% Guaranteed</span>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Clean Quick Contact Card --}}
                <div class="lg:col-span-5">
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
                        <div class="border-b border-slate-100 pb-4 mb-6">
                            <h2 class="text-lg font-bold text-slate-900">Need Immediate Help or a Quote?</h2>
                            <p class="text-xs text-slate-500 mt-1">Contact our service team directly or book online.</p>
                        </div>

                        <div class="space-y-4">
                            <a href="tel:+18005550199" class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 hover:bg-amber-50/50 border border-slate-200 transition-colors group">
                                <div class="w-10 h-10 rounded-lg bg-[#C5A059]/15 text-[#8F6B20] flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </div>
                                <div>
                                    <div class="text-xs text-slate-500">Call Toll-Free</div>
                                    <div class="text-sm font-bold text-slate-900 group-hover:text-[#8F6B20]">+1 (800) 555-0199</div>
                                </div>
                            </a>

                            <a href="mailto:info@emacdevelopment.com" class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 hover:bg-amber-50/50 border border-slate-200 transition-colors group">
                                <div class="w-10 h-10 rounded-lg bg-[#C5A059]/15 text-[#8F6B20] flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <div class="text-xs text-slate-500">Email Customer Service</div>
                                    <div class="text-sm font-bold text-slate-900 group-hover:text-[#8F6B20]">info@emacdevelopment.com</div>
                                </div>
                            </a>

                            <div class="pt-2">
                                <a href="{{ route('contact') }}" class="block w-full text-center px-4 py-3 text-xs font-bold text-slate-900 bg-[#C5A059] hover:bg-[#b8934b] rounded-xl transition-colors">
                                    Submit Online Inquiry
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Core Services Section --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-[#8F6B20]">What We Do</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Our Core Services</h2>
                <p class="text-sm text-slate-600 mt-2">
                    Professional, guaranteed repair and installation services tailored to residential homes, commercial properties, and property managers.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                @foreach ($categories as $category)
                    <div class="bg-slate-50 rounded-2xl border border-slate-200 p-6 sm:p-8 flex flex-col justify-between hover:border-[#C5A059]/60 transition-colors">
                        <div>
                            <div class="flex items-center gap-3.5 mb-4">
                                <div class="w-11 h-11 rounded-xl bg-white border border-slate-200 text-[#C5A059] flex items-center justify-center text-xl shadow-2xs shrink-0">
                                    {{ $category->icon ?? '🛠️' }}
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-slate-900">{{ $category->name }}</h3>
                                    <span class="text-xs font-semibold text-[#8F6B20]">{{ $category->activeSubcategories->count() }} Available Services</span>
                                </div>
                            </div>

                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-6">
                                {{ $category->description }}
                            </p>

                            <div class="mb-6">
                                <div class="text-xs font-bold text-slate-700 mb-2.5">Included Services:</div>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($category->activeSubcategories as $sub)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs bg-white border border-slate-200 text-slate-700">
                                            {{ $sub->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
                            <a href="{{ route('services') }}" class="text-xs font-semibold text-slate-700 hover:text-slate-900">
                                View Details &rarr;
                            </a>
                            <a href="{{ route('contact', ['category_id' => $category->id]) }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-slate-900 bg-[#C5A059] hover:bg-[#b8934b] rounded-lg transition-colors">
                                <span>Get a Quote</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- How It Works Section --}}
    <section class="py-16 bg-slate-50 border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-[#8F6B20]">Simple Process</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">How It Works</h2>
                <p class="text-sm text-slate-600 mt-2">Getting your home or commercial repairs done is quick and hassle-free.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-xl border border-slate-200 p-6 relative">
                    <div class="w-8 h-8 rounded-full bg-amber-50 text-[#8F6B20] font-bold text-sm flex items-center justify-center mb-4 border border-[#C5A059]/30">
                        1
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">Request Service</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Submit our online quote form with your service details or call our support line directly.
                    </p>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 p-6 relative">
                    <div class="w-8 h-8 rounded-full bg-amber-50 text-[#8F6B20] font-bold text-sm flex items-center justify-center mb-4 border border-[#C5A059]/30">
                        2
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">Clear Estimate & Schedule</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Receive a transparent upfront estimate with no hidden costs and book a convenient appointment.
                    </p>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 p-6 relative">
                    <div class="w-8 h-8 rounded-full bg-amber-50 text-[#8F6B20] font-bold text-sm flex items-center justify-center mb-4 border border-[#C5A059]/30">
                        3
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">Expert Execution</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Our licensed technician arrives on time, completes the work cleanly, and guarantees quality.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Why Choose EMAC Section --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-6 space-y-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#8F6B20]">Why Choose EMAC</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
                        Professional Trades with Accountable Service
                    </h2>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        We take pride in providing dependable, transparent, and high-quality repairs. Whether managing a single residential fix or ongoing multi-unit property maintenance, our team treats your property with care and respect.
                    </p>

                    <div class="space-y-3 pt-2">
                        <div class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs shrink-0 mt-0.5">✓</div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">Multi-Trade Convenience</h4>
                                <p class="text-xs text-slate-500">Handle plumbing and general handyman tasks in one single visit.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs shrink-0 mt-0.5">✓</div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">Licensed & Insured Professionals</h4>
                                <p class="text-xs text-slate-500">Skilled, background-checked trade technicians for your peace of mind.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs shrink-0 mt-0.5">✓</div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">Upfront & Fair Pricing</h4>
                                <p class="text-xs text-slate-500">No surprises or hidden fees. Transparent quotes before work begins.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-6">
                    <div class="bg-slate-900 text-white rounded-2xl p-8 space-y-6">
                        <h3 class="text-xl font-bold">Ready to schedule your repair or maintenance?</h3>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Reach out today for a free quote or consultation. Our support team is ready to assist you across all service markets.
                        </p>
                        <div class="flex flex-wrap gap-4 pt-2">
                            <a href="{{ route('contact') }}" class="px-5 py-3 text-xs font-bold text-slate-900 bg-[#C5A059] hover:bg-[#b8934b] rounded-lg transition-colors">
                                Request a Quote Online
                            </a>
                            <a href="tel:+18005550199" class="px-5 py-3 text-xs font-semibold text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 rounded-lg transition-colors">
                                Call (800) 555-0199
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-website.layout>
