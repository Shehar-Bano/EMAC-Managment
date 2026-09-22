<x-website.layout :title="'Frequently Asked Questions & Support — EMAC Development, LLC.'">

    {{-- Hero Section --}}
    <section class="relative overflow-hidden py-14 lg:py-20 bg-gradient-to-b from-[#FAF8F4] via-white to-[#F5F1E8] border-b border-slate-200/80">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#C5A05912_1px,transparent_1px),linear-gradient(to_bottom,#C5A05912_1px,transparent_1px)] bg-[size:3.5rem_3.5rem] pointer-events-none"></div>
        <div class="absolute top-1/4 left-1/4 w-80 h-80 bg-[#C5A059]/15 rounded-full blur-3xl pointer-events-none animate-pulse-glow"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal-init">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#C5A059]/15 border border-[#C5A059]/35 text-[#8F6B20] text-xs font-bold mb-4 backdrop-blur-md shadow-2xs">
                <x-gold-icon name="star" size="xs" class="text-[#C5A059]" />
                <span>KNOWLEDGEBASE & ASSISTANCE</span>
            </div>

            <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight">
                Frequently Asked Questions
            </h1>

            <p class="mt-4 text-sm sm:text-base text-slate-600 max-w-2xl mx-auto leading-relaxed">
                Find clear answers about our Cayman house plans, licensed contracting in Florida, Jamaican maintenance, scheduling, and billing.
            </p>
        </div>
    </section>

    {{-- Main FAQ Accordion Grid --}}
    <section class="py-16 bg-white" x-data="{ openItem: null }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

            {{-- Group 1: Cayman Islands House Plans --}}
            <div class="reveal-init">
                <h2 class="text-lg font-bold text-[#8F6B20] uppercase tracking-wider mb-4 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-[#C5A059]/15 text-[#C5A059] flex items-center justify-center border border-[#C5A059]/30">
                        <x-gold-icon name="blueprint" size="sm" />
                    </span>
                    <span>Cayman Islands House Plans & Architecture</span>
                </h2>

                <div class="space-y-3">
                    <div class="rounded-2xl border border-slate-200/90 bg-[#FAF8F4] hover:border-[#C5A059]/50 transition-all shadow-2xs hover:shadow-xs p-5" :class="openItem === 1 ? 'border-[#C5A059] bg-white ring-1 ring-[#C5A059]/20' : ''">
                        <button
                            type="button"
                            @click="openItem = openItem === 1 ? null : 1"
                            class="w-full flex items-center justify-between text-left text-sm font-bold text-slate-900 cursor-pointer"
                        >
                            <span>Are EMAC house plans compliant with Cayman Islands building codes?</span>
                            <span class="text-[#8F6B20] font-bold text-lg transition-transform duration-200" :class="openItem === 1 ? 'rotate-45' : ''">+</span>
                        </button>
                        <div x-show="openItem === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="mt-3 text-xs text-slate-600 leading-relaxed border-t border-slate-200/60 pt-3">
                            Yes. All stock house plans offered through our Cayman catalog are engineered according to Cayman Islands planning guidelines, mandatory setback requirements, hurricane wind resistance standards, and Caribbean thermal efficiencies.
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200/90 bg-[#FAF8F4] hover:border-[#C5A059]/50 transition-all shadow-2xs hover:shadow-xs p-5" :class="openItem === 2 ? 'border-[#C5A059] bg-white ring-1 ring-[#C5A059]/20' : ''">
                        <button
                            type="button"
                            @click="openItem = openItem === 2 ? null : 2"
                            class="w-full flex items-center justify-between text-left text-sm font-bold text-slate-900 cursor-pointer"
                        >
                            <span>Can I request custom modifications to a stock house plan?</span>
                            <span class="text-[#8F6B20] font-bold text-lg transition-transform duration-200" :class="openItem === 2 ? 'rotate-45' : ''">+</span>
                        </button>
                        <div x-show="openItem === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="mt-3 text-xs text-slate-600 leading-relaxed border-t border-slate-200/60 pt-3">
                            Absolutely. You can select any stock blueprint and submit a modification request (such as expanding bedrooms, adapting room configurations, altering exterior elevations, or customizing footprints to your specific lot). Our architectural team provides a tailored quote for the modifications.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Group 2: Construction & General Contracting --}}
            <div class="reveal-init">
                <h2 class="text-lg font-bold text-[#8F6B20] uppercase tracking-wider mb-4 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-[#C5A059]/15 text-[#C5A059] flex items-center justify-center border border-[#C5A059]/30">
                        <x-gold-icon name="hammer" size="sm" />
                    </span>
                    <span>General Contracting & Construction</span>
                </h2>

                <div class="space-y-3">
                    <div class="rounded-2xl border border-slate-200/90 bg-[#FAF8F4] hover:border-[#C5A059]/50 transition-all shadow-2xs hover:shadow-xs p-5" :class="openItem === 3 ? 'border-[#C5A059] bg-white ring-1 ring-[#C5A059]/20' : ''">
                        <button
                            type="button"
                            @click="openItem = openItem === 3 ? null : 3"
                            class="w-full flex items-center justify-between text-left text-sm font-bold text-slate-900 cursor-pointer"
                        >
                            <span>What credentials and licenses does EMAC Development hold?</span>
                            <span class="text-[#8F6B20] font-bold text-lg transition-transform duration-200" :class="openItem === 3 ? 'rotate-45' : ''">+</span>
                        </button>
                        <div x-show="openItem === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="mt-3 text-xs text-slate-600 leading-relaxed border-t border-slate-200/60 pt-3">
                            EMAC holds State-Certified General Contractor credentials and Certified Plumbing Contractor credentials in Florida, along with professional architectural and construction management capabilities across Jamaica and the Cayman Islands.
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200/90 bg-[#FAF8F4] hover:border-[#C5A059]/50 transition-all shadow-2xs hover:shadow-xs p-5" :class="openItem === 4 ? 'border-[#C5A059] bg-white ring-1 ring-[#C5A059]/20' : ''">
                        <button
                            type="button"
                            @click="openItem = openItem === 4 ? null : 4"
                            class="w-full flex items-center justify-between text-left text-sm font-bold text-slate-900 cursor-pointer"
                        >
                            <span>How does the turnkey construction process work?</span>
                            <span class="text-[#8F6B20] font-bold text-lg transition-transform duration-200" :class="openItem === 4 ? 'rotate-45' : ''">+</span>
                        </button>
                        <div x-show="openItem === 4" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="mt-3 text-xs text-slate-600 leading-relaxed border-t border-slate-200/60 pt-3">
                            We manage your project from initial design and permit documentation through procurement, foundation engineering, MEP installations, architectural finishes, and final occupancy certification.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Group 3: Plumbing, Handyman & Subscriptions --}}
            <div class="reveal-init">
                <h2 class="text-lg font-bold text-[#8F6B20] uppercase tracking-wider mb-4 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-[#C5A059]/15 text-[#C5A059] flex items-center justify-center border border-[#C5A059]/30">
                        <x-gold-icon name="tools" size="sm" />
                    </span>
                    <span>Plumbing, Handyman & Maintenance Subscriptions</span>
                </h2>

                <div class="space-y-3">
                    <div class="rounded-2xl border border-slate-200/90 bg-[#FAF8F4] hover:border-[#C5A059]/50 transition-all shadow-2xs hover:shadow-xs p-5" :class="openItem === 5 ? 'border-[#C5A059] bg-white ring-1 ring-[#C5A059]/20' : ''">
                        <button
                            type="button"
                            @click="openItem = openItem === 5 ? null : 5"
                            class="w-full flex items-center justify-between text-left text-sm font-bold text-slate-900 cursor-pointer"
                        >
                            <span>How do recurring property maintenance subscriptions work?</span>
                            <span class="text-[#8F6B20] font-bold text-lg transition-transform duration-200" :class="openItem === 5 ? 'rotate-45' : ''">+</span>
                        </button>
                        <div x-show="openItem === 5" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="mt-3 text-xs text-slate-600 leading-relaxed border-t border-slate-200/60 pt-3">
                            Our subscription programs allow homeowners, landlords, and vacation rental hosts to schedule monthly preventive checkups, seasonal air conditioning / plumbing audits, and enjoy prioritized emergency response with discounted repair rates.
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200/90 bg-[#FAF8F4] hover:border-[#C5A059]/50 transition-all shadow-2xs hover:shadow-xs p-5" :class="openItem === 6 ? 'border-[#C5A059] bg-white ring-1 ring-[#C5A059]/20' : ''">
                        <button
                            type="button"
                            @click="openItem = openItem === 6 ? null : 6"
                            class="w-full flex items-center justify-between text-left text-sm font-bold text-slate-900 cursor-pointer"
                        >
                            <span>Can I request a quote online before booking a technician?</span>
                            <span class="text-[#8F6B20] font-bold text-lg transition-transform duration-200" :class="openItem === 6 ? 'rotate-45' : ''">+</span>
                        </button>
                        <div x-show="openItem === 6" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="mt-3 text-xs text-slate-600 leading-relaxed border-t border-slate-200/60 pt-3">
                            Yes! Our digital service request platform allows you to upload photos of the issue, select your preferred date, and receive an itemized estimate outlining labor, materials, and terms before work begins.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- Need Help CTA --}}
    <section class="py-14 bg-[#FAF8F4] border-t border-slate-200/80 text-center reveal-init">
        <h3 class="text-xl font-bold text-slate-900">Have a specific question not listed here?</h3>
        <p class="text-xs text-slate-500 mt-1">Our customer service and technical team are available to assist.</p>
        <div class="mt-5">
            <a href="{{ route('contact') }}" class="btn-premium inline-flex items-center gap-2 px-6 py-3 text-xs font-bold text-slate-950 bg-gradient-to-r from-[#D4AF37] to-[#C5A059] rounded-xl shadow-md shadow-[#C5A059]/20">
                <x-gold-icon name="phone" size="xs" class="text-slate-950" />
                <span>Contact Support</span>
                <span class="arrow-slide">&rarr;</span>
            </a>
        </div>
    </section>

</x-website.layout>
