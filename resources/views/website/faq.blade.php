<x-website.layout :title="'Frequently Asked Questions — EMAC Development, LLC.'">

    {{-- Hero Section --}}
    <section class="bg-gradient-to-b from-white to-slate-50 border-b border-slate-200 py-14 lg:py-18">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-[#C5A059]/40 text-[#8F6B20] text-xs font-semibold mb-3">
                <span>Support & Guidance</span>
            </div>

            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                Frequently Asked Questions
            </h1>

            <p class="mt-3 text-sm sm:text-base text-slate-600 max-w-2xl mx-auto leading-relaxed">
                Clear answers regarding our handyman repairs, plumbing services, pricing, scheduling, and warranty.
            </p>
        </div>
    </section>

    {{-- Main FAQ Accordion Grid --}}
    <section class="py-14 bg-white" x-data="{ openItem: 1 }">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- FAQ 1 --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 transition-colors" :class="openItem === 1 ? 'bg-white border-[#C5A059]' : ''">
                <button
                    type="button"
                    @click="openItem = openItem === 1 ? null : 1"
                    class="w-full flex items-center justify-between text-left text-sm font-bold text-slate-900 cursor-pointer"
                >
                    <span>How quickly can a handyman or plumber arrive?</span>
                    <span class="text-[#8F6B20] font-bold text-lg" x-text="openItem === 1 ? '−' : '+'">+</span>
                </button>
                <div x-show="openItem === 1" class="mt-3 text-xs text-slate-600 leading-relaxed border-t border-slate-200 pt-3">
                    We offer standard scheduled appointments throughout the week, as well as priority emergency dispatch for urgent plumbing leaks and immediate handyman repairs across our service regions.
                </div>
            </div>

            {{-- FAQ 2 --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 transition-colors" :class="openItem === 2 ? 'bg-white border-[#C5A059]' : ''">
                <button
                    type="button"
                    @click="openItem = openItem === 2 ? null : 2"
                    class="w-full flex items-center justify-between text-left text-sm font-bold text-slate-900 cursor-pointer"
                >
                    <span>Do you provide upfront pricing before starting work?</span>
                    <span class="text-[#8F6B20] font-bold text-lg" x-text="openItem === 2 ? '−' : '+'">+</span>
                </button>
                <div x-show="openItem === 2" class="mt-3 text-xs text-slate-600 leading-relaxed border-t border-slate-200 pt-3">
                    Yes. We believe in 100% transparency. After assessing your request, our technician or coordinator provides a clear, itemized quote detailing labor and parts before any repair begins.
                </div>
            </div>

            {{-- FAQ 3 --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 transition-colors" :class="openItem === 3 ? 'bg-white border-[#C5A059]' : ''">
                <button
                    type="button"
                    @click="openItem = openItem === 3 ? null : 3"
                    class="w-full flex items-center justify-between text-left text-sm font-bold text-slate-900 cursor-pointer"
                >
                    <span>Are your tradespeople licensed and insured?</span>
                    <span class="text-[#8F6B20] font-bold text-lg" x-text="openItem === 3 ? '−' : '+'">+</span>
                </button>
                <div x-show="openItem === 3" class="mt-3 text-xs text-slate-600 leading-relaxed border-t border-slate-200 pt-3">
                    Yes. EMAC Development holds certified contractor credentials and full liability insurance coverage to protect your home and commercial property.
                </div>
            </div>

            {{-- FAQ 4 --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 transition-colors" :class="openItem === 4 ? 'bg-white border-[#C5A059]' : ''">
                <button
                    type="button"
                    @click="openItem = openItem === 4 ? null : 4"
                    class="w-full flex items-center justify-between text-left text-sm font-bold text-slate-900 cursor-pointer"
                >
                    <span>Can I bundle multiple handyman and plumbing repairs together?</span>
                    <span class="text-[#8F6B20] font-bold text-lg" x-text="openItem === 4 ? '−' : '+'">+</span>
                </button>
                <div x-show="openItem === 4" class="mt-3 text-xs text-slate-600 leading-relaxed border-t border-slate-200 pt-3">
                    Absolutely! In fact, we recommend it. You can list all your repair tasks in our request form, and we will dispatch the appropriate tradesperson to resolve them in a single visit, saving you time and cost.
                </div>
            </div>

            {{-- FAQ 5 --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 transition-colors" :class="openItem === 5 ? 'bg-white border-[#C5A059]' : ''">
                <button
                    type="button"
                    @click="openItem = openItem === 5 ? null : 5"
                    class="w-full flex items-center justify-between text-left text-sm font-bold text-slate-900 cursor-pointer"
                >
                    <span>Do you offer a warranty on completed work?</span>
                    <span class="text-[#8F6B20] font-bold text-lg" x-text="openItem === 5 ? '−' : '+'">+</span>
                </button>
                <div x-show="openItem === 5" class="mt-3 text-xs text-slate-600 leading-relaxed border-t border-slate-200 pt-3">
                    Yes. All EMAC repair and plumbing work is backed by our customer satisfaction guarantee and standard workmanship warranty.
                </div>
            </div>

        </div>
    </section>

    {{-- Contact CTA --}}
    <section class="py-12 bg-slate-50 border-t border-slate-200 text-center">
        <h3 class="text-lg font-bold text-slate-900">Still have questions?</h3>
        <p class="text-xs text-slate-500 mt-1">Our customer support team is happy to help.</p>
        <div class="mt-4">
            <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold text-slate-900 bg-[#C5A059] hover:bg-[#b8934b] rounded-lg transition-colors">
                <span>Contact Support</span>
                <span>&rarr;</span>
            </a>
        </div>
    </section>

</x-website.layout>
