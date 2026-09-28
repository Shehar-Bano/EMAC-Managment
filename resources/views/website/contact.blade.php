<x-website.layout :title="'Request a Quote & Contact Us — EMAC Development, LLC.'">

    {{-- Contact Hero Section --}}
    <section class="bg-gradient-to-b from-white to-slate-50 border-b border-slate-200 py-14 lg:py-18">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-[#C5A059]/40 text-[#8F6B20] text-xs font-semibold mb-3">
                <span>Free Transparent Estimates</span>
            </div>

            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                Request a Quote or Book Service
            </h1>

            <p class="mt-3 text-sm sm:text-base text-slate-600 max-w-2xl mx-auto leading-relaxed">
                Select your service region and requirements to see estimated upfront pricing. You can also upload photos and a short video of the issue for an accurate quote.
            </p>
        </div>
    </section>

    {{-- Main Contact Form & Details Grid --}}
    <section class="py-14 bg-white" x-data="quoteFormHandler({
        categories: {{ Js::from($categories) }},
        regions: {{ Js::from($regions) }},
        initialRegionId: {{ (int) old('region_id', request('region_id', $regions->first()?->id ?? 1)) }},
        initialCategoryId: {{ (int) old('category_id', request('category_id', $categories->first()?->id ?? 0)) }},
        initialSubcategoryId: {{ (int) old('subcategory_id', request('subcategory_id', 0)) }},
    })">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                {{-- Form Column --}}
                <div class="lg:col-span-7">
                    <div class="p-6 sm:p-8 rounded-3xl bg-slate-50 border border-slate-200 shadow-xs">
                        <div class="mb-6 border-b border-slate-200 pb-4">
                            <h2 class="text-lg font-bold text-slate-900">Service Quote Request Form</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Fill out your details and project info for immediate coordinator response.</p>
                        </div>

                        <form method="POST" action="{{ route('contact.submit') }}" enctype="multipart/form-data" class="space-y-5">
                            @csrf

                            {{-- Contact Information --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- Name --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Full Name *</label>
                                    <input
                                        type="text"
                                        name="name"
                                        value="{{ old('name') }}"
                                        placeholder="John Doe"
                                        required
                                        class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-300 rounded-xl focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none"
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
                                        placeholder="john@example.com"
                                        required
                                        class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-300 rounded-xl focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none"
                                    >
                                    @error('email')
                                        <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- Phone --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Phone Number</label>
                                    <input
                                        type="text"
                                        name="phone"
                                        value="{{ old('phone') }}"
                                        placeholder="+1 (555) 000-0000"
                                        class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-300 rounded-xl focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none"
                                    >
                                    @error('phone')
                                        <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Service Region (Dynamic from DB) --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Service Market / Region *</label>
                                    <select
                                        name="region_id"
                                        x-model.number="selectedRegionId"
                                        @change="updatePricing()"
                                        required
                                        class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-300 rounded-xl focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none font-semibold"
                                    >
                                        <template x-for="reg in regions" :key="reg.id">
                                            <option :value="reg.id" x-text="reg.name + ' (' + reg.currency + ')'"></option>
                                        </template>
                                    </select>
                                    @error('region_id')
                                        <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Service Category & Subcategory Selection --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- Category --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Service Category</label>
                                    <select
                                        name="category_id"
                                        x-model.number="selectedCategoryId"
                                        @change="onCategoryChange()"
                                        class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-300 rounded-xl focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none font-medium"
                                    >
                                        <option value="0">-- Select Category --</option>
                                        <template x-for="cat in categories" :key="cat.id">
                                            <option :value="cat.id" x-text="cat.name"></option>
                                        </template>
                                    </select>
                                    @error('category_id')
                                        <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Subcategory --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Specific Service / Task</label>
                                    <select
                                        name="subcategory_id"
                                        x-model.number="selectedSubcategoryId"
                                        @change="updatePricing()"
                                        :disabled="subcategoriesList.length === 0"
                                        class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-300 rounded-xl focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none font-medium disabled:bg-slate-100 disabled:text-slate-400"
                                    >
                                        <option value="0">-- Select Specific Task --</option>
                                        <template x-for="sub in subcategoriesList" :key="sub.id + '-' + selectedRegionId">
                                            <option :value="sub.id" x-text="getSubcategoryOptionLabel(sub)"></option>
                                        </template>
                                    </select>
                                    @error('subcategory_id')
                                        <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Project Description / Message --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Describe Your Issue or Project *</label>
                                <textarea
                                    name="message"
                                    rows="4"
                                    required
                                    placeholder="Please describe the repairs or maintenance needed, preferred appointment time, and property address..."
                                    class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-300 rounded-xl focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none leading-relaxed"
                                >{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Media Upload Section: Multiple Photographs & Video --}}
                            <div class="space-y-4 pt-2 border-t border-slate-200">
                                <div>
                                    <label class="block text-xs font-bold text-slate-900 mb-0.5">Attach Photographs (Multiple Images Allowed)</label>
                                    <p class="text-[11px] text-slate-500 mb-2">Upload pictures of the repair area or fixtures (PNG, JPG, WEBP, up to 10MB each).</p>

                                    <div class="relative border-2 border-dashed border-slate-300 hover:border-[#C5A059] rounded-2xl p-4 bg-white text-center cursor-pointer transition-colors">
                                        <input
                                            type="file"
                                            name="photographs[]"
                                            multiple
                                            accept="image/png,image/jpeg,image/webp,image/jpg"
                                            @change="handleImageSelection($event)"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                        >
                                        <div class="flex flex-col items-center justify-center space-y-1">
                                            <div class="w-9 h-9 rounded-full bg-amber-50 text-[#8F6B20] flex items-center justify-center">
                                                📸
                                            </div>
                                            <div class="text-xs font-semibold text-slate-700">
                                                <span class="text-[#8F6B20] font-bold">Click to browse</span> or drag multiple photos here
                                            </div>
                                            <div class="text-[10px] text-slate-400" x-text="selectedPhotosCount > 0 ? (selectedPhotosCount + ' photo(s) selected') : 'Supports up to 10 photos'"></div>
                                        </div>
                                    </div>
                                    @error('photographs.*')
                                        <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-900 mb-0.5">Attach Video Clip (Optional)</label>
                                    <p class="text-[11px] text-slate-500 mb-2">Upload a short walkthrough video showing the leak or damage (MP4, MOV, WEBM, max 50MB).</p>

                                    <div class="relative border-2 border-dashed border-slate-300 hover:border-[#C5A059] rounded-2xl p-4 bg-white text-center cursor-pointer transition-colors">
                                        <input
                                            type="file"
                                            name="video"
                                            accept="video/mp4,video/quicktime,video/webm,video/x-msvideo,video/3gpp"
                                            @change="handleVideoSelection($event)"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                        >
                                        <div class="flex flex-col items-center justify-center space-y-1">
                                            <div class="w-9 h-9 rounded-full bg-amber-50 text-[#8F6B20] flex items-center justify-center">
                                                🎥
                                            </div>
                                            <div class="text-xs font-semibold text-slate-700">
                                                <span class="text-[#8F6B20] font-bold">Click to choose video</span> or drag video file here
                                            </div>
                                            <div class="text-[10px] text-slate-400" x-text="selectedVideoName ? ('Selected: ' + selectedVideoName) : 'Max file size 50MB'"></div>
                                        </div>
                                    </div>
                                    @error('video')
                                        <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <button
                                type="submit"
                                class="w-full py-3.5 px-6 text-xs font-bold text-slate-950 bg-gradient-to-r from-[#D4AF37] to-[#C5A059] hover:brightness-105 rounded-xl shadow-xs transition-all cursor-pointer"
                            >
                                Submit Service Quote Request
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Contact Details Column --}}
                <div class="lg:col-span-5 space-y-6">
                    <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200">
                        <h3 class="text-sm font-bold text-slate-900 mb-4 pb-2 border-b border-slate-200">Direct Contact & Support</h3>
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

                    <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200">
                        <h3 class="text-sm font-bold text-slate-900 mb-2">Our Quality Commitment</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            All EMAC repair and plumbing technicians are licensed, background-checked, and committed to clean, respectful, on-time service with upfront pricing across Grand Cayman, Florida, and Jamaica.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Interactive Alpine Handler --}}
    <script>
        function quoteFormHandler(config) {
            return {
                categories: config.categories || [],
                regions: config.regions || [],
                selectedRegionId: config.initialRegionId || (config.regions[0]?.id || 1),
                selectedCategoryId: config.initialCategoryId || 0,
                selectedSubcategoryId: config.initialSubcategoryId || 0,
                subcategoriesList: [],
                estimatedPrice: null,
                currency: 'USD',
                selectedSubcategoryName: '',
                selectedPhotosCount: 0,
                selectedVideoName: '',

                init() {
                    if (this.selectedCategoryId > 0) {
                        this.onCategoryChange();
                    } else if (this.categories.length > 0) {
                        this.selectedCategoryId = this.categories[0].id;
                        this.onCategoryChange();
                    }
                    this.updatePricing();
                },

                onRegionChange() {
                    this.updatePricing();
                },

                onCategoryChange() {
                    const currentCat = this.categories.find(c => Number(c.id) === Number(this.selectedCategoryId));
                    this.subcategoriesList = (currentCat && currentCat.active_subcategories) ? currentCat.active_subcategories : [];
                    if (this.subcategoriesList.length > 0) {
                        const exists = this.subcategoriesList.some(s => Number(s.id) === Number(this.selectedSubcategoryId));
                        if (!exists) {
                            this.selectedSubcategoryId = this.subcategoriesList[0].id;
                        }
                    } else {
                        this.selectedSubcategoryId = 0;
                    }
                    this.updatePricing();
                },

                getSubcategoryOptionLabel(sub) {
                    if (!sub) return '';
                    const prices = sub.regional_service_prices || sub.active_regional_service_prices || [];
                    const matchedPrice = prices.find(p => Number(p.region_id) === Number(this.selectedRegionId) && (p.status === 'active' || p.status === undefined))
                        || prices.find(p => Number(p.region_id) === Number(this.selectedRegionId));

                    if (matchedPrice && matchedPrice.price !== null && matchedPrice.price !== undefined && matchedPrice.price !== '') {
                        const curr = matchedPrice.currency || this.currency || 'USD';
                        const numPrice = Number(matchedPrice.price);
                        const formattedPrice = isNaN(numPrice) ? matchedPrice.price : numPrice.toFixed(2);
                        return `${sub.name} — ${curr} ${formattedPrice}`;
                    }

                    return sub.name;
                },

                updatePricing() {
                    const currentRegion = this.regions.find(r => Number(r.id) === Number(this.selectedRegionId));
                    this.currency = currentRegion ? currentRegion.currency : 'USD';

                    const currentSub = this.subcategoriesList.find(s => Number(s.id) === Number(this.selectedSubcategoryId));
                    if (currentSub) {
                        this.selectedSubcategoryName = currentSub.name;
                        const prices = currentSub.regional_service_prices || currentSub.active_regional_service_prices || [];
                        const matchedPrice = prices.find(p => Number(p.region_id) === Number(this.selectedRegionId) && (p.status === 'active' || p.status === undefined))
                            || prices.find(p => Number(p.region_id) === Number(this.selectedRegionId));

                        if (matchedPrice && matchedPrice.price !== null && matchedPrice.price !== undefined && matchedPrice.price !== '') {
                            this.estimatedPrice = matchedPrice.price;
                            this.currency = matchedPrice.currency || this.currency;
                        } else if (prices.length > 0) {
                            this.estimatedPrice = prices[0].price;
                            this.currency = prices[0].currency || this.currency;
                        } else {
                            this.estimatedPrice = null;
                        }
                    } else {
                        this.estimatedPrice = null;
                        this.selectedSubcategoryName = '';
                    }
                },

                handleImageSelection(event) {
                    this.selectedPhotosCount = event.target.files ? event.target.files.length : 0;
                },

                handleVideoSelection(event) {
                    if (event.target.files && event.target.files.length > 0) {
                        this.selectedVideoName = event.target.files[0].name;
                    } else {
                        this.selectedVideoName = '';
                    }
                }
            };
        }
    </script>

</x-website.layout>
