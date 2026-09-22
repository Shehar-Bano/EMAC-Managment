<x-website.layout :title="'Request a Quote & Contact Us — EMAC Development, LLC.'">

    {{-- Contact Hero Section --}}
    <section class="bg-gradient-to-b from-white to-slate-50 border-b border-slate-200 py-14 lg:py-18">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-[#C5A059]/40 text-[#8F6B20] text-xs font-semibold mb-3">
                <span>Get In Touch</span>
            </div>

            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                Request a Free Quote or Book Service
            </h1>

            <p class="mt-3 text-sm sm:text-base text-slate-600 max-w-2xl mx-auto leading-relaxed">
                Tell us about your handyman or plumbing requirements. Our regional coordinator will get back to you promptly with clear estimates and scheduling options.
            </p>
        </div>
    </section>

    {{-- Main Contact Form & Details Grid --}}
    <section class="py-14 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                {{-- Form Column --}}
                <div class="lg:col-span-7">
                    <div class="p-6 sm:p-8 rounded-2xl bg-slate-50 border border-slate-200">
                        <div class="mb-6 border-b border-slate-200 pb-4">
                            <h2 class="text-lg font-bold text-slate-900">Service Request Form</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Please provide your contact info and project requirements.</p>
                        </div>

                        <form method="POST" action="{{ route('contact.submit') }}" class="space-y-4">
                            @csrf

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- Name --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name *</label>
                                    <input
                                        type="text"
                                        name="name"
                                        value="{{ old('name') }}"
                                        placeholder="John Doe"
                                        required
                                        class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none"
                                    >
                                    @error('name')
                                        <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address *</label>
                                    <input
                                        type="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        placeholder="john@example.com"
                                        required
                                        class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none"
                                    >
                                    @error('email')
                                        <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- Phone --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Phone Number</label>
                                    <input
                                        type="text"
                                        name="phone"
                                        value="{{ old('phone') }}"
                                        placeholder="+1 (555) 000-0000"
                                        class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none"
                                    >
                                    @error('phone')
                                        <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Regional Market --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Service Market *</label>
                                    <select
                                        name="market"
                                        required
                                        class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none"
                                    >
                                        <option value="Cayman Islands" {{ old('market', request('market')) == 'Cayman Islands' ? 'selected' : '' }}>Grand Cayman</option>
                                        <option value="Florida" {{ old('market', request('market')) == 'Florida' ? 'selected' : '' }}>Florida, USA</option>
                                        <option value="Jamaica" {{ old('market', request('market')) == 'Jamaica' ? 'selected' : '' }}>Jamaica</option>
                                        <option value="General Inquiry" {{ old('market', request('market')) == 'General Inquiry' ? 'selected' : '' }}>General Inquiry</option>
                                    </select>
                                    @error('market')
                                        <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Category from DB --}}
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Service Category (Optional)</label>
                                <select
                                    name="category_id"
                                    class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none"
                                >
                                    <option value="">-- Select Category --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id', request('category_id')) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Message --}}
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Describe Your Issue or Project *</label>
                                <textarea
                                    name="message"
                                    rows="4"
                                    required
                                    placeholder="Please describe the repairs or maintenance needed, preferred schedule, and property location..."
                                    class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none"
                                >{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button
                                type="submit"
                                class="w-full py-3 px-6 text-xs font-bold text-slate-900 bg-[#C5A059] hover:bg-[#b8934b] rounded-lg transition-colors cursor-pointer"
                            >
                                Submit Quote Request
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Contact Details Column --}}
                <div class="lg:col-span-5 space-y-6">
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200">
                        <h3 class="text-sm font-bold text-slate-900 mb-4">Direct Contact & Support</h3>
                        <div class="space-y-4 text-xs">
                            <div>
                                <div class="font-semibold text-slate-500">Toll-Free Phone:</div>
                                <a href="tel:+18005550199" class="text-sm font-bold text-slate-900 hover:text-[#8F6B20] mt-0.5 block">
                                    +1 (800) 555-0199
                                </a>
                            </div>

                            <div>
                                <div class="font-semibold text-slate-500">Email Address:</div>
                                <a href="mailto:info@emacdevelopment.com" class="text-sm font-bold text-slate-900 hover:text-[#8F6B20] mt-0.5 block">
                                    info@emacdevelopment.com
                                </a>
                            </div>

                            <div>
                                <div class="font-semibold text-slate-500">Business Hours:</div>
                                <p class="text-slate-700 mt-0.5">Monday &ndash; Friday: 8:00 AM &ndash; 6:00 PM EST</p>
                                <p class="text-slate-700">Saturday: 9:00 AM &ndash; 2:00 PM EST</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200">
                        <h3 class="text-sm font-bold text-slate-900 mb-2">Our Quality Commitment</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            All EMAC repair and plumbing technicians are licensed, background-checked, and committed to clean, respectful, on-time service with upfront pricing.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-website.layout>
