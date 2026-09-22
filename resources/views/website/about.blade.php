<x-website.layout :title="'About Us & Credentials — EMAC Development, LLC.'">

    {{-- Hero Section --}}
    <section class="relative overflow-hidden py-14 lg:py-20 bg-gradient-to-b from-[#FAF8F4] via-white to-[#F5F1E8] border-b border-slate-200/80">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#C5A05912_1px,transparent_1px),linear-gradient(to_bottom,#C5A05912_1px,transparent_1px)] bg-[size:3.5rem_3.5rem] pointer-events-none"></div>
        <div class="absolute top-1/4 left-1/3 w-80 h-80 bg-[#C5A059]/15 rounded-full blur-3xl pointer-events-none animate-pulse-glow"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal-init">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#C5A059]/15 border border-[#C5A059]/35 text-[#8F6B20] text-xs font-bold mb-4 backdrop-blur-md shadow-2xs">
                <x-gold-icon name="shield" size="xs" class="text-[#C5A059]" />
                <span>COMPANY PROFILE & CREDENTIALS</span>
            </div>

            <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight">
                About EMAC Development, LLC.
            </h1>

            <p class="mt-4 text-sm sm:text-base text-slate-600 max-w-2xl mx-auto leading-relaxed">
                A multi-disciplinary construction, architectural design, certified plumbing, and property services firm operating across Florida, Jamaica, and the Cayman Islands.
            </p>
        </div>
    </section>

    {{-- Company Mission & Credentials Overview (3D Layered Perspective) --}}
    <section class="py-16 bg-white perspective-container">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-6 space-y-5 reveal-init">
                    <span class="text-xs font-bold uppercase tracking-widest text-[#8F6B20]">Our Foundation</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Built on Integrity, Technical Rigor & Regional Expertise
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        EMAC Development was established to bridge the gap between architectural concept and turnkey project delivery. Whether developing residential house plans tailored to the Cayman Islands, executing licensed general contracting in Florida, or providing reliable property maintenance across Jamaica, we operate with a singular commitment to quality and transparency.
                    </p>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Our integrated workflow ensures that architectural design, structural engineering, certified plumbing trades, and ongoing property maintenance operate in harmony through our unified technology platform.
                    </p>
                </div>

                {{-- 3D Interactive Credentials Grid with Gold Icons --}}
                <div class="lg:col-span-6 grid grid-cols-1 sm:grid-cols-2 gap-4" data-reveal-stagger>
                    <div class="tilt-card p-6 rounded-3xl bg-[#FAF8F4] border border-[#C5A059]/30 shadow-2xs hover:shadow-luxury transition-all hover:border-[#C5A059]/70 preserve-3d" data-tilt>
                        <div class="card-glare"></div>
                        <div class="tilt-card-inner">
                            <div class="w-12 h-12 rounded-2xl bg-white border border-[#C5A059]/30 text-[#C5A059] flex items-center justify-center text-xl mb-4 shadow-2xs">
                                <x-gold-icon name="shield" size="md" />
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 mb-1">Certified General Contractor</h3>
                            <p class="text-xs text-slate-500 leading-relaxed">Fully licensed, insured, and certified for commercial and residential construction.</p>
                        </div>
                    </div>

                    <div class="tilt-card p-6 rounded-3xl bg-[#FAF8F4] border border-[#C5A059]/30 shadow-2xs hover:shadow-luxury transition-all hover:border-[#C5A059]/70 preserve-3d" data-tilt>
                        <div class="card-glare"></div>
                        <div class="tilt-card-inner">
                            <div class="w-12 h-12 rounded-2xl bg-white border border-[#C5A059]/30 text-[#C5A059] flex items-center justify-center text-xl mb-4 shadow-2xs">
                                <x-gold-icon name="wrench" size="md" />
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 mb-1">Certified Plumbing Contractor</h3>
                            <p class="text-xs text-slate-500 leading-relaxed">State-certified master plumbing diagnostics, water heating, and mainline installations.</p>
                        </div>
                    </div>

                    <div class="tilt-card p-6 rounded-3xl bg-[#FAF8F4] border border-[#C5A059]/30 shadow-2xs hover:shadow-luxury transition-all hover:border-[#C5A059]/70 preserve-3d" data-tilt>
                        <div class="card-glare"></div>
                        <div class="tilt-card-inner">
                            <div class="w-12 h-12 rounded-2xl bg-white border border-[#C5A059]/30 text-[#C5A059] flex items-center justify-center text-xl mb-4 shadow-2xs">
                                <x-gold-icon name="blueprint" size="md" />
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 mb-1">Cayman Code Compliant</h3>
                            <p class="text-xs text-slate-500 leading-relaxed">House plans engineered for Caribbean setbacks, hurricane wind loads, and thermal efficiency.</p>
                        </div>
                    </div>

                    <div class="tilt-card p-6 rounded-3xl bg-[#FAF8F4] border border-[#C5A059]/30 shadow-2xs hover:shadow-luxury transition-all hover:border-[#C5A059]/70 preserve-3d" data-tilt>
                        <div class="card-glare"></div>
                        <div class="tilt-card-inner">
                            <div class="w-12 h-12 rounded-2xl bg-white border border-[#C5A059]/30 text-[#C5A059] flex items-center justify-center text-xl mb-4 shadow-2xs">
                                <x-gold-icon name="tools" size="md" />
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 mb-1">Unified Tech Platform</h3>
                            <p class="text-xs text-slate-500 leading-relaxed">Online quoting, live technician dispatch, digital invoicing, and client portals.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Regional Service Footprint --}}
    <section class="py-16 bg-[#FAF8F5] border-t border-slate-200/80 perspective-container">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12 reveal-init">
                <span class="text-xs font-bold uppercase tracking-widest text-[#8F6B20]">Our Territory</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">
                    Three Strategic Regional Markets
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6" data-reveal-stagger>
                <div class="tilt-card p-7 rounded-3xl bg-white border border-slate-200 shadow-luxury hover:border-[#C5A059]/50 transition-all preserve-3d" data-tilt>
                    <div class="tilt-card-inner">
                        <div class="w-12 h-12 rounded-2xl bg-[#C5A059]/15 text-[#C5A059] flex items-center justify-center mb-4 border border-[#C5A059]/30">
                            <x-gold-icon name="home" size="lg" />
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-2">Cayman Islands</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Serving Grand Cayman, Cayman Brac & Little Cayman with stock & custom residential house plans, architectural modifications, turnkey general contracting, and property maintenance.
                        </p>
                    </div>
                </div>

                <div class="tilt-card p-7 rounded-3xl bg-white border border-slate-200 shadow-luxury hover:border-[#C5A059]/50 transition-all preserve-3d" data-tilt>
                    <div class="tilt-card-inner">
                        <div class="w-12 h-12 rounded-2xl bg-[#C5A059]/15 text-[#C5A059] flex items-center justify-center mb-4 border border-[#C5A059]/30">
                            <x-gold-icon name="hammer" size="lg" />
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-2">Florida, United States</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Certified General Contracting and Certified Plumbing services across commercial build-outs, residential renovations, leak detection, and property maintenance programs.
                        </p>
                    </div>
                </div>

                <div class="tilt-card p-7 rounded-3xl bg-white border border-slate-200 shadow-luxury hover:border-[#C5A059]/50 transition-all preserve-3d" data-tilt>
                    <div class="tilt-card-inner">
                        <div class="w-12 h-12 rounded-2xl bg-[#C5A059]/15 text-[#C5A059] flex items-center justify-center mb-4 border border-[#C5A059]/30">
                            <x-gold-icon name="tools" size="lg" />
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-2">Jamaica</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Reliable property improvement, facility maintenance, villa repairs, carpentry, plumbing diagnostics, and construction-related execution.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Dynamic Active Divisions from DB --}}
    <section class="py-16 bg-white border-t border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal-init">
            <h2 class="text-2xl font-bold text-slate-900 mb-8">Active Operational Divisions in EMAC ERP</h2>
            <div class="flex flex-wrap items-center justify-center gap-4" data-reveal-stagger>
                @foreach ($categories as $cat)
                    <div class="px-5 py-3 rounded-2xl bg-[#FAF8F4] border border-[#C5A059]/30 hover:border-[#C5A059] shadow-2xs hover:shadow-xs transition-all flex items-center gap-3 hover:-translate-y-1">
                        <div class="w-8 h-8 rounded-xl bg-white border border-[#C5A059]/30 text-[#C5A059] flex items-center justify-center shrink-0">
                            @if (str_contains(strtolower($cat->name), 'architect'))
                                <x-gold-icon name="architecture" size="xs" />
                            @elseif(str_contains(strtolower($cat->name), 'house plan') || str_contains(strtolower($cat->name), 'cayman'))
                                <x-gold-icon name="blueprint" size="xs" />
                            @elseif(str_contains(strtolower($cat->name), 'construction') || str_contains(strtolower($cat->name), 'contract'))
                                <x-gold-icon name="hammer" size="xs" />
                            @elseif(str_contains(strtolower($cat->name), 'plumb'))
                                <x-gold-icon name="wrench" size="xs" />
                            @else
                                <x-gold-icon name="tools" size="xs" />
                            @endif
                        </div>
                        <div class="text-left">
                            <div class="text-xs font-bold text-slate-900">{{ $cat->name }}</div>
                            <div class="text-[10px] text-[#8F6B20] font-semibold">{{ $cat->active_subcategories_count }} Trades Active</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</x-website.layout>
