<x-auth.layout :title="'Reset Password — EMAC Development'">
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Reset Password</h1>
        <p class="text-xs text-slate-500 mt-1">Enter your registered email address and we'll send recovery instructions</p>
    </div>

    @if (session('status'))
        <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <x-input
                label="Registered Email"
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

        <div class="pt-2">
            <x-button type="submit" variant="primary" class="w-full py-2.5">
                Send Reset Link
            </x-button>
        </div>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-100 text-center text-xs text-slate-500">
        Remembered your credentials? <a href="{{ route('login') }}" class="font-semibold text-[#B8903B] hover:text-[#C5A059]">Back to Login</a>
    </div>
</x-auth.layout>
