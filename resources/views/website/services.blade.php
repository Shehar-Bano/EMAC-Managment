<x-website.layout :title="'Services & Solutions — EMAC Development, LLC.'">

    {{-- Services Hero Header --}}
    <section class="relative overflow-hidden py-14 lg:py-20 bg-gradient-to-b from-[#FAF8F4] via-white to-[#F5F1E8] border-b border-slate-200/80">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#C5A05912_1px,transparent_1px),linear-gradient(to_bottom,#C5A05912_1px,transparent_1px)] bg-[size:3.5rem_3.5rem] pointer-events-none"></div>
        <div class="absolute top-10 right-1/4 w-80 h-80 bg-[#C5A059]/10 rounded-full blur-3xl pointer-events-none animate-pulse-glow"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal-init">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#C5A059]/15 border border-[#C5A059]/35 text-[#8F6B20] text-xs font-bold mb-4 backdrop-blur-md shadow-2xs">
                <x-gold-icon name="tools" size="xs" class="text-[#C5A059]" />
                <span>COMPLETE TRADES & CAPABILITIES</span>
            </div>

            <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight">
                Our Services & Specialized Solutions
            </h1>

            <p class="mt-4 text-sm sm:text-base text-slate-600 max-w-2xl mx-auto leading-relaxed">
                From licensed general contracting and custom architectural blueprints to certified plumbing and property maintenance subscriptions across Florida, Jamaica, and the Cayman Islands.
            </p>
        </div>
    </section>

    {{-- Interactive Dynamic Categories Grid from Database --}}
    <section class="py-16 bg-white perspective-container" x-data="{ activeTab: 'all' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Category Filter Navigation Pills --}}
            <div class="flex flex-wrap items-center justify-center gap-2 mb-12 reveal-init">
                <button
                    type="button"
                    @click="activeTab = 'all'"
                    :class="activeTab === 'all' ? 'bg-[#C5A059] text-slate-950 font-bold shadow-xs scale-105' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                    class="px-4 py-2 text-xs font-semibold rounded-xl transition-all cursor-pointer"
                >
                    All Divisions ({{ $categories->count() }})
                </button>

                @foreach ($categories as $cat)
                    <button
                        type="button"
                        @click="activeTab = 'cat-{{ $cat->id }}'"
                        :class="activeTab === 'cat-{{ $cat->id }}' ? 'bg-[#C5A059] text-slate-950 font-bold shadow-xs scale-105' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                        class="px-4 py-2 text-xs font-semibold rounded-xl transition-all cursor-pointer flex items-center gap-1.5"
                    >
                        <span class="w-2 h-2 rounded-full bg-[#C5A059]"></span>
                        <span>{{ $cat->name }}</span>
                    </button>
                @endforeach
            </div>

            {{-- Categories List with 3D Tilt Panels --}}
            <div class="space-y-12">
                @foreach ($categories as $category)
                    <div
                        x-show="activeTab === 'all' || activeTab === 'cat-{{ $category->id }}'"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-3"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="tilt-card group p-8 sm:p-10 rounded-3xl bg-[#FAF8F4] border border-slate-200/90 shadow-luxury hover:border-[#C5A059]/60 transition-all preserve-3d"
                        data-tilt
                    >
                        <div class="card-glare"></div>

                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-slate-200/80 tilt-card-subtle">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-white border border-[#C5A059]/40 text-[#C5A059] flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform overflow-hidden">
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
                                <div>
                                    <div class="flex items-center gap-3">
                                        <h2 class="text-xl sm:text-2xl font-bold text-slate-900">{{ $category->name }}</h2>
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#C5A059]/20 text-[#8F6B20]">
                                            {{ $category->activeSubcategories->count() }} Trades
                                        </span>
                                    </div>
                                    <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-3xl leading-relaxed">
                                        {{ $category->description }}
                                    </p>
                                </div>
                            </div>

                            <a
                                href="{{ route('contact', ['category_id' => $category->id]) }}"
                                class="btn-premium px-5 py-2.5 text-xs font-bold text-slate-950 bg-gradient-to-r from-[#D4AF37] to-[#C5A059] rounded-xl shadow-sm shadow-[#C5A059]/20 shrink-0 self-start lg:self-auto"
                            >
                                Request Quote for {{ $category->name }} &rarr;
                            </a>
                        </div>

                        {{-- Subcategories Grid with Image / Gold Icons --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6 tilt-card-inner">
                            @forelse ($category->activeSubcategories as $sub)
                                <div class="p-4 rounded-2xl bg-white border border-slate-200/80 hover:border-[#C5A059]/50 hover:shadow-xs transition-all hover:-translate-y-1 group">
                                    <div class="w-9 h-9 rounded-xl bg-[#FAF8F4] border border-[#C5A059]/30 text-[#C5A059] flex items-center justify-center mb-2.5 group-hover:scale-110 group-hover:rotate-3 transition-transform overflow-hidden font-bold">
                                        @if ($sub->image_url)
                                            <img src="{{ $sub->image_url }}" alt="{{ $sub->name }}" class="w-full h-full object-cover">
                                        @elseif ($sub->icon)
                                            <span class="text-sm">{{ $sub->icon }}</span>
                                        @else
                                            <x-gold-icon name="check" size="xs" />
                                        @endif
                                    </div>
                                    <h3 class="text-xs font-bold text-slate-900 mb-1 group-hover:text-[#8F6B20] transition-colors">{{ $sub->name }}</h3>
                                    <p class="text-[11px] text-slate-500 leading-relaxed">{{ $sub->description }}</p>
                                </div>
                            @empty
                                <div class="col-span-4 text-center py-6 text-xs text-slate-400">
                                    Subcategory listings updated regularly.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Maintenance Subscriptions Callout with Glass Styling --}}
    <section class="py-16 bg-[#F4EFE6] border-t border-[#E5DFD3] relative overflow-hidden">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#C5A05915_1px,transparent_1px),linear-gradient(to_bottom,#C5A05915_1px,transparent_1px)] bg-[size:3.5rem_3.5rem] pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal-init">
            <span class="text-xs font-bold uppercase tracking-widest text-[#8F6B20]">Property Protection</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">
                Recurring Handyman & Property Maintenance Subscriptions
            </h2>
            <p class="text-xs sm:text-sm text-slate-600 max-w-2xl mx-auto mt-2">
                Ideal for Florida homeowners, Cayman residential landlords, and Jamaican vacation rental properties. Enjoy scheduled preventive visits, priority emergency dispatch, and fixed monthly rates.
            </p>
            <div class="mt-6 flex justify-center gap-4">
                <a href="{{ route('contact') }}" class="btn-premium px-6 py-3 text-xs font-bold text-slate-950 bg-gradient-to-r from-[#D4AF37] to-[#C5A059] rounded-xl shadow-md shadow-[#C5A059]/25">
                    Inquire About Maintenance Plans
                </a>
            </div>
        </div>
    </section>

</x-website.layout>
