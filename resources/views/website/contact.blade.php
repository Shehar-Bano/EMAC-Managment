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
        initialRegionId: {{ (int) old('region_id', $defaultRegionId ?? ($regions->first()?->id ?? 1)) }},
        initialCategoryId: {{ (int) old('category_id', request('category_id', $categories->first()?->id ?? 0)) }},
        initialSubcategoryId: {{ (int) old('subcategory_id', request('subcategory_id', 0)) }},
    })">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                {{-- Form Column --}}
                <div class="lg:col-span-7">
                    <div class="p-6 sm:p-8 rounded-3xl bg-slate-50 border border-slate-200 shadow-xs">
                        <div class="mb-6 border-b border-slate-200 pb-4 flex items-center justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">Service Quote Request Form</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Select your category, service task, and describe your maintenance needs.</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-[#8F6B20] text-[11px] font-bold border border-[#C5A059]/30">
                                Step 1 of 1
                            </span>
                        </div>

                        {{-- Logged In User Verified Info Banner / Guest Login Prompt --}}
                        @auth
                            <div class="mb-5 p-4 rounded-2xl bg-amber-50/70 border border-[#C5A059]/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs shadow-2xs">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#E5C158] to-[#C5A059] text-slate-950 font-black flex items-center justify-center text-sm shrink-0 shadow-xs">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                            <span>{{ auth()->user()->name }}</span>
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">Auto-filled</span>
                                        </div>
                                        <div class="text-slate-600 text-[11px] mt-0.5">
                                            {{ auth()->user()->email }} • {{ auth()->user()->phone ?? 'Phone on file' }}
                                        </div>
                                    </div>
                                </div>
                                @if ($userAddress)
                                    <div class="text-[11px] text-slate-600 sm:text-right border-t sm:border-t-0 pt-2 sm:pt-0 border-amber-200/60">
                                        <div class="font-semibold text-slate-700">Registered Territory:</div>
                                        <div class="font-bold text-[#8F6B20]">{{ $userAddress->region?->name ?? 'Service Market' }}</div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="mb-5 p-3.5 rounded-2xl bg-white border border-slate-200/80 flex flex-wrap items-center justify-between gap-3 text-xs shadow-2xs">
                                <div class="flex items-center gap-2 text-slate-600">
                                    <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Already registered with EMAC?</span>
                                </div>
                                <a href="{{ route('login') }}" class="font-bold text-[#8F6B20] hover:text-[#C5A059] hover:underline">
                                    Sign In to auto-fill details &rarr;
                                </a>
                            </div>
                        @endauth

                        {{-- Form Submission Error Banner (Client/Server) --}}
                        <div x-show="formErrorMessage" x-cloak class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-start justify-between gap-3 shadow-xs">
                            <div class="flex items-start gap-2.5">
                                <span class="text-base leading-none mt-0.5">⚠️</span>
                                <div>
                                    <h4 class="font-bold">Submission Error</h4>
                                    <p class="mt-0.5 text-[11px] text-rose-600 leading-relaxed" x-text="formErrorMessage"></p>
                                </div>
                            </div>
                            <button type="button" @click="formErrorMessage = null" class="text-rose-400 hover:text-rose-700 font-bold text-sm leading-none">&times;</button>
                        </div>

                        <form method="POST" action="{{ route('contact.submit') }}" enctype="multipart/form-data" @submit.prevent="submitForm($event)" class="space-y-5">
                            @csrf

                            {{-- Guest Contact Information (Hidden/Auto-populated for logged-in user) --}}
                            @guest
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

                                    {{-- Property Location / Street Address (No zipcode) --}}
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Property Location / Street Address</label>
                                        <input
                                            type="text"
                                            name="property_information"
                                            value="{{ old('property_information') }}"
                                            placeholder="e.g. 742 Evergreen Terrace, Apt 4B"
                                            class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-300 rounded-xl focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none"
                                        >
                                    </div>
                                </div>
                            @else
                                {{-- For logged-in user, optional field to specify alternate property location if different from primary --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                        Property Location / Service Address
                                    </label>
                                    <input
                                        type="text"
                                        name="property_information"
                                        value="{{ old('property_information', $userAddress?->address) }}"
                                        placeholder="e.g. 742 Evergreen Terrace, Apt 4B"
                                        class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-300 rounded-xl focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none"
                                    >
                                    <p class="text-[10px] text-slate-400 mt-1">Pre-filled with your on-file address. You can update this for a different property.</p>
                                </div>
                            @endguest

                            {{-- Service Territory / Region Selector --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Service Market / Region *</label>
                                <select
                                    name="region_id"
                                    x-model.number="selectedRegionId"
                                    @change="updateRegion()"
                                    required
                                    class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-300 rounded-xl focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none font-semibold cursor-pointer"
                                >
                                    <template x-for="reg in regions" :key="reg.id">
                                        <option :value="reg.id" x-text="reg.name + ' (' + reg.currency + ')'"></option>
                                    </template>
                                </select>
                                @error('region_id')
                                    <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Service Category & Subcategory Selection --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- Category --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Service Category *</label>
                                    <select
                                        name="category_id"
                                        x-model.number="selectedCategoryId"
                                        @change="onCategoryChange()"
                                        required
                                        class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-300 rounded-xl focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none font-semibold cursor-pointer"
                                    >
                                        <option value="">-- Select Category --</option>
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
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Specific Service / Task *</label>
                                    <select
                                        name="subcategory_id"
                                        x-model.number="selectedSubcategoryId"
                                        @change="updatePricing()"
                                        :disabled="subcategoriesList.length === 0"
                                        required
                                        class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-300 rounded-xl focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none font-semibold disabled:bg-slate-100 disabled:text-slate-400 cursor-pointer"
                                    >
                                        <option value="">-- Select Specific Task --</option>
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
                                    placeholder="Please describe the repairs or maintenance needed, symptoms of the problem, and any specific preferences..."
                                    class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-300 rounded-xl focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none leading-relaxed"
                                >{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Priority / Emergency Request Option --}}
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200" x-data="{ isEmergency: {{ old('is_emergency') || old('priority') === 'emergency' ? 'true' : 'false' }} }">
                                <div class="flex items-start sm:items-center justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 transition-colors"
                                            :class="isEmergency ? 'bg-rose-100 text-rose-700 border border-rose-200' : 'bg-amber-100 text-[#8F6B20] border border-[#C5A059]/30'">
                                            <span x-text="isEmergency ? '🚨' : '⚡'" class="text-xl"></span>
                                        </div>
                                        <div>
                                            <label for="is_emergency_toggle" class="text-xs font-bold text-slate-900 cursor-pointer flex items-center gap-2">
                                                <span>Emergency Priority Request</span>
                                                <span x-show="isEmergency" x-cloak class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-600 text-white uppercase tracking-wider animate-pulse">
                                                    Urgent / 24/7
                                                </span>
                                            </label>
                                            <p class="text-[11px] text-slate-500 mt-0.5">
                                                <span x-show="!isEmergency">Normal request with standard scheduling and transparent quote.</span>
                                                <span x-show="isEmergency" x-cloak class="text-rose-600 font-semibold">Flagged for immediate response and emergency priority handling.</span>
                                            </p>
                                        </div>
                                    </div>

                                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                        <input
                                            type="checkbox"
                                            id="is_emergency_toggle"
                                            name="is_emergency"
                                            value="1"
                                            x-model="isEmergency"
                                            class="sr-only peer"
                                        >
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-600"></div>
                                    </label>
                                </div>
                            </div>

                            {{-- Media Upload Section: Multiple Photographs & Video with Live Preview & View Modal --}}
                            <div class="space-y-5 pt-3 border-t border-slate-200">
                                {{-- 1. Photographs Uploader & Live Preview Grid --}}
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-xs font-bold text-slate-900">Attach Photographs</label>
                                        <span class="text-[11px] font-semibold text-slate-500" x-text="selectedPhotos.length > 0 ? (selectedPhotos.length + '/10 Photos Selected') : 'Up to 10 photos'"></span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mb-2.5">Upload clear pictures of the repair area or fixtures (PNG, JPG, WEBP, up to 10MB each).</p>

                                    {{-- Photo Dropzone when no photo selected --}}
                                    <div x-show="selectedPhotos.length === 0" class="relative border-2 border-dashed border-slate-300 hover:border-[#C5A059] rounded-2xl p-5 bg-white text-center cursor-pointer transition-all hover:bg-amber-50/20 group">
                                        <input
                                            type="file"
                                            id="photos-file-input"
                                            name="photographs[]"
                                            multiple
                                            accept="image/png,image/jpeg,image/webp,image/jpg,image/heic,image/heif"
                                            @change="handleImageSelection($event)"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                        >
                                        <div class="flex flex-col items-center justify-center space-y-1.5 pointer-events-none">
                                            <div class="w-10 h-10 rounded-full bg-amber-50 text-[#8F6B20] flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                                                📸
                                            </div>
                                            <div class="text-xs font-semibold text-slate-700">
                                                <span class="text-[#8F6B20] font-bold">Click to browse</span> or drag multiple photos here
                                            </div>
                                            <div class="text-[10px] text-slate-400">Supports PNG, JPG, WEBP &bull; Max 10MB per image</div>
                                        </div>
                                    </div>

                                    {{-- Selected Photos Preview Gallery --}}
                                    <div x-show="selectedPhotos.length > 0" x-cloak class="space-y-3">
                                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5">
                                            <template x-for="(photo, index) in selectedPhotos" :key="photo.id">
                                                <div class="relative group rounded-xl overflow-hidden border border-slate-200 bg-slate-900 aspect-square shadow-2xs">
                                                    <img :src="photo.preview" :alt="photo.name" class="w-full h-full object-cover">
                                                    
                                                    {{-- Hover Action Bar --}}
                                                    <div class="absolute inset-0 bg-slate-950/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-between p-2">
                                                        <div class="flex items-center justify-between">
                                                            <span class="text-[9px] font-mono font-bold text-white/90 bg-black/40 px-1.5 py-0.5 rounded backdrop-blur-xs" x-text="photo.size"></span>
                                                            <button
                                                                type="button"
                                                                @click.stop="removePhoto(index)"
                                                                title="Remove this photo"
                                                                class="w-6 h-6 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center text-xs shadow-xs transition-transform active:scale-90"
                                                            >
                                                                &times;
                                                            </button>
                                                        </div>
                                                        <button
                                                            type="button"
                                                            @click.stop="openModalPreview('image', photo.preview, photo.name, photo.size)"
                                                            class="w-full py-1 text-[10px] font-bold text-slate-900 bg-amber-400 hover:bg-amber-300 rounded-lg flex items-center justify-center gap-1 shadow-xs transition-colors"
                                                        >
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                            <span>View Image</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>

                                            {{-- Add More Photos Slot --}}
                                            <div x-show="selectedPhotos.length < 10" class="relative border-2 border-dashed border-slate-300 hover:border-[#C5A059] rounded-xl aspect-square flex flex-col items-center justify-center bg-slate-50 hover:bg-amber-50/30 transition-colors cursor-pointer group">
                                                <input
                                                    type="file"
                                                    multiple
                                                    accept="image/png,image/jpeg,image/webp,image/jpg,image/heic,image/heif"
                                                    @change="handleAdditionalPhotos($event)"
                                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                                >
                                                <span class="text-xl group-hover:scale-110 transition-transform">➕</span>
                                                <span class="text-[10px] font-bold text-slate-600 group-hover:text-[#8F6B20] mt-1">Add More</span>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between pt-1">
                                            <span class="text-[10px] text-slate-400">Click any image to view enlarged. Hover to delete.</span>
                                            <button
                                                type="button"
                                                @click="clearAllPhotos()"
                                                class="text-[11px] font-bold text-rose-600 hover:text-rose-700 hover:underline"
                                            >
                                                Clear All Photos
                                            </button>
                                        </div>
                                    </div>

                                    @error('photographs.*')
                                        <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- 2. Video Uploader & Live Player Preview --}}
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-xs font-bold text-slate-900">Attach Video Clip (Optional)</label>
                                        <span class="text-[11px] font-semibold text-slate-500" x-text="selectedVideo ? ('1 Video Selected (' + selectedVideo.size + ')') : 'Max 50MB'"></span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mb-2.5">Upload a short walkthrough video showing the leak or issue (MP4, MOV, WEBM, max 50MB).</p>

                                    {{-- Video Error Alert --}}
                                    <div x-show="videoError" x-cloak class="mb-3 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-center justify-between gap-2 shadow-2xs">
                                        <div class="flex items-center gap-2">
                                            <span>⚠️</span>
                                            <span x-text="videoError" class="font-medium"></span>
                                        </div>
                                        <button type="button" @click="videoError = null" class="text-rose-400 hover:text-rose-700 font-bold text-sm leading-none">&times;</button>
                                    </div>

                                    {{-- Video Dropzone when no video selected --}}
                                    <div
                                        x-show="!selectedVideo"
                                        @dragover.prevent="isDraggingVideo = true"
                                        @dragleave.prevent="isDraggingVideo = false"
                                        @drop.prevent="handleVideoDrop($event)"
                                        :class="isDraggingVideo ? 'border-[#C5A059] bg-amber-50/50 ring-2 ring-[#C5A059]/30' : 'border-slate-300 hover:border-[#C5A059] hover:bg-amber-50/20'"
                                        class="relative border-2 border-dashed rounded-2xl p-5 bg-white text-center cursor-pointer transition-all group"
                                    >
                                        <input
                                            type="file"
                                            id="video-file-input"
                                            name="video"
                                            accept="video/mp4,video/quicktime,video/webm,video/x-msvideo,video/3gpp,video/avi,video/*,.mp4,.mov,.webm,.avi,.mkv,.3gp"
                                            @change="handleVideoSelection($event)"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                        >
                                        <div class="flex flex-col items-center justify-center space-y-1.5 pointer-events-none">
                                            <div class="w-10 h-10 rounded-full bg-amber-50 text-[#8F6B20] flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                                                🎥
                                            </div>
                                            <div class="text-xs font-semibold text-slate-700">
                                                <span class="text-[#8F6B20] font-bold">Click to choose video</span> or drag video file here
                                            </div>
                                            <div class="text-[10px] text-slate-400">MP4, MOV, WEBM, AVI, MKV &bull; Max 50MB</div>
                                        </div>
                                    </div>

                                    {{-- Selected Video Player Card Preview --}}
                                    <div x-show="selectedVideo" x-cloak class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800 text-white shadow-xs space-y-3">
                                        <div class="flex items-center justify-between gap-2 border-b border-slate-800 pb-2">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="w-7 h-7 rounded-lg bg-amber-500/20 text-[#D4AF37] flex items-center justify-center text-xs shrink-0 font-bold">
                                                    🎬
                                                </span>
                                                <div class="min-w-0">
                                                    <p class="text-xs font-bold text-white truncate" x-text="selectedVideo?.name"></p>
                                                    <p class="text-[10px] font-mono text-slate-400" x-text="selectedVideo?.size"></p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1.5 shrink-0">
                                                <button
                                                    type="button"
                                                    @click="openModalPreview('video', selectedVideo.previewUrl, selectedVideo.name, selectedVideo.size)"
                                                    class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-slate-800 hover:bg-slate-700 text-amber-400 border border-amber-500/30 flex items-center gap-1 transition-colors cursor-pointer"
                                                >
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                    <span>Enlarge</span>
                                                </button>
                                                <button
                                                    type="button"
                                                    @click="removeVideo()"
                                                    class="px-2 py-1 text-[11px] font-bold rounded-lg bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/40 transition-colors cursor-pointer"
                                                    title="Remove video"
                                                >
                                                    &times; Remove
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Fixed Aspect Ratio Inline Video Player (Ensures Video Fits Inside Container) --}}
                                        <div class="relative w-full aspect-video max-h-[320px] rounded-xl overflow-hidden bg-black border border-slate-800 shadow-inner flex items-center justify-center">
                                            <video
                                                :src="selectedVideo?.previewUrl"
                                                controls
                                                playsinline
                                                preload="metadata"
                                                class="w-full h-full object-contain bg-black"
                                            ></video>
                                        </div>
                                    </div>

                                    @error('video')
                                        <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Submit Button with Interactive Loading State --}}
                            <button
                                type="submit"
                                :disabled="isSubmitting"
                                class="w-full py-3.5 px-6 text-xs font-bold text-slate-950 bg-gradient-to-r from-[#D4AF37] to-[#C5A059] hover:brightness-105 disabled:opacity-75 disabled:cursor-not-allowed rounded-xl shadow-xs transition-all cursor-pointer mt-3 flex items-center justify-center gap-2.5"
                            >
                                <svg x-show="isSubmitting" x-cloak class="animate-spin w-4 h-4 text-slate-950" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span x-text="isSubmitting ? (selectedVideo ? 'Uploading Video & Submitting Request...' : 'Submitting Service Request...') : 'Submit Service Quote Request'"></span>
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

        {{-- Full Screen / Modal Media Viewer (Fixed 16:9 Aspect Video Player View) --}}
        <div
            x-show="modalPreview.open"
            x-cloak
            @keydown.escape.window="closeModalPreview()"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-950/85 backdrop-blur-md transition-opacity"
        >
            <div
                @click.outside="closeModalPreview()"
                class="relative bg-slate-900 border border-slate-800 rounded-3xl shadow-2xl max-w-4xl w-full overflow-hidden flex flex-col max-h-[90vh]"
            >
                {{-- Modal Header --}}
                <div class="px-5 py-3.5 border-b border-slate-800 flex items-center justify-between text-white bg-slate-900/90">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#D4AF37]"></span>
                        <div class="min-w-0">
                            <h4 class="text-xs sm:text-sm font-bold truncate" x-text="modalPreview.title || 'Media Preview'"></h4>
                            <p class="text-[10px] font-mono text-slate-400" x-text="modalPreview.size ? ('Size: ' + modalPreview.size) : ''"></p>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="closeModalPreview()"
                        class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center text-base transition-colors shrink-0 cursor-pointer"
                    >
                        &times;
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-4 sm:p-6 flex items-center justify-center overflow-auto bg-black/70 min-h-[300px]">
                    <template x-if="modalPreview.type === 'image'">
                        <img :src="modalPreview.src" :alt="modalPreview.title" class="max-h-[70vh] max-w-full rounded-xl object-contain shadow-lg">
                    </template>
                    <template x-if="modalPreview.type === 'video'">
                        <div class="w-full max-w-3xl aspect-video rounded-2xl overflow-hidden bg-black shadow-2xl flex items-center justify-center border border-slate-800">
                            <video
                                :src="modalPreview.src"
                                controls
                                autoplay
                                playsinline
                                preload="auto"
                                class="w-full h-full object-contain bg-black"
                            ></video>
                        </div>
                    </template>
                </div>

                {{-- Modal Footer --}}
                <div class="px-5 py-3 border-t border-slate-800 bg-slate-900 flex justify-end">
                    <button
                        type="button"
                        @click="closeModalPreview()"
                        class="px-4 py-1.5 text-xs font-bold rounded-xl bg-slate-800 hover:bg-slate-700 text-white transition-colors cursor-pointer"
                    >
                        Close Preview
                    </button>
                </div>
            </div>
        </div>

        {{-- Full Screen Interactive Video/Media Upload Loader Modal Overlay --}}
        <div
            x-show="isSubmitting"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-950/85 backdrop-blur-md transition-all"
        >
            <div class="relative bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 max-w-md w-full text-center shadow-2xl text-white space-y-5">
                {{-- Animated Upload Icon Ring --}}
                <div class="relative w-16 h-16 mx-auto flex items-center justify-center">
                    <div class="absolute inset-0 rounded-full border-4 border-amber-500/20 border-t-[#D4AF37] animate-spin"></div>
                    <span class="text-2xl" x-text="uploadPercent === 100 ? '✅' : (selectedVideo ? '🎥' : '📤')"></span>
                </div>

                <div>
                    <h3 class="text-base font-bold text-white mb-1" x-text="uploadPercent === 100 ? 'Processing Service Request...' : (selectedVideo ? 'Uploading Walkthrough Video...' : 'Uploading Files & Submitting...')"></h3>
                    <p class="text-xs text-slate-400" x-text="uploadStatusText"></p>
                </div>

                {{-- Live Progress Bar & Percentage --}}
                <div class="space-y-2">
                    <div class="w-full h-3 bg-slate-800 rounded-full overflow-hidden p-0.5 border border-slate-700/60">
                        <div
                            class="h-full bg-gradient-to-r from-[#D4AF37] via-amber-400 to-[#C5A059] rounded-full transition-all duration-200 relative overflow-hidden"
                            :style="`width: ${uploadPercent}%`"
                        >
                            <div class="absolute inset-0 bg-white/25 animate-pulse"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-[11px] font-mono text-slate-400 px-1">
                        <span x-text="uploadedBytesText && totalBytesText ? (uploadedBytesText + ' / ' + totalBytesText) : 'Uploading...'"></span>
                        <span class="font-bold text-[#D4AF37]" x-text="uploadPercent + '%'"></span>
                    </div>
                </div>

                <div class="text-[11px] text-slate-400 bg-slate-800/60 py-2.5 px-3.5 rounded-xl border border-slate-800 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Please keep this window open while your video uploads.</span>
                </div>
            </div>
        </div>

    </section>

    {{-- Interactive Alpine Handler with Live Previews & Sync --}}
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
                
                // Photographs & Video State
                selectedPhotos: [],
                selectedVideo: null,
                isDraggingVideo: false,
                videoError: null,
                
                // Form Submission & Live Upload Progress State
                isSubmitting: false,
                uploadPercent: 0,
                uploadStatusText: 'Uploading media & submitting request...',
                uploadedBytesText: '',
                totalBytesText: '',
                formErrorMessage: null,

                modalPreview: {
                    open: false,
                    type: 'image',
                    src: '',
                    title: '',
                    size: ''
                },

                init() {
                    this.updateRegion();
                    if (this.selectedCategoryId > 0) {
                        this.onCategoryChange();
                    } else if (this.categories.length > 0) {
                        this.selectedCategoryId = this.categories[0].id;
                        this.onCategoryChange();
                    }
                    this.updatePricing();
                },

                formatBytes(bytes, decimals = 2) {
                    if (!bytes || bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const dm = decimals < 0 ? 0 : decimals;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
                },

                updateRegion() {
                    const currentRegion = this.regions.find(r => Number(r.id) === Number(this.selectedRegionId));
                    this.currency = currentRegion ? currentRegion.currency : 'USD';
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

                // Photograph Handlers
                handleImageSelection(event) {
                    const files = Array.from(event.target.files || []);
                    if (!files.length) return;

                    this.selectedPhotos = [];
                    files.slice(0, 10).forEach((file, idx) => {
                        const preview = URL.createObjectURL(file);
                        this.selectedPhotos.push({
                            id: Date.now() + '-' + idx,
                            file: file,
                            preview: preview,
                            name: file.name,
                            size: this.formatBytes(file.size)
                        });
                    });
                },

                handleAdditionalPhotos(event) {
                    const newFiles = Array.from(event.target.files || []);
                    if (!newFiles.length) return;

                    const maxAllowed = 10 - this.selectedPhotos.length;
                    newFiles.slice(0, maxAllowed).forEach((file, idx) => {
                        const preview = URL.createObjectURL(file);
                        this.selectedPhotos.push({
                            id: Date.now() + '-' + idx,
                            file: file,
                            preview: preview,
                            name: file.name,
                            size: this.formatBytes(file.size)
                        });
                    });

                    this.syncPhotosFileInput();
                },

                removePhoto(index) {
                    if (index >= 0 && index < this.selectedPhotos.length) {
                        const removed = this.selectedPhotos.splice(index, 1);
                        if (removed.length && removed[0].preview) {
                            URL.revokeObjectURL(removed[0].preview);
                        }
                        this.syncPhotosFileInput();
                    }
                },

                clearAllPhotos() {
                    this.selectedPhotos.forEach(p => {
                        if (p.preview) URL.revokeObjectURL(p.preview);
                    });
                    this.selectedPhotos = [];
                    const input = document.getElementById('photos-file-input');
                    if (input) input.value = '';
                },

                syncPhotosFileInput() {
                    const input = document.getElementById('photos-file-input');
                    if (!input) return;
                    
                    try {
                        const dt = new DataTransfer();
                        this.selectedPhotos.forEach(item => {
                            if (item.file) dt.items.add(item.file);
                        });
                        input.files = dt.files;
                    } catch (e) {
                        console.warn('DataTransfer sync note:', e);
                    }
                },

                // Video Handlers & Validation
                validateAndSetVideo(file) {
                    if (!file) return;

                    // 1. Validate max size (200MB = 209,715,200 bytes)
                    const maxBytes = 200 * 1024 * 1024;
                    if (file.size > maxBytes) {
                        this.videoError = `The selected video file is too large (${this.formatBytes(file.size)}). Maximum allowed size is 200MB. Please select a shorter or compressed clip.`;
                        const input = document.getElementById('video-file-input');
                        if (input) input.value = '';
                        return false;
                    }

                    // 2. Validate file extension / mime
                    const allowedExts = ['mp4', 'mov', 'webm', 'avi', 'mkv', '3gp', 'wmv', 'flv', 'mpeg', 'mpg', 'm4v', 'ogg', 'qt'];
                    const ext = (file.name.split('.').pop() || '').toLowerCase();
                    if (!allowedExts.includes(ext) && !file.type.startsWith('video/')) {
                        this.videoError = `Unsupported video format (${ext ? ('.' + ext) : 'Unknown'}). Please attach an MP4, MOV, WEBM, or AVI video.`;
                        const input = document.getElementById('video-file-input');
                        if (input) input.value = '';
                        return false;
                    }

                    this.videoError = null;

                    if (this.selectedVideo && this.selectedVideo.previewUrl) {
                        URL.revokeObjectURL(this.selectedVideo.previewUrl);
                    }

                    this.selectedVideo = {
                        file: file,
                        previewUrl: URL.createObjectURL(file),
                        name: file.name,
                        size: this.formatBytes(file.size)
                    };

                    // Sync file input if set via drag & drop
                    const input = document.getElementById('video-file-input');
                    if (input) {
                        try {
                            const dt = new DataTransfer();
                            dt.items.add(file);
                            input.files = dt.files;
                        } catch (e) {
                            console.warn('DataTransfer video sync note:', e);
                        }
                    }

                    return true;
                },

                handleVideoSelection(event) {
                    const files = event.target.files;
                    if (files && files.length > 0) {
                        this.validateAndSetVideo(files[0]);
                    }
                },

                handleVideoDrop(event) {
                    this.isDraggingVideo = false;
                    const files = event.dataTransfer?.files;
                    if (files && files.length > 0) {
                        this.validateAndSetVideo(files[0]);
                    }
                },

                removeVideo() {
                    if (this.selectedVideo && this.selectedVideo.previewUrl) {
                        URL.revokeObjectURL(this.selectedVideo.previewUrl);
                    }
                    this.selectedVideo = null;
                    this.videoError = null;
                    const input = document.getElementById('video-file-input');
                    if (input) input.value = '';
                },

                // Modal Viewer
                openModalPreview(type, src, title, size) {
                    this.modalPreview = {
                        open: true,
                        type: type,
                        src: src,
                        title: title,
                        size: size
                    };
                },

                closeModalPreview() {
                    this.modalPreview.open = false;
                    this.modalPreview.src = '';
                },

                // Form Submission with Live Upload Progress
                submitForm(event) {
                    const form = event.target;
                    this.formErrorMessage = null;

                    // Basic client validations
                    if (!this.selectedCategoryId || !this.selectedSubcategoryId) {
                        this.formErrorMessage = 'Please select a service category and specific task before submitting.';
                        return;
                    }

                    const message = form.querySelector('[name="message"]')?.value?.trim();
                    if (!message || message.length < 5) {
                        this.formErrorMessage = 'Please provide a detailed description of the issue or project (at least 5 characters).';
                        return;
                    }

                    // Prepare FormData
                    const formData = new FormData(form);

                    // Ensure selected photos are attached from Alpine state
                    if (this.selectedPhotos.length > 0) {
                        formData.delete('photographs[]');
                        this.selectedPhotos.forEach(item => {
                            if (item.file) {
                                formData.append('photographs[]', item.file);
                            }
                        });
                    }

                    // Ensure selected video is attached from Alpine state
                    if (this.selectedVideo && this.selectedVideo.file) {
                        formData.set('video', this.selectedVideo.file);
                    }

                    this.isSubmitting = true;
                    this.uploadPercent = 0;
                    this.uploadedBytesText = '';
                    this.totalBytesText = '';
                    this.uploadStatusText = this.selectedVideo ? 'Uploading walkthrough video & photos...' : 'Uploading service details...';

                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', form.action, true);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('Accept', 'application/json');

                    xhr.upload.onprogress = (e) => {
                        if (e.lengthComputable) {
                            const percent = Math.min(99, Math.round((e.loaded / e.total) * 100));
                            this.uploadPercent = percent;
                            this.uploadedBytesText = this.formatBytes(e.loaded);
                            this.totalBytesText = this.formatBytes(e.total);

                            if (percent < 100) {
                                this.uploadStatusText = `Uploading media files (${this.uploadedBytesText} / ${this.totalBytesText})...`;
                            } else {
                                this.uploadStatusText = 'Upload complete! Processing service request on server...';
                            }
                        }
                    };

                    xhr.onload = () => {
                        if (xhr.status >= 200 && xhr.status < 300) {
                            this.uploadPercent = 100;
                            this.uploadStatusText = 'Request submitted successfully!';

                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Request Submitted Successfully!',
                                    text: 'Thank you! Your service quote request and media have been received. Our team will review your inquiry and prepare a quote shortly.',
                                    confirmButtonText: 'Done',
                                    confirmButtonColor: '#C5A059',
                                    allowOutsideClick: false,
                                }).then(() => {
                                    window.location.href = "{{ route('contact') }}?submitted=1";
                                });
                            } else {
                                setTimeout(() => {
                                    window.location.href = "{{ route('contact') }}?submitted=1";
                                }, 800);
                            }
                        } else {
                            this.isSubmitting = false;
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.errors) {
                                    const firstKey = Object.keys(response.errors)[0];
                                    this.formErrorMessage = response.errors[firstKey][0];
                                } else if (response.message) {
                                    this.formErrorMessage = response.message;
                                } else {
                                    this.formErrorMessage = 'An error occurred during submission. Please try again.';
                                }
                            } catch (err) {
                                if (xhr.status === 413) {
                                    this.formErrorMessage = 'Uploaded payload exceeds server capacity. Please attach a smaller video file (under 200MB) or restart the web server.';
                                } else {
                                    this.formErrorMessage = 'Server encountered an issue (' + xhr.status + '). Please try again or contact support.';
                                }
                            }

                            if (typeof Swal !== 'undefined' && this.formErrorMessage) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Submission Failed',
                                    text: this.formErrorMessage,
                                    confirmButtonColor: '#C5A059'
                                });
                            }

                            // Smooth scroll to error
                            window.scrollTo({ top: 150, behavior: 'smooth' });
                        }
                    };

                    xhr.onerror = () => {
                        this.isSubmitting = false;
                        this.formErrorMessage = 'Network connection interrupted during upload. Please check your internet and try again.';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Network Error',
                                text: this.formErrorMessage,
                                confirmButtonColor: '#C5A059'
                            });
                        }
                        window.scrollTo({ top: 150, behavior: 'smooth' });
                    };

                    xhr.send(formData);
                }
            };
        }
    </script>

    @if (request('submitted'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Service Request Received!',
                        text: 'Your quote request has been registered in our system. You can track updates and quotes from your dashboard.',
                        confirmButtonText: 'Great!',
                        confirmButtonColor: '#C5A059'
                    });
                }
            });
        </script>
    @endif

</x-website.layout>
