<x-website.layout :title="'Request Service & Online Quote — EMAC Development, LLC.'">

    {{-- Contact Hero Section --}}
    <section class="relative overflow-hidden py-14 lg:py-20 bg-gradient-to-b from-[#FAF8F4] via-white to-[#F5F1E8] border-b border-slate-200/80">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#C5A05912_1px,transparent_1px),linear-gradient(to_bottom,#C5A05912_1px,transparent_1px)] bg-[size:3.5rem_3.5rem] pointer-events-none"></div>
        <div class="absolute top-1/4 right-1/3 w-80 h-80 bg-[#C5A059]/15 rounded-full blur-3xl pointer-events-none animate-pulse-glow"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal-init">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#C5A059]/15 border border-[#C5A059]/35 text-[#8F6B20] text-xs font-bold mb-4 backdrop-blur-md shadow-2xs">
                <x-gold-icon name="quote" size="xs" class="text-[#C5A059]" />
                <span>REGIONAL SERVICE & CONSULTATION</span>
            </div>

            <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight">
                Request a Quote or Book Service
            </h1>

            <p class="mt-4 text-sm sm:text-base text-slate-600 max-w-2xl mx-auto leading-relaxed">
                Connect with our certified estimators, architectural consultants, and master tradespeople across the Cayman Islands, Florida, and Jamaica.
            </p>
        </div>
    </section>

    {{-- Main Contact Form & Details Grid --}}
    <section class="py-16 bg-white perspective-container">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                {{-- Form Column --}}
                <div class="lg:col-span-7 reveal-init">
                    <div class="p-8 sm:p-10 rounded-3xl bg-[#FAF8F4] border border-slate-200/90 shadow-luxury">
                        <div class="mb-6">
                            <h2 class="text-xl font-bold text-slate-900">Service Request & Project Consultation</h2>
                            <p class="text-xs text-slate-500 mt-1">Fill out the details below and an EMAC regional coordinator will respond within 24 hours.</p>
                        </div>

                        <form method="POST" action="{{ route('contact.submit') }}" class="space-y-5">
                            @csrf

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                {{-- Name --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Your Full Name *</label>
                                    <input
                                        type="text"
                                        name="name"
                                        value="{{ old('name') }}"
                                        placeholder="e.g. Johnathan Smith"
                                        required
                                        class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-300 rounded-xl focus:border-[#C5A059] focus:ring-2 focus:ring-[#C5A059]/20 transition-all outline-none"
                                    >
                                    @error('name')
                                        <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Address *</label>
                                    <input
                                        type="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        placeholder="e.g. jsmith@example.com"
                                        required
                                        class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-300 rounded-xl focus:border-[#C5A059] focus:ring-2 focus:ring-[#C5A059]/20 transition-all outline-none"
                                    >
                                    @error('email')
                                        <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                {{-- Phone --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Phone Number</label>
                                    <input
                                        type="text"
                                        name="phone"
                                        value="{{ old('phone') }}"
                                        placeholder="+1 (555) 000-0000"
                                        class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-300 rounded-xl focus:border-[#C5A059] focus:ring-2 focus:ring-[#C5A059]/20 transition-all outline-none"
                                    >
                                    @error('phone')
                                        <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Regional Market --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Regional Market *</label>
                                    <select
                                        name="market"
                                        required
                                        class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-300 rounded-xl focus:border-[#C5A059] focus:ring-2 focus:ring-[#C5A059]/20 transition-all outline-none font-medium"
                                    >
                                        <option value="Cayman Islands" {{ old('market', request('market')) == 'Cayman Islands' ? 'selected' : '' }}>Cayman Islands</option>
                                        <option value="Florida" {{ old('market', request('market')) == 'Florida' ? 'selected' : '' }}>Florida, USA</option>
                                        <option value="Jamaica" {{ old('market', request('market')) == 'Jamaica' ? 'selected' : '' }}>Jamaica</option>
                                        <option value="General Inquiry" {{ old('market', request('market')) == 'General Inquiry' ? 'selected' : '' }}>General Inquiry</option>
                                    </select>
                                    @error('market')
                                        <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Dynamic Category from DB --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Service Division (Optional)</label>
                                <select
                                    name="category_id"
                                    class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-300 rounded-xl focus:border-[#C5A059] focus:ring-2 focus:ring-[#C5A059]/20 transition-all outline-none"
                                >
                                    <option value="">-- Select Specific Division (Optional) --</option>
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

                            {{-- Message / Request Details --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Project Details or Repair Description *</label>
                                <textarea
                                    name="message"
                                    rows="5"
                                    required
                                    placeholder="Please describe your requirements, property location, timeline, or house plan inquiry..."
                                    class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-300 rounded-xl focus:border-[#C5A059] focus:ring-2 focus:ring-[#C5A059]/20 transition-all outline-none leading-relaxed"
                                >{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button
                                type="submit"
                                class="btn-premium w-full py-3.5 px-6 text-xs font-bold text-slate-950 bg-gradient-to-r from-[#D4AF37] via-[#C5A059] to-[#B8903B] rounded-xl shadow-md shadow-[#C5A059]/25 cursor-pointer flex items-center justify-center gap-2"
                            >
                                <span>Submit Request for Estimate</span>
                                <svg class="w-4 h-4 arrow-slide" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Contact Info Cards Column --}}
                <div class="lg:col-span-5 space-y-6" data-reveal-stagger>
                    {{-- Quick Contact Card --}}
                    <div class="tilt-card p-8 rounded-3xl bg-[#FAF8F4] border border-[#C5A059]/35 shadow-luxury hover:border-[#C5A059] transition-all preserve-3d" data-tilt>
                        <div class="tilt-card-inner">
                            <h3 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-[#C5A059]/15 text-[#C5A059] flex items-center justify-center border border-[#C5A059]/30">
                                    <x-gold-icon name="phone" size="sm" />
                                </span>
                                <span>Direct Communication</span>
                            </h3>
                            <div class="space-y-4 text-xs">
                                <div>
                                    <div class="font-bold text-slate-700 uppercase tracking-wider text-[10px]">Email Inquiries</div>
                                    <a href="mailto:info@emacdevelopment.com" class="text-sm font-semibold text-[#8F6B20] hover:underline flex items-center gap-1.5 mt-0.5">
                                        <x-gold-icon name="email" size="xs" class="text-[#C5A059]" />
                                        <span>info@emacdevelopment.com</span>
                                    </a>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-700 uppercase tracking-wider text-[10px]">Toll-Free Phone</div>
                                    <a href="tel:+18005550199" class="text-sm font-semibold text-[#8F6B20] hover:underline flex items-center gap-1.5 mt-0.5">
                                        <x-gold-icon name="phone" size="xs" class="text-[#C5A059]" />
                                        <span>+1 (800) 555-0199</span>
                                    </a>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-700 uppercase tracking-wider text-[10px]">Operating Hours</div>
                                    <p class="text-slate-600 mt-0.5">Monday &ndash; Friday: 8:00 AM &ndash; 6:00 PM EST</p>
                                    <p class="text-[11px] text-[#8F6B20] font-semibold mt-0.5">24/7 Emergency Plumbing & Handyman on Call</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Markets Summary Card --}}
                    <div class="tilt-card p-8 rounded-3xl bg-[#FAF8F4] border border-[#C5A059]/35 shadow-luxury hover:border-[#C5A059] transition-all preserve-3d" data-tilt>
                        <div class="tilt-card-inner">
                            <h3 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-[#C5A059]/15 text-[#C5A059] flex items-center justify-center border border-[#C5A059]/30">
                                    <x-gold-icon name="location" size="sm" />
                                </span>
                                <span>Regional Coverage</span>
                            </h3>
                            <div class="space-y-3 text-xs text-slate-600">
                                <div class="p-3.5 rounded-2xl bg-white border border-slate-200 hover:border-[#C5A059]/40 transition-colors flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-[#C5A059]/15 text-[#C5A059] flex items-center justify-center shrink-0">
                                        <x-gold-icon name="home" size="xs" />
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">Cayman Islands</div>
                                        <div class="text-[11px] text-slate-500">Grand Cayman • Cayman Brac • Little Cayman</div>
                                    </div>
                                </div>
                                <div class="p-3.5 rounded-2xl bg-white border border-slate-200 hover:border-[#C5A059]/40 transition-colors flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-[#C5A059]/15 text-[#C5A059] flex items-center justify-center shrink-0">
                                        <x-gold-icon name="hammer" size="xs" />
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">Florida</div>
                                        <div class="text-[11px] text-slate-500">Commercial & Residential Contracting Services</div>
                                    </div>
                                </div>
                                <div class="p-3.5 rounded-2xl bg-white border border-slate-200 hover:border-[#C5A059]/40 transition-colors flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-[#C5A059]/15 text-[#C5A059] flex items-center justify-center shrink-0">
                                        <x-gold-icon name="tools" size="xs" />
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">Jamaica</div>
                                        <div class="text-[11px] text-slate-500">Property Maintenance & Renovation Operations</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-website.layout>
