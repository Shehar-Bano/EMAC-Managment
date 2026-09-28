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
    <section class="py-14 bg-white" x-data="{ activeTab: 'all' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Category Filter Pills --}}
            <div class="flex flex-wrap items-center justify-center gap-2 mb-10">
                <button
                    type="button"
                    @click="activeTab = 'all'"
                    :class="activeTab === 'all' ? 'bg-[#C5A059] text-slate-900 font-bold shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                    class="px-4 py-2 text-xs font-semibold rounded-lg transition-colors cursor-pointer"
                >
                    All Services ({{ $categories->sum(fn($c) => $c->activeSubcategories->count()) }})
                </button>

                @foreach ($categories as $cat)
                    <button
                        type="button"
                        @click="activeTab = 'cat-{{ $cat->id }}'"
                        :class="activeTab === 'cat-{{ $cat->id }}' ? 'bg-[#C5A059] text-slate-900 font-bold shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                        class="px-4 py-2 text-xs font-semibold rounded-lg transition-colors cursor-pointer"
                    >
                        {{ $cat->name }} ({{ $cat->activeSubcategories->count() }})
                    </button>
                @endforeach
            </div>

            {{-- Categories Section --}}
            <div class="space-y-12">
                @foreach ($categories as $category)
                    <div
                        x-show="activeTab === 'all' || activeTab === 'cat-{{ $category->id }}'"
                        class="bg-slate-50 rounded-2xl border border-slate-200 p-6 sm:p-8"
                    >
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
                            <div class="flex items-center gap-3.5">
                                <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 text-[#C5A059] flex items-center justify-center text-2xl shrink-0 shadow-2xs overflow-hidden">
                                    @if ($category->image_url)
                                        <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                                    @elseif ($category->icon)
                                        <span>{{ $category->icon }}</span>
                                    @else
                                        <span>🛠️</span>
                                    @endif
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-slate-900">{{ $category->name }}</h2>
                                    <p class="text-xs text-slate-600 mt-0.5">{{ $category->description }}</p>
                                </div>
                            </div>

                            <a
                                href="{{ route('contact', ['category_id' => $category->id, 'region_id' => $selectedRegion?->id]) }}"
                                class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-slate-900 bg-[#C5A059] hover:bg-[#b8934b] rounded-lg transition-colors shrink-0"
                            >
                                Request Category Quote &rarr;
                            </a>
                        </div>

                        {{-- Subcategories Grid --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                            @forelse ($category->activeSubcategories as $sub)
                                @php
                                    $priceRecord = $sub->regionalServicePrices->first();
                                @endphp
                                <div class="bg-white p-4 rounded-xl border border-slate-200 hover:border-[#C5A059]/60 hover:shadow-xs transition-all flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-start gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-amber-50 border border-[#C5A059]/30 text-lg flex items-center justify-center shrink-0 overflow-hidden">
                                                @if ($sub->image_url)
                                                    <img src="{{ $sub->image_url }}" alt="{{ $sub->name }}" class="w-full h-full object-cover">
                                                @elseif ($sub->icon)
                                                    <span>{{ $sub->icon }}</span>
                                                @else
                                                    <span>🛠️</span>
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <h3 class="text-xs font-bold text-slate-900 mb-1 truncate">{{ $sub->name }}</h3>
                                                <p class="text-[11px] text-slate-500 leading-relaxed">{{ $sub->description }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                        <div>
                                            @if ($priceRecord)
                                                <div class="text-[10px] text-slate-400 uppercase font-semibold">Standard Rate ({{ $priceRecord->region?->name ?? $selectedRegion?->name }})</div>
                                                <div class="text-xs font-extrabold text-[#8F6B20]">
                                                    {{ $priceRecord->currency }} {{ number_format((float) $priceRecord->price, 2) }}
                                                </div>
                                            @else
                                                <div class="text-[10px] text-slate-400">Custom Estimate</div>
                                                <div class="text-xs font-semibold text-slate-700">Contact for Quote</div>
                                            @endif
                                        </div>

                                        <a
                                            href="{{ route('contact', ['category_id' => $category->id, 'subcategory_id' => $sub->id, 'region_id' => $selectedRegion?->id]) }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-slate-900 bg-amber-50 hover:bg-[#C5A059] border border-[#C5A059]/40 rounded-md transition-colors"
                                        >
                                            <span>Quote</span>
                                            <span>&rarr;</span>
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-3 text-center py-6 text-xs text-slate-400">
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
