<x-auth.layout :title="'Sign In to Dashboard — EMAC Development'">
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Sign In</h1>
        <p class="text-xs text-slate-500 mt-1">Enter your authorized credentials to access the administrative console</p>
    </div>

    {{-- Error Flash Alert --}}
    @if (session('status'))
        <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
        @csrf

        {{-- Email Address --}}
        <div>
            <x-input
                label="Email Address"
                name="email"
                type="email"
                value="{{ old('email') }}"
                placeholder="name@emac.test"
                required
                autofocus
            >
                <x-slot:icon>
                    <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                </x-slot:icon>
            </x-input>
        </div>

        {{-- Password --}}
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                    Password <span class="text-rose-500 font-bold">*</span>
                </label>
                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-[#B8903B] hover:text-[#C5A059] transition-colors">
                    Forgot Password?
                </a>
            </div>
            <div class="relative">
                <input
                    type="password"
                    name="password"
                    id="password"
                    required
                    class="block w-full rounded-lg border border-slate-300 text-sm pl-9 pr-3.5 py-2 text-slate-900 placeholder-slate-400 focus:border-[#C5A059] focus:ring-[#C5A059] bg-white transition-all"
                    placeholder="••••••••"
                >
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
            </div>
            @error('password')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center justify-between pt-1">
            <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-600">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded-sm border-slate-300 text-[#C5A059] focus:ring-[#C5A059] cursor-pointer">
                <span>Remember this workstation</span>
            </label>
        </div>

        {{-- Submit Button --}}
        <div class="pt-2">
            <x-button type="submit" variant="primary" class="w-full py-2.5">
                Sign In to Dashboard
            </x-button>
        </div>
    </form>
</x-auth.layout>
