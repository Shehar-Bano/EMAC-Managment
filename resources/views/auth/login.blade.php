<x-auth.layout :title="'Sign In to ERP Dashboard — EMAC Development'">
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">ERP Sign In</h1>
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
                value="{{ old('email', 'admin@emac.test') }}"
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
                    value="password"
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

    {{-- Quick Test Credentials Box --}}
    <div class="mt-6 pt-5 border-t border-slate-100 text-xs text-slate-500 space-y-2 bg-gradient-to-br from-[#FAF8F4] to-[#F5EFE6] p-4 rounded-xl border border-[#C5A059]/30">
        <div class="font-bold text-slate-800 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-[#8F6B20]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
            <span>Demo Role Logins (Password: <code class="bg-[#FAF8F4] px-1.5 py-0.5 rounded text-[#8F6B20] font-mono border border-[#C5A059]/30">password</code>)</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-1 font-mono text-[10.5px]">
            <div class="bg-white p-2.5 rounded-lg border border-slate-200/80 shadow-2xs">
                <span class="text-[#8F6B20] font-bold block">Super Admin:</span>
                <span class="text-slate-700 truncate block">admin@emac.test</span>
            </div>
            <div class="bg-white p-2.5 rounded-lg border border-slate-200/80 shadow-2xs">
                <span class="text-[#8F6B20] font-bold block">Technician:</span>
                <span class="text-slate-700 truncate block">technician@emac.test</span>
            </div>
            <div class="bg-white p-2.5 rounded-lg border border-slate-200/80 shadow-2xs">
                <span class="text-[#8F6B20] font-bold block">Customer:</span>
                <span class="text-slate-700 truncate block">customer@emac.test</span>
            </div>
        </div>
    </div>
</x-auth.layout>
