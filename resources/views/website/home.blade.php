<x-website.layout :title="'EMAC Development, LLC. | Construction, Architectural Design, Plumbing & House Plans'">

    {{-- Hero Section --}}
    <section class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-32 bg-gradient-to-b from-[#FAF8F4] via-white to-[#F5F1E8] text-slate-900 perspective-container">
        {{-- Animated Blueprint Grid & Ambient Glowing Orbs --}}
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#C5A05915_1px,transparent_1px),linear-gradient(to_bottom,#C5A05915_1px,transparent_1px)] blueprint-grid-animated [mask-image:radial-gradient(ellipse_70%_60%_at_50%_20%,#000_75%,transparent_100%)] pointer-events-none"></div>
        <div data-parallax="0.08" class="absolute -top-24 left-1/4 w-96 h-96 bg-[#C5A059]/15 rounded-full blur-3xl pointer-events-none animate-pulse-glow"></div>
        <div data-parallax="-0.06" class="absolute top-1/3 right-10 w-80 h-80 bg-[#D4AF37]/10 rounded-full blur-3xl pointer-events-none animate-float-reverse"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                {{-- Left: Text & CTA Column --}}
                <div class="lg:col-span-7 text-left">
                    {{-- Top Regional Badge (Animated entrance) --}}
                    <div class="reveal-init inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#C5A059]/15 border border-[#C5A059]/35 text-[#8F6B20] text-xs font-bold mb-6 backdrop-blur-md shadow-2xs">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#C5A059] animate-pulse"></span>
                        <span class="tracking-wide uppercase font-mono text-[11px]">FLORIDA • JAMAICA • CAYMAN ISLANDS</span>
                    </div>

                    {{-- Main Headline --}}
                    <h1 class="reveal-init text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 leading-[1.15]">
                        Complete Construction, Architectural Design & <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#D4AF37] via-[#C5A059] to-[#8F6B20]">Property Services</span>
                    </h1>

                    {{-- Subtitle --}}
                    <p class="reveal-init mt-5 text-base sm:text-lg text-slate-600 max-w-2xl leading-relaxed font-normal">
                        EMAC Development, LLC. combines certified general contracting, bespoke architectural house plans, licensed plumbing, and recurring property maintenance under one unified multi-regional platform.
                    </p>

                    {{-- Action Buttons --}}
                    <div class="reveal-init mt-8 flex flex-wrap items-center gap-4">
                        <a href="{{ route('contact') }}" class="btn-premium inline-flex items-center gap-2 px-7 py-3.5 text-sm font-bold text-slate-950 bg-gradient-to-r from-[#D4AF37] via-[#C5A059] to-[#B8903B] rounded-xl shadow-lg shadow-[#C5A059]/30">
                            <span>Request Service & Instant Quote</span>
                            <svg class="w-4 h-4 arrow-slide" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                        <a href="{{ route('services') }}#house-plans" class="inline-flex items-center gap-2 px-7 py-3.5 text-sm font-semibold text-slate-800 hover:text-slate-950 bg-white/90 hover:bg-white border border-slate-300 hover:border-[#C5A059] rounded-xl shadow-xs transition-all hover:-translate-y-0.5">
                            <x-gold-icon name="blueprint" size="sm" class="text-[#C5A059]" />
                            <span>Browse Cayman House Plans</span>
                            <svg class="w-4 h-4 text-[#8F6B20]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>

                </div>

                {{-- Right: 3D Interactive Showcase Card --}}
                <div class="lg:col-span-5 relative">
                    {{-- Decorative 3D Ambient Blur Circle --}}
                    <div class="absolute -inset-2 bg-gradient-to-tr from-[#C5A059]/30 to-[#D4AF37]/20 rounded-3xl blur-2xl opacity-70"></div>

                    {{-- Main 3D Card --}}
                    <div class="tilt-card relative rounded-3xl bg-white p-7 border border-[#C5A059]/40 shadow-luxury preserve-3d" data-tilt data-tilt-max="7">
                        <div class="card-glare"></div>

                        {{-- Card Header --}}
                        <div class="flex items-center justify-between pb-5 border-b border-slate-100 tilt-card-subtle">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-[#C5A059]/15 border border-[#C5A059]/35 text-[#C5A059] flex items-center justify-center shadow-2xs">
                                    <x-gold-icon name="architecture" size="md" />
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-900">EMAC Multi-Trade Hub</div>
                                    <div class="text-[10px] text-slate-500 font-mono">Turnkey Architectural & Trade Ops</div>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Active</span>
                            </span>
                        </div>

                        {{-- Isometric Blueprint & Architectural Rendering Visual Mockup --}}
                        <div class="my-5 rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-950 p-5 text-white relative overflow-hidden border border-[#C5A059]/30 tilt-card-inner shadow-md">
                            <div class="absolute inset-0 bg-[linear-gradient(to_right,#C5A05915_1px,transparent_1px),linear-gradient(to_bottom,#C5A05915_1px,transparent_1px)] bg-[size:1.25rem_1.25rem]"></div>
                            
                            <div class="relative flex items-center justify-between text-[11px] text-[#D4AF37] font-mono mb-3">
                                <span class="flex items-center gap-1.5">
                                    <x-gold-icon name="ruler" size="xs" class="text-[#C5A059]" />
                                    <span>CAYMAN ARCHITECTURAL BLUEPRINT</span>
                                </span>
                                <span class="text-slate-400">LOT-702-KY</span>
                            </div>

                            <div class="relative space-y-2 text-xs">
                                <div class="h-2 rounded bg-slate-700/80 w-3/4"></div>
                                <div class="h-2 rounded bg-slate-700/60 w-1/2"></div>
                                <div class="grid grid-cols-3 gap-2 pt-2">
                                    <div class="p-2 rounded-lg bg-slate-800/90 border border-[#C5A059]/30 text-center">
                                        <div class="text-[10px] text-slate-400">Living Area</div>
                                        <div class="font-bold text-[#D4AF37]">3,450 SQ.FT</div>
                                    </div>
                                    <div class="p-2 rounded-lg bg-slate-800/90 border border-[#C5A059]/30 text-center">
                                        <div class="text-[10px] text-slate-400">Wind Rating</div>
                                        <div class="font-bold text-emerald-400">155+ MPH</div>
                                    </div>
                                    <div class="p-2 rounded-lg bg-slate-800/90 border border-[#C5A059]/30 text-center">
                                        <div class="text-[10px] text-slate-400">Permit Status</div>
                                        <div class="font-bold text-[#C5A059]">Compliant</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Floating Badges Composition with Gold Icons --}}
                        <div class="space-y-2.5 tilt-card-deep">
                            <div class="p-3 rounded-xl bg-[#FAF8F4] border border-[#C5A059]/30 flex items-center justify-between shadow-2xs hover:bg-[#FAF6EC] transition-colors">
                                <div class="flex items-center gap-2.5 text-xs font-bold text-slate-800">
                                    <span class="w-7 h-7 rounded-lg bg-[#C5A059]/20 text-[#C5A059] flex items-center justify-center">
                                        <x-gold-icon name="shield" size="xs" />
                                    </span>
                                    <span>Certified General Contractor (CGC)</span>
                                </div>
                                <span class="text-[10px] font-mono font-bold text-[#8F6B20]">Verified</span>
                            </div>

                            <div class="p-3 rounded-xl bg-[#FAF8F4] border border-[#C5A059]/30 flex items-center justify-between shadow-2xs hover:bg-[#FAF6EC] transition-colors">
                                <div class="flex items-center gap-2.5 text-xs font-bold text-slate-800">
                                    <span class="w-7 h-7 rounded-lg bg-[#C5A059]/20 text-[#C5A059] flex items-center justify-center">
                                        <x-gold-icon name="wrench" size="xs" />
                                    </span>
                                    <span>Certified Plumbing Contractor (CPC)</span>
                                </div>
                                <span class="text-[10px] font-mono font-bold text-[#8F6B20]">State-Licensed</span>
                            </div>
                        </div>

                        {{-- Mini Floating Card (Top Right Offset) --}}
                        <div class="absolute -top-4 -right-4 px-3.5 py-2 rounded-xl bg-slate-900 text-white border border-[#C5A059]/50 shadow-xl text-[11px] font-bold flex items-center gap-2 animate-float-slow">
                            <x-gold-icon name="home" size="xs" class="text-[#C5A059]" />
                            <span>Cayman House Plans</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Interactive Regional Market Switcher Section --}}
    <section class="py-16 bg-[#FAF8F5] border-t border-slate-200/80 perspective-container" x-data="{ activeMarket: 'cayman' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-10 reveal-init">
                <span class="text-xs font-bold uppercase tracking-widest text-[#8F6B20]">Geographic Operations</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1.5 tracking-tight">
                    Tailored Services by Regional Territory
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 mt-2">
                    EMAC delivers specialized construction, design, and trade capabilities customized to local zoning, climate, and regulatory standards.
                </p>

                {{-- Market Selection Tabs --}}
                <div class="mt-6 inline-flex p-1.5 rounded-2xl bg-white border border-slate-200 shadow-2xs">
                    <button
                        type="button"
                        @click="activeMarket = 'cayman'"
                        :class="activeMarket === 'cayman' ? 'bg-[#C5A059] text-slate-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="px-4 py-2 text-xs font-semibold rounded-xl transition-all cursor-pointer flex items-center gap-1.5"
                    >
                        <x-gold-icon name="home" size="xs" />
                        <span>Cayman Islands</span>
                    </button>
                    <button
                        type="button"
                        @click="activeMarket = 'florida'"
                        :class="activeMarket === 'florida' ? 'bg-[#C5A059] text-slate-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="px-4 py-2 text-xs font-semibold rounded-xl transition-all cursor-pointer flex items-center gap-1.5"
                    >
                        <x-gold-icon name="hammer" size="xs" />
                        <span>Florida</span>
                    </button>
                    <button
                        type="button"
                        @click="activeMarket = 'jamaica'"
                        :class="activeMarket === 'jamaica' ? 'bg-[#C5A059] text-slate-950 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="px-4 py-2 text-xs font-semibold rounded-xl transition-all cursor-pointer flex items-center gap-1.5"
                    >
                        <x-gold-icon name="tools" size="xs" />
                        <span>Jamaica</span>
                    </button>
                </div>
            </div>

            {{-- Market 1: Cayman Islands --}}
            <div x-show="activeMarket === 'cayman'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="tilt-card bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-luxury" data-tilt>
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 pb-6 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#C5A059]/15 text-[#C5A059] flex items-center justify-center border border-[#C5A059]/30">
                                <x-gold-icon name="home" size="md" />
                            </div>
                            <h3 class="text-xl font-bold text-slate-900">Cayman Islands Service Portfolio</h3>
                        </div>
                        <p class="text-xs text-slate-600 mt-2 max-w-2xl leading-relaxed">
                            Grand Cayman, Cayman Brac & Little Cayman — Specialized architectural house plan sales, bespoke custom home design, licensed building contracting, certified plumbing, and property maintenance.
                        </p>
                    </div>
                    <a href="{{ route('contact', ['market' => 'Cayman Islands']) }}" class="btn-premium px-5 py-2.5 text-xs font-bold text-slate-950 bg-gradient-to-r from-[#D4AF37] to-[#C5A059] rounded-xl shrink-0 shadow-2xs">
                        Inquire in Cayman &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                    <div class="p-5 rounded-2xl bg-[#FAF8F4] border border-[#C5A059]/25 hover:border-[#C5A059]/60 hover:shadow-xs transition-all hover:-translate-y-1 group">
                        <div class="w-9 h-9 rounded-xl bg-white border border-[#C5A059]/30 text-[#C5A059] flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <x-gold-icon name="blueprint" size="sm" />
                        </div>
                        <div class="text-sm font-bold text-slate-900">House Plan Catalog</div>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">Ready-to-build residential blueprints optimized for Caribbean living & building codes.</p>
                    </div>

                    <div class="p-5 rounded-2xl bg-[#FAF8F4] border border-[#C5A059]/25 hover:border-[#C5A059]/60 hover:shadow-xs transition-all hover:-translate-y-1 group">
                        <div class="w-9 h-9 rounded-xl bg-white border border-[#C5A059]/30 text-[#C5A059] flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <x-gold-icon name="architecture" size="sm" />
                        </div>
                        <div class="text-sm font-bold text-slate-900">Architectural Design</div>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">Custom estate design, schematic floor plans, 3D renderings, and planning submissions.</p>
                    </div>

                    <div class="p-5 rounded-2xl bg-[#FAF8F4] border border-[#C5A059]/25 hover:border-[#C5A059]/60 hover:shadow-xs transition-all hover:-translate-y-1 group">
                        <div class="w-9 h-9 rounded-xl bg-white border border-[#C5A059]/30 text-[#C5A059] flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <x-gold-icon name="hammer" size="sm" />
                        </div>
                        <div class="text-sm font-bold text-slate-900">Construction & Trades</div>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">New residential builds, luxury renovations, structural concrete, and site supervision.</p>
                    </div>
                </div>
            </div>

            {{-- Market 2: Florida --}}
            <div x-show="activeMarket === 'florida'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="tilt-card bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-luxury" data-tilt style="display: none;">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 pb-6 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#C5A059]/15 text-[#C5A059] flex items-center justify-center border border-[#C5A059]/30">
                                <x-gold-icon name="hammer" size="md" />
                            </div>
                            <h3 class="text-xl font-bold text-slate-900">Florida Service Portfolio</h3>
                        </div>
                        <p class="text-xs text-slate-600 mt-2 max-w-2xl leading-relaxed">
                            State-certified General Contracting & certified Plumbing Contractor capabilities. Residential remodels, emergency plumbing repairs, commercial build-outs, and ongoing maintenance.
                        </p>
                    </div>
                    <a href="{{ route('contact', ['market' => 'Florida']) }}" class="btn-premium px-5 py-2.5 text-xs font-bold text-slate-950 bg-gradient-to-r from-[#D4AF37] to-[#C5A059] rounded-xl shrink-0 shadow-2xs">
                        Inquire in Florida &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                    <div class="p-5 rounded-2xl bg-[#FAF8F4] border border-[#C5A059]/25 hover:border-[#C5A059]/60 hover:shadow-xs transition-all hover:-translate-y-1 group">
                        <div class="w-9 h-9 rounded-xl bg-white border border-[#C5A059]/30 text-[#C5A059] flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <x-gold-icon name="construction" size="sm" />
                        </div>
                        <div class="text-sm font-bold text-slate-900">General Contracting</div>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">Full construction management, home additions, and certified structural builds.</p>
                    </div>

                    <div class="p-5 rounded-2xl bg-[#FAF8F4] border border-[#C5A059]/25 hover:border-[#C5A059]/60 hover:shadow-xs transition-all hover:-translate-y-1 group">
                        <div class="w-9 h-9 rounded-xl bg-white border border-[#C5A059]/30 text-[#C5A059] flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <x-gold-icon name="wrench" size="sm" />
                        </div>
                        <div class="text-sm font-bold text-slate-900">Certified Plumbing</div>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">Water heaters, leak detection, hydro-jetting, and full repiping services.</p>
                    </div>

                    <div class="p-5 rounded-2xl bg-[#FAF8F4] border border-[#C5A059]/25 hover:border-[#C5A059]/60 hover:shadow-xs transition-all hover:-translate-y-1 group">
                        <div class="w-9 h-9 rounded-xl bg-white border border-[#C5A059]/30 text-[#C5A059] flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <x-gold-icon name="tools" size="sm" />
                        </div>
                        <div class="text-sm font-bold text-slate-900">Handyman & Repairs</div>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">Scheduled maintenance plans, drywall, painting, and rapid property repairs.</p>
                    </div>
                </div>
            </div>

            {{-- Market 3: Jamaica --}}
            <div x-show="activeMarket === 'jamaica'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="tilt-card bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-luxury" data-tilt style="display: none;">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 pb-6 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#C5A059]/15 text-[#C5A059] flex items-center justify-center border border-[#C5A059]/30">
                                <x-gold-icon name="tools" size="md" />
                            </div>
                            <h3 class="text-xl font-bold text-slate-900">Jamaica Service Portfolio</h3>
                        </div>
                        <p class="text-xs text-slate-600 mt-2 max-w-2xl leading-relaxed">
                            Property improvements, professional handyman services, facility maintenance, renovations, and construction-related execution across residential and commercial properties.
                        </p>
                    </div>
                    <a href="{{ route('contact', ['market' => 'Jamaica']) }}" class="btn-premium px-5 py-2.5 text-xs font-bold text-slate-950 bg-gradient-to-r from-[#D4AF37] to-[#C5A059] rounded-xl shrink-0 shadow-2xs">
                        Inquire in Jamaica &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                    <div class="p-5 rounded-2xl bg-[#FAF8F4] border border-[#C5A059]/25 hover:border-[#C5A059]/60 hover:shadow-xs transition-all hover:-translate-y-1 group">
                        <div class="w-9 h-9 rounded-xl bg-white border border-[#C5A059]/30 text-[#C5A059] flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <x-gold-icon name="maintenance" size="sm" />
                        </div>
                        <div class="text-sm font-bold text-slate-900">Property Maintenance</div>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">Routine inspection, scheduled upkeep, and rapid troubleshooting for properties.</p>
                    </div>

                    <div class="p-5 rounded-2xl bg-[#FAF8F4] border border-[#C5A059]/25 hover:border-[#C5A059]/60 hover:shadow-xs transition-all hover:-translate-y-1 group">
                        <div class="w-9 h-9 rounded-xl bg-white border border-[#C5A059]/30 text-[#C5A059] flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <x-gold-icon name="hammer" size="sm" />
                        </div>
                        <div class="text-sm font-bold text-slate-900">Construction & Renovations</div>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">Villa renovations, kitchen & bathroom refurbishments, and roof/masonry repairs.</p>
                    </div>

                    <div class="p-5 rounded-2xl bg-[#FAF8F4] border border-[#C5A059]/25 hover:border-[#C5A059]/60 hover:shadow-xs transition-all hover:-translate-y-1 group">
                        <div class="w-9 h-9 rounded-xl bg-white border border-[#C5A059]/30 text-[#C5A059] flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <x-gold-icon name="tools" size="sm" />
                        </div>
                        <div class="text-sm font-bold text-slate-900">Handyman Services</div>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">Carpentry, painting, plumbing fixtures, electrical repairs, and fixture installations.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Dynamic Core Service Divisions (From Database with 3D Tilt Cards) --}}
    <section class="py-20 bg-white border-t border-slate-200/80 perspective-container">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-14 reveal-init">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-[#8F6B20]">Our Divisions</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-1.5 tracking-tight">
                        Comprehensive Design & Construction Capabilities
                    </h2>
                    <p class="text-sm text-slate-600 mt-2 max-w-2xl">
                        Explore our core service divisions directly managed in our ERP database. Click any category to view specialized trades.
                    </p>
                </div>
                <a href="{{ route('services') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#8F6B20] hover:text-[#C5A059] transition-colors shrink-0 group">
                    <span>View All Services & Trades</span>
                    <svg class="w-4 h-4 arrow-slide" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>

            {{-- Categories Loop from DB with Gold Icon Containers --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" data-reveal-stagger>
                @foreach ($categories as $category)
                    <div class="tilt-card p-7 rounded-3xl bg-[#FAF8F4] border border-slate-200/90 hover:border-[#C5A059] shadow-2xs hover:shadow-luxury transition-all group flex flex-col justify-between preserve-3d" data-tilt>
                        <div class="card-glare"></div>

                        <div class="tilt-card-inner">
                            <div class="w-14 h-14 rounded-2xl bg-white border border-[#C5A059]/40 text-[#C5A059] flex items-center justify-center text-2xl mb-5 group-hover:scale-110 group-hover:rotate-3 transition-transform shadow-2xs overflow-hidden">
                                @if ($category->image_url)
                                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                                @elseif (str_contains(strtolower($category->name), 'architect'))
                                    <x-gold-icon name="architecture" size="lg" />
                                @elseif(str_contains(strtolower($category->name), 'house plan') || str_contains(strtolower($category->name), 'cayman'))
                                    <x-gold-icon name="blueprint" size="lg" />
                                @elseif(str_contains(strtolower($category->name), 'construction') || str_contains(strtolower($category->name), 'contract'))
                                    <x-gold-icon name="hammer" size="lg" />
                                @elseif(str_contains(strtolower($category->name), 'plumb'))
                                    <x-gold-icon name="wrench" size="lg" />
                                @elseif(str_contains(strtolower($category->name), 'handyman') || str_contains(strtolower($category->name), 'maintenance'))
                                    <x-gold-icon name="tools" size="lg" />
                                @else
                                    <x-gold-icon name="star" size="lg" />
                                @endif
                            </div>

                            <h3 class="text-lg font-bold text-slate-900 mb-2 group-hover:text-[#8F6B20] transition-colors">
                                {{ $category->name }}
                            </h3>

                            <p class="text-xs text-slate-600 leading-relaxed mb-4">
                                {{ $category->description }}
                            </p>

                            {{-- Subcategories pills --}}
                            <div class="space-y-1.5 pt-3 border-t border-slate-200/60">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Key Trades Included:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($category->activeSubcategories as $sub)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-white border border-slate-200 text-slate-700 hover:border-[#C5A059]/40 transition-colors">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#C5A059]"></span>
                                            <span>{{ $sub->name }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-200/60 flex items-center justify-between tilt-card-subtle">
                            <span class="text-[11px] font-bold text-[#8F6B20] font-mono">{{ $category->activeSubcategories->count() }} Available Services</span>
                            <a href="{{ route('contact', ['category_id' => $category->id]) }}" class="text-xs font-bold text-slate-900 group-hover:text-[#C5A059] inline-flex items-center gap-1">
                                <span>Get Quote</span>
                                <span class="arrow-slide">&rarr;</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Cayman House Plans Highlight Section (3D Dark Architectural Showcase) --}}
    <section id="house-plans" class="py-20 bg-gradient-to-b from-[#FAF8F5] to-white border-t border-slate-200/80 perspective-container">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="tilt-card p-8 sm:p-12 rounded-3xl bg-[#0f172a] text-white relative overflow-hidden shadow-2xl border border-[#C5A059]/40 preserve-3d" data-tilt data-tilt-max="5">
                <div class="card-glare"></div>
                <div class="absolute -right-16 -top-16 w-96 h-96 rounded-full bg-[#C5A059]/20 blur-3xl pointer-events-none animate-pulse-glow"></div>
                <div class="absolute inset-0 bg-[linear-gradient(to_right,#C5A05912_1px,transparent_1px),linear-gradient(to_bottom,#C5A05912_1px,transparent_1px)] bg-[size:2.5rem_2.5rem] pointer-events-none"></div>

                <div class="relative grid grid-cols-1 lg:grid-cols-12 gap-8 items-center tilt-card-inner">
                    <div class="lg:col-span-7">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-[#C5A059]/20 border border-[#C5A059]/40 text-[#D4AF37] text-xs font-bold mb-4">
                            <x-gold-icon name="home" size="xs" class="text-[#C5A059]" />
                            <span>CAYMAN ISLANDS STORE</span>
                        </div>

                        <h2 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                            Ready-to-Build Cayman Residential House Plans
                        </h2>

                        <p class="text-xs sm:text-sm text-slate-300 mt-4 leading-relaxed">
                            Purchase ready architectural blueprints designed specifically for Cayman Islands climate, setback requirements, hurricane resistance, and Caribbean indoor-outdoor flow. Need alterations? Submit custom modification requests for bedroom additions, kitchen remodeling, or lot adapting.
                        </p>

                        <div class="mt-8 flex flex-wrap items-center gap-4">
                            <a href="{{ route('services') }}#house-plans" class="btn-premium px-6 py-3 text-xs font-bold text-slate-950 bg-gradient-to-r from-[#D4AF37] to-[#C5A059] rounded-xl shadow-md shadow-[#C5A059]/30">
                                Explore House Plan Collection
                            </a>
                            <a href="{{ route('contact', ['market' => 'Cayman Islands']) }}" class="px-6 py-3 text-xs font-bold text-white bg-slate-800/90 hover:bg-slate-800 border border-slate-600 hover:border-[#C5A059]/60 rounded-xl transition-colors">
                                Request Custom Plan Modification
                            </a>
                        </div>
                    </div>

                    {{-- Isometric Specs Visual Box with Gold Icons --}}
                    <div class="lg:col-span-5 grid grid-cols-2 gap-3 tilt-card-deep">
                        <div class="p-4 rounded-2xl bg-slate-800/80 border border-[#C5A059]/30 backdrop-blur-md">
                            <div class="w-8 h-8 rounded-lg bg-[#C5A059]/20 text-[#C5A059] flex items-center justify-center mb-2">
                                <x-gold-icon name="ruler" size="sm" />
                            </div>
                            <div class="text-xs font-bold text-white">Cayman Planning Approved</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Compliant setback dimensions & roof pitches</div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-800/80 border border-[#C5A059]/30 backdrop-blur-md">
                            <div class="w-8 h-8 rounded-lg bg-[#C5A059]/20 text-[#C5A059] flex items-center justify-center mb-2">
                                <x-gold-icon name="hurricane" size="sm" />
                            </div>
                            <div class="text-xs font-bold text-white">Hurricane Resilient</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Engineered for 150+ MPH tropical wind loads</div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-800/80 border border-[#C5A059]/30 backdrop-blur-md">
                            <div class="w-8 h-8 rounded-lg bg-[#C5A059]/20 text-[#C5A059] flex items-center justify-center mb-2">
                                <x-gold-icon name="sun" size="sm" />
                            </div>
                            <div class="text-xs font-bold text-white">Thermal Efficiency</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Passive Caribbean cross-ventilation design</div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-800/80 border border-[#C5A059]/30 backdrop-blur-md">
                            <div class="w-8 h-8 rounded-lg bg-[#C5A059]/20 text-[#C5A059] flex items-center justify-center mb-2">
                                <x-gold-icon name="edit" size="sm" />
                            </div>
                            <div class="text-xs font-bold text-white">Custom Modifiable</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Tailored room expansions for your parcel</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- How EMAC Service Request Flow Works --}}
    <section class="py-20 bg-white border-t border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 reveal-init">
                <span class="text-xs font-bold uppercase tracking-widest text-[#8F6B20]">Service Delivery Workflow</span>
                <h2 class="text-3xl font-extrabold text-slate-900 mt-1 tracking-tight">
                    From Online Request to Job Completion
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 mt-2">
                    How homeowners, property managers, and commercial clients interact with EMAC.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6" data-reveal-stagger>
                <div class="p-6 rounded-2xl bg-[#FAF8F4] border border-slate-200/80 text-center hover:border-[#C5A059]/60 hover:shadow-luxury transition-all hover:-translate-y-1.5 group">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#C5A059] to-[#D4AF37] text-slate-950 mx-auto flex items-center justify-center mb-4 shadow-sm group-hover:scale-110 transition-transform">
                        <x-gold-icon name="blueprint" size="md" class="text-slate-950" />
                    </div>
                    <h3 class="font-bold text-sm text-slate-900 mb-1">1. Select Service</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">Choose your trade, describe the issue, and upload photos.</p>
                </div>

                <div class="p-6 rounded-2xl bg-[#FAF8F4] border border-slate-200/80 text-center hover:border-[#C5A059]/60 hover:shadow-luxury transition-all hover:-translate-y-1.5 group">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#C5A059] to-[#D4AF37] text-slate-950 mx-auto flex items-center justify-center mb-4 shadow-sm group-hover:scale-110 transition-transform">
                        <x-gold-icon name="quote" size="md" class="text-slate-950" />
                    </div>
                    <h3 class="font-bold text-sm text-slate-900 mb-1">2. Receive Quote</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">Review transparent breakdown of labor, materials, and timeline.</p>
                </div>

                <div class="p-6 rounded-2xl bg-[#FAF8F4] border border-slate-200/80 text-center hover:border-[#C5A059]/60 hover:shadow-luxury transition-all hover:-translate-y-1.5 group">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#C5A059] to-[#D4AF37] text-slate-950 mx-auto flex items-center justify-center mb-4 shadow-sm group-hover:scale-110 transition-transform">
                        <x-gold-icon name="calendar" size="md" class="text-slate-950" />
                    </div>
                    <h3 class="font-bold text-sm text-slate-900 mb-1">3. Schedule Job</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">Book convenient appointment slot and track assigned technician.</p>
                </div>

                <div class="p-6 rounded-2xl bg-[#FAF8F4] border border-slate-200/80 text-center hover:border-[#C5A059]/60 hover:shadow-luxury transition-all hover:-translate-y-1.5 group">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#C5A059] to-[#D4AF37] text-slate-950 mx-auto flex items-center justify-center mb-4 shadow-sm group-hover:scale-110 transition-transform">
                        <x-gold-icon name="payment" size="md" class="text-slate-950" />
                    </div>
                    <h3 class="font-bold text-sm text-slate-900 mb-1">4. Receive & Pay</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">Inspect work, review digital invoice, and complete secure online payment.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Bottom CTA with Luxury Gradient & Micro-interactions --}}
    <section class="bg-gradient-to-r from-[#F4EFE6] via-[#EAE2D0] to-[#F4EFE6] text-slate-900 py-16 border-t border-[#E5DFD3] relative overflow-hidden">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#C5A05915_1px,transparent_1px),linear-gradient(to_bottom,#C5A05915_1px,transparent_1px)] bg-[size:3rem_3rem] pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal-init">
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">Ready to Begin Your Project or Schedule Service?</h2>
            <p class="mt-2 text-xs sm:text-sm text-slate-600 max-w-xl mx-auto">
                Submit an online consultation request or reach our regional coordination team directly.
            </p>
            <div class="mt-6 flex flex-wrap justify-center gap-4">
                <a href="{{ route('contact') }}" class="btn-premium px-6 py-3 text-xs font-bold text-slate-950 bg-gradient-to-r from-[#D4AF37] via-[#C5A059] to-[#B8903B] rounded-xl shadow-md shadow-[#C5A059]/30">
                    Start Service Request
                </a>
                <a href="tel:+18005550199" class="px-6 py-3 text-xs font-bold bg-white/90 border border-[#C5A059]/40 text-[#8F6B20] hover:bg-white rounded-xl transition-colors shadow-2xs flex items-center gap-2">
                    <x-gold-icon name="phone" size="xs" class="text-[#C5A059]" />
                    <span>Call: +1 (800) 555-0199</span>
                </a>
            </div>
        </div>
    </section>

</x-website.layout>
