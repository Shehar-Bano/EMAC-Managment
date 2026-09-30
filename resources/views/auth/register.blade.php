<x-auth.layout :title="'Create Customer Account — EMAC Development'" :maxWidth="'max-w-2xl'">
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Create Account</h1>
        <p class="text-xs text-slate-500 mt-1">Register to submit service requests, review quotations, and track scheduled maintenance</p>
    </div>

    {{-- Error / Status Messages --}}
    @if (session('error'))
        <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs font-semibold text-rose-700">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('register.post') }}" class="space-y-4" x-data="registerForm({
        regions: {{ Js::from($regions) }},
        selectedRegionId: '{{ old('region_id', '') }}',
        state: '{{ old('state', '') }}',
        country: '{{ old('country', '') }}',
        city: '{{ old('city', '') }}',
        zipcode: '{{ old('zipcode', '') }}',
    })">
        @csrf

        {{-- Row 1: Name & Email --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input
                    label="Full Name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    placeholder="e.g. John Doe"
                    required
                    autofocus
                >
                    <x-slot:icon>
                        <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </x-slot:icon>
                </x-input>
            </div>

            <div>
                <x-input
                    label="Email Address"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    placeholder="name@domain.com"
                    required
                >
                    <x-slot:icon>
                        <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                    </x-slot:icon>
                </x-input>
            </div>
        </div>

        {{-- Row 2: Phone & Dynamic Region --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input
                    label="Phone Number"
                    name="phone"
                    type="tel"
                    value="{{ old('phone') }}"
                    placeholder="+1 (555) 000-0000"
                    required
                >
                    <x-slot:icon>
                        <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    </x-slot:icon>
                </x-input>
            </div>

            <div>
                <label for="region_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Service Region / Market <span class="text-rose-500 font-bold">*</span>
                </label>
                <div class="relative">
                    <select
                        name="region_id"
                        id="region_id"
                        x-model="selectedRegionId"
                        @change="onRegionChange()"
                        required
                        class="block w-full rounded-lg border border-slate-300 text-sm pl-9 pr-8 py-2 text-slate-900 focus:border-[#C5A059] focus:ring-[#C5A059] bg-white transition-all appearance-none cursor-pointer"
                    >
                        <option value="">Select Service Territory</option>
                        @foreach ($regions as $region)
                            <option value="{{ $region->id }}">
                                {{ $region->name }} ({{ $region->currency }})
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
                @error('region_id')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Row 3: Street Address --}}
        <div>
            <x-input
                label="Street Address / Property Location"
                name="address"
                type="text"
                value="{{ old('address') }}"
                placeholder="e.g. 742 Evergreen Terrace, Apt 4B"
                required
            >
                <x-slot:icon>
                    <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                </x-slot:icon>
            </x-input>
        </div>

        {{-- Row 4: City, State, Zipcode & Country --}}
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-1">
                <label for="city" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    City <span class="text-rose-500 font-bold">*</span>
                </label>
                <input
                    type="text"
                    name="city"
                    id="city"
                    x-model="city"
                    required
                    placeholder="Miami / George Town"
                    class="block w-full rounded-lg border border-slate-300 text-sm px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-[#C5A059] focus:ring-[#C5A059] bg-white transition-all"
                >
                @error('city')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="sm:col-span-1">
                <label for="state" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    State / Parish
                </label>
                <input
                    type="text"
                    name="state"
                    id="state"
                    x-model="state"
                    placeholder="FL / Cayman"
                    class="block w-full rounded-lg border border-slate-300 text-sm px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-[#C5A059] focus:ring-[#C5A059] bg-white transition-all"
                >
                @error('state')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="sm:col-span-1">
                <label for="zipcode" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Zip / Postal <span class="text-rose-500 font-bold">*</span>
                </label>
                <input
                    type="text"
                    name="zipcode"
                    id="zipcode"
                    x-model="zipcode"
                    required
                    placeholder="33101 / KY1-1001"
                    class="block w-full rounded-lg border border-slate-300 text-sm px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-[#C5A059] focus:ring-[#C5A059] bg-white transition-all"
                >
                @error('zipcode')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="sm:col-span-1">
                <label for="country" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Country
                </label>
                <input
                    type="text"
                    name="country"
                    id="country"
                    x-model="country"
                    placeholder="Country"
                    class="block w-full rounded-lg border border-slate-300 text-sm px-3 py-2 text-slate-900 placeholder-slate-400 focus:border-[#C5A059] focus:ring-[#C5A059] bg-white transition-all"
                >
                @error('country')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Row 5: Password & Password Confirmation --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-data="{ showPass: false, showConfirm: false }">
            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Password <span class="text-rose-500 font-bold">*</span>
                </label>
                <div class="relative">
                    <input
                        type="password"
                        :type="showPass ? 'text' : 'password'"
                        name="password"
                        id="password"
                        required
                        class="block w-full rounded-lg border border-slate-300 text-sm pl-9 pr-10 py-2 text-slate-900 placeholder-slate-400 focus:border-[#C5A059] focus:ring-[#C5A059] bg-white transition-all"
                        placeholder="At least 8 characters"
                    >
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <button
                        type="button"
                        @click="showPass = !showPass"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer focus:outline-none"
                        tabindex="-1"
                    >
                        <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <svg x-show="showPass" x-cloak class="w-4 h-4 text-[#8F6B20]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                    Confirm Password <span class="text-rose-500 font-bold">*</span>
                </label>
                <div class="relative">
                    <input
                        type="password"
                        :type="showConfirm ? 'text' : 'password'"
                        name="password_confirmation"
                        id="password_confirmation"
                        required
                        class="block w-full rounded-lg border border-slate-300 text-sm pl-9 pr-10 py-2 text-slate-900 placeholder-slate-400 focus:border-[#C5A059] focus:ring-[#C5A059] bg-white transition-all"
                        placeholder="Re-type your password"
                    >
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <button
                        type="button"
                        @click="showConfirm = !showConfirm"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer focus:outline-none"
                        tabindex="-1"
                    >
                        <svg x-show="!showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <svg x-show="showConfirm" x-cloak class="w-4 h-4 text-[#8F6B20]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Legal Acceptances --}}
        <div class="pt-2 space-y-2">
            <label class="flex items-start gap-2.5 cursor-pointer text-xs text-slate-600">
                <input
                    type="checkbox"
                    name="terms_accepted"
                    value="1"
                    {{ old('terms_accepted') ? 'checked' : '' }}
                    required
                    class="mt-0.5 w-4 h-4 rounded-sm border-slate-300 text-[#C5A059] focus:ring-[#C5A059] cursor-pointer"
                >
                <span>
                    I agree to the <a href="{{ route('terms') }}" target="_blank" class="font-semibold text-[#8F6B20] hover:underline">Terms and Conditions</a> governing service estimates and property work.
                </span>
            </label>
            @error('terms_accepted')
                <p class="text-xs text-rose-600">{{ $message }}</p>
            @enderror

            <label class="flex items-start gap-2.5 cursor-pointer text-xs text-slate-600">
                <input
                    type="checkbox"
                    name="privacy_policy_accepted"
                    value="1"
                    {{ old('privacy_policy_accepted') ? 'checked' : '' }}
                    required
                    class="mt-0.5 w-4 h-4 rounded-sm border-slate-300 text-[#C5A059] focus:ring-[#C5A059] cursor-pointer"
                >
                <span>
                    I have read and consent to the <a href="{{ route('privacy') }}" target="_blank" class="font-semibold text-[#8F6B20] hover:underline">Privacy Policy</a> and communications policy.
                </span>
            </label>
            @error('privacy_policy_accepted')
                <p class="text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit Button --}}
        <div class="pt-3">
            <x-button type="submit" variant="primary" class="w-full py-2.5">
                Register & Send Confirmation OTP
            </x-button>
        </div>

        {{-- Return to Login Link --}}
        <div class="text-center pt-2 text-xs text-slate-500">
            <span>Already registered?</span>
            <a href="{{ route('login') }}" class="font-bold text-[#8F6B20] hover:text-[#C5A059] hover:underline ml-1">
                Sign In to Account
            </a>
        </div>
    </form>

    <script>
        function registerForm(config) {
            return {
                regions: config.regions || [],
                selectedRegionId: config.selectedRegionId || '',
                state: config.state || '',
                country: config.country || '',
                city: config.city || '',
                zipcode: config.zipcode || '',

                init() {
                    if (this.selectedRegionId && (!this.country || !this.state)) {
                        this.onRegionChange();
                    }
                },

                onRegionChange() {
                    const reg = this.regions.find(r => Number(r.id) === Number(this.selectedRegionId));
                    if (reg) {
                        if (reg.name.toLowerCase().includes('cayman')) {
                            this.country = 'Cayman Islands';
                            this.state = 'Grand Cayman';
                            if (!this.city) this.city = 'George Town';
                            if (!this.zipcode) this.zipcode = 'KY1-1102';
                        } else if (reg.name.toLowerCase().includes('florida')) {
                            this.country = 'United States';
                            this.state = 'Florida';
                            if (!this.city) this.city = 'Miami';
                            if (!this.zipcode) this.zipcode = '33101';
                        } else if (reg.name.toLowerCase().includes('jamaica')) {
                            this.country = 'Jamaica';
                            this.state = 'Kingston';
                            if (!this.city) this.city = 'Kingston';
                            if (!this.zipcode) this.zipcode = 'JMAKN01';
                        } else {
                            this.country = reg.country || 'USA';
                            this.state = reg.name;
                        }
                    }
                }
            };
        }
    </script>
</x-auth.layout>
