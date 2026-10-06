
<x-website.layout :title="'Services, Capabilities & Regional Pricing — EMAC Development, LLC.'">

    {{-- Services Hero Header --}}
    <section class="bg-gradient-to-b from-white to-slate-50 border-b border-slate-200 py-14 lg:py-18">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-[#C5A059]/40 text-[#8F6B20] text-xs font-semibold mb-3">
                <span>Certified Trades & Transparent Pricing</span>
            </div>

            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                Our Services & Capabilities
            </h1>

            <p class="mt-3 text-sm sm:text-base text-slate-600 max-w-2xl mx-auto leading-relaxed">
                Explore our full range of professional handyman repairs, preventative property maintenance, and certified plumbing solutions with transparent regional pricing.
            </p>

            {{-- Dynamic Region Selector Bar --}}
            @if ($regions->isNotEmpty())
                <div class="mt-8 max-w-xl mx-auto bg-white p-2 rounded-2xl border border-slate-200 shadow-xs flex flex-wrap items-center justify-center gap-2">
                    <span class="text-xs font-bold text-slate-600 px-3 uppercase tracking-wider">Region / Market:</span>
                    @foreach ($regions as $r)
                        <a
                            href="{{ route('services', ['region_id' => $r->id]) }}"
                            class="px-4 py-2 text-xs font-bold rounded-xl transition-all {{ ($selectedRegion && $selectedRegion->id === $r->id) ? 'bg-[#C5A059] text-slate-950 shadow-xs' : 'bg-slate-50 text-slate-700 hover:bg-slate-100' }}"
                        >
                            {{ $r->name }} ({{ $r->currency }})
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Categories & Subcategories List --}}
    <section class="py-14 bg-slate-50/50" x-data="{ activeTab: 'all' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Category Filter Pills --}}
            <div class="flex flex-wrap items-center justify-center gap-2 mb-12">
                <button
                    type="button"
                    @click="activeTab = 'all'"
                    :class="activeTab === 'all' ? 'bg-[#C5A059] text-slate-950 font-extrabold shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                    class="px-5 py-2.5 text-xs font-bold rounded-xl transition-all cursor-pointer"
                >
                    All Services ({{ $categories->sum(fn($c) => $c->activeSubcategories->count()) }})
                </button>

                @foreach ($categories as $cat)
                    <button
                        type="button"
                        @click="activeTab = 'cat-{{ $cat->id }}'"
                        :class="activeTab === 'cat-{{ $cat->id }}' ? 'bg-[#C5A059] text-slate-950 font-extrabold shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                        class="px-5 py-2.5 text-xs font-bold rounded-xl transition-all cursor-pointer"
                    >
                        {{ $cat->name }} ({{ $cat->activeSubcategories->count() }})
                    </button>
                @endforeach
            </div>

            {{-- Categories Section --}}
            <div class="space-y-16">
                @foreach ($categories as $category)
                    <div
                        x-show="activeTab === 'all' || activeTab === 'cat-{{ $category->id }}'"
                        class="space-y-6"
                    >
                        {{-- 1. Category Luxury Banner Card (Light on Top-Right) --}}
                        <div class="relative rounded-3xl overflow-hidden bg-slate-950 border border-slate-800 shadow-xl group">
                            {{-- Background Image with Gradient Overlays (Light on Top-Right) --}}
                            @if ($category->image_url)
                                <img
                                    src="{{ $category->image_url }}"
                                    alt="{{ $category->name }} Cover"
                                    class="absolute inset-0 w-full h-full object-cover object-center transform scale-100 group-hover:scale-105 transition-transform duration-1000 ease-out"
                                >
                                {{-- Left gradient for crisp text readability, fading to transparent on the right --}}
                                <div class="absolute inset-0 bg-gradient-to-r from-slate-950/90 via-slate-950/40 to-transparent"></div>
                                {{-- Soft bottom gradient, leaving top-right fully bright and clear --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                            @else
                                <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-950 to-[#1f1a10]"></div>
                                <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-[#C5A059]/20 via-transparent to-transparent"></div>
                            @endif

                            {{-- Card Foreground Content --}}
                            <div class="relative z-10 p-6 sm:p-8 lg:p-10 flex flex-col md:flex-row md:items-end justify-between gap-6 min-h-[200px] sm:min-h-[220px]">
                                <div class="flex items-start sm:items-center gap-4 sm:gap-6">
                                    {{-- Category Icon Container --}}
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 p-3 shadow-2xl flex items-center justify-center shrink-0 ring-1 ring-[#C5A059]/40 group-hover:border-[#C5A059]/70 transition-colors">
                                        @if ($category->icon_url && (str_contains($category->icon_url, '/') || str_contains($category->icon_url, '.')))
                                            <img src="{{ $category->icon_url }}" alt="{{ $category->name }} Icon" class="w-full h-full object-contain">
                                        @elseif ($category->icon)
                                            <span class="text-3xl sm:text-4xl">{{ $category->icon }}</span>
                                        @else
                                            <svg class="w-9 h-9 sm:w-11 sm:h-11 text-[#E5C158]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        @endif
                                    </div>

                                    <div>
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-[#C5A059]/20 text-[#E5C158] border border-[#C5A059]/40 backdrop-blur-xs mb-2 shadow-xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>{{ $category->activeSubcategories->count() }} Available Services</span>
                                        </div>
                                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight drop-shadow-sm">{{ $category->name }}</h2>
                                        @if ($category->description)
                                            <p class="text-xs sm:text-sm text-slate-200 max-w-2xl mt-1.5 leading-relaxed line-clamp-2 drop-shadow-xs">{{ $category->description }}</p>
                                        @endif
                                    </div>
                                </div>

                                <a
                                    href="{{ route('contact', ['category_id' => $category->id, 'region_id' => $selectedRegion?->id]) }}"
                                    class="inline-flex items-center justify-center gap-2 px-6 py-3.5 text-xs sm:text-sm font-extrabold text-slate-950 bg-gradient-to-r from-[#C5A059] to-[#D4AF37] hover:from-[#d4af37] hover:to-[#e5c158] rounded-xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-0.5 shrink-0"
                                >
                                    <span>Request Category Quote</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            </div>
                        </div>

                        {{-- 2. Subcategories Detail Cards Grid --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            @forelse ($category->activeSubcategories as $sub)
                                @php
                                    $priceRecord = $sub->regionalServicePrices->first();
                                @endphp
                                <div class="bg-white rounded-2xl border border-slate-200/90 hover:border-[#C5A059] shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group/card">
                                    {{-- Subcategory Top Visual Area --}}
                                    @if ($sub->image_url)
                                        <div class="relative h-40 w-full overflow-hidden bg-slate-900">
                                            <img
                                                src="{{ $sub->image_url }}"
                                                alt="{{ $sub->name }}"
                                                class="w-full h-full object-cover group-hover/card:scale-108 transition-transform duration-700 ease-out"
                                            >
                                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/30 to-transparent"></div>

                                            {{-- Floating Subcategory Icon Badge --}}
                                            <div class="absolute bottom-3 left-3 w-11 h-11 rounded-xl bg-white/95 backdrop-blur-md p-2 shadow-lg border border-white/60 flex items-center justify-center shrink-0">
                                                @if ($sub->icon_url && (str_contains($sub->icon_url, '/') || str_contains($sub->icon_url, '.')))
                                                    <img src="{{ $sub->icon_url }}" alt="{{ $sub->name }} Icon" class="w-full h-full object-contain">
                                                @elseif ($sub->icon)
                                                    <span class="text-xl">{{ $sub->icon }}</span>
                                                @else
                                                    <svg class="w-5 h-5 text-[#8F6B20]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Card Content --}}
                                    <div class="p-5 flex-1 flex flex-col justify-between">
                                        <div>
                                            @if (!$sub->image_url)
                                                <div class="flex items-center gap-3 mb-3">
                                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#E5C158]/15 to-[#C5A059]/25 border border-[#C5A059]/30 p-2.5 flex items-center justify-center shrink-0 shadow-2xs">
                                                        @if ($sub->icon_url && (str_contains($sub->icon_url, '/') || str_contains($sub->icon_url, '.')))
                                                            <img src="{{ $sub->icon_url }}" alt="{{ $sub->name }} Icon" class="w-full h-full object-contain">
                                                        @elseif ($sub->icon)
                                                            <span class="text-2xl">{{ $sub->icon }}</span>
                                                        @else
                                                            <svg class="w-6 h-6 text-[#8F6B20]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                        @endif
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <h3 class="text-sm font-bold text-slate-900 group-hover/card:text-[#8F6B20] transition-colors truncate">{{ $sub->name }}</h3>
                                                        <span class="text-[10px] font-bold text-[#8F6B20] uppercase tracking-wider">Certified Trade</span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="mb-2">
                                                    <h3 class="text-sm font-bold text-slate-900 group-hover/card:text-[#8F6B20] transition-colors">{{ $sub->name }}</h3>
                                                </div>
                                            @endif

                                            <p class="text-xs text-slate-500 leading-relaxed line-clamp-2">{{ $sub->description ?: 'Certified specialist maintenance and professional installation service.' }}</p>
                                        </div>

                                        {{-- Pricing & CTA Row --}}
                                        <div class="mt-5 pt-3.5 border-t border-slate-100 flex items-center justify-between gap-2">
                                            <div>
                                                @if ($priceRecord)
                                                    <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Standard Rate ({{ $priceRecord->region?->name ?? $selectedRegion?->name }})</div>
                                                    <div class="text-sm font-extrabold text-slate-900 flex items-baseline gap-1 mt-0.5">
                                                        <span class="text-xs text-[#8F6B20] font-bold">{{ $priceRecord->currency }}</span>
                                                        <span class="text-base font-black text-[#8F6B20]">{{ number_format((float) $priceRecord->price, 2) }}</span>
                                                    </div>
                                                @else
                                                    <div class="text-[10px] text-slate-400 font-semibold uppercase">Pricing</div>
                                                    <div class="text-xs font-bold text-slate-700 mt-0.5">Custom Estimate</div>
                                                @endif
                                            </div>

                                            <a
                                                href="{{ route('contact', ['category_id' => $category->id, 'subcategory_id' => $sub->id, 'region_id' => $selectedRegion?->id]) }}"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-slate-900 bg-amber-50 hover:bg-[#C5A059] border border-[#C5A059]/40 hover:border-transparent rounded-xl transition-all duration-200 group/btn"
                                            >
                                                <span>Book Quote</span>
                                                <svg class="w-3.5 h-3.5 group-hover/btn:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-3 text-center py-10 bg-white rounded-2xl border border-slate-200 text-xs text-slate-400">
                                    No active services currently listed under this category.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</x-website.layout>

