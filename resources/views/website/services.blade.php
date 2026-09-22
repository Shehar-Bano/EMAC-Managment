<x-website.layout :title="'Services & Capabilities — EMAC Development, LLC.'">

    {{-- Services Hero Header --}}
    <section class="bg-gradient-to-b from-white to-slate-50 border-b border-slate-200 py-14 lg:py-18">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-[#C5A059]/40 text-[#8F6B20] text-xs font-semibold mb-3">
                <span>Certified Trades & Property Solutions</span>
            </div>

            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                Our Services & Capabilities
            </h1>

            <p class="mt-3 text-sm sm:text-base text-slate-600 max-w-2xl mx-auto leading-relaxed">
                Explore our full range of professional handyman repairs, property maintenance checkups, and certified plumbing solutions.
            </p>
        </div>
    </section>

    {{-- Categories & Subcategories List --}}
    <section class="py-14 bg-white" x-data="{ activeTab: 'all' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Filter Pills --}}
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
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-[#C5A059] flex items-center justify-center text-xl shrink-0 shadow-2xs">
                                    {{ $category->icon ?? '🛠️' }}
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-slate-900">{{ $category->name }}</h2>
                                    <p class="text-xs text-slate-600 mt-0.5">{{ $category->description }}</p>
                                </div>
                            </div>

                            <a
                                href="{{ route('contact', ['category_id' => $category->id]) }}"
                                class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-slate-900 bg-[#C5A059] hover:bg-[#b8934b] rounded-lg transition-colors shrink-0"
                            >
                                Request Quote &rarr;
                            </a>
                        </div>

                        {{-- Subcategories Grid --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                            @forelse ($category->activeSubcategories as $sub)
                                <div class="bg-white p-4 rounded-xl border border-slate-200 hover:border-[#C5A059]/60 hover:shadow-xs transition-all">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-amber-50 border border-[#C5A059]/30 text-base flex items-center justify-center shrink-0">
                                            {{ $sub->icon ?? '✓' }}
                                        </div>
                                        <div>
                                            <h3 class="text-xs font-bold text-slate-900 mb-1">{{ $sub->name }}</h3>
                                            <p class="text-[11px] text-slate-500 leading-relaxed">{{ $sub->description }}</p>
                                        </div>
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
