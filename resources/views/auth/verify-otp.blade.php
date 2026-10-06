<x-auth.layout :title="'Verify Email Confirmation — EMAC Development'">
    <div class="mb-6 text-center">
        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-[#8F6B20] border border-[#C5A059]/30 flex items-center justify-center mx-auto mb-3 shadow-2xs">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Verify Your Email</h1>
        <p class="text-xs text-slate-500 mt-1">Please enter the 6-digit OTP confirmation code sent to your email address</p>
    </div>

    {{-- Error / Status Messages --}}
    @if (session('success'))
        <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs font-semibold text-rose-700">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('otp.verify') }}" class="space-y-4">
        @csrf

        {{-- Email Address Field --}}
        <div>
            <x-input
                label="Registered Email Address"
                name="email"
                type="email"
                value="{{ old('email', $email) }}"
                placeholder="name@domain.com"
                required
            >
                <x-slot:icon>
                    <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                </x-slot:icon>
            </x-input>
        </div>

        {{-- 6-Digit OTP Code --}}
        <div>
            <label for="otp" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                6-Digit Confirmation Code <span class="text-rose-500 font-bold">*</span>
            </label>
            <div class="relative">
                <input
                    type="text"
                    name="otp"
                    id="otp"
                    inputmode="numeric"
                    pattern="[0-9]{6}"
                    maxlength="6"
                    required
                    autofocus
                    class="block w-full rounded-xl border border-slate-300 text-center text-2xl tracking-[0.5em] font-mono font-black py-3 text-slate-900 placeholder-slate-300 focus:border-[#C5A059] focus:ring-[#C5A059] bg-white transition-all shadow-inner"
                    placeholder="••••••"
                >
            </div>
            @error('otp')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit Button --}}
        <div class="pt-2">
            <x-button type="submit" variant="primary" class="w-full py-2.5">
                Confirm OTP & Sign In
            </x-button>
        </div>
    </form>

    {{-- Resend OTP Section --}}
    <div class="mt-6 pt-5 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500" x-data="{ countdown: 60, timer: null }" x-init="timer = setInterval(() => { if (countdown > 0) countdown--; }, 1000)">
        <div>
            <span>Didn't receive code?</span>
            <form method="POST" action="{{ route('otp.resend') }}" class="inline ml-1">
                @csrf
                <input type="hidden" name="email" value="{{ old('email', $email) }}">
                <button
                    type="submit"
                    :disabled="countdown > 0"
                    :class="countdown > 0 ? 'text-slate-400 cursor-not-allowed opacity-60' : 'text-[#8F6B20] hover:text-[#C5A059] font-bold hover:underline cursor-pointer'"
                    class="transition-colors"
                >
                    Resend Code <span x-show="countdown > 0" x-text="'(' + countdown + 's)'"></span>
                </button>
            </form>
        </div>

        <a href="{{ route('login') }}" class="text-slate-600 hover:text-slate-900 font-semibold transition-colors">
            &larr; Back to Login
        </a>
    </div>
</x-auth.layout>
