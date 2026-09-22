<header class="sticky top-0 z-30 h-16 bg-white/95 backdrop-blur-md border-b border-slate-200/80 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
    {{-- Left: Mobile Toggle & Breadcrumbs --}}
    <div class="flex items-center gap-4">
        <button
            type="button"
            @click="sidebarOpen = true"
            class="lg:hidden p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>

        {{-- Breadcrumbs / Section Title --}}
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('dashboard.index') }}" class="font-semibold text-slate-700 hover:text-[#C5A059]">ERP</a>
            @if (isset($breadcrumbs))
                {{ $breadcrumbs }}
            @endif
        </div>
    </div>

    {{-- Right: System Status & User Profile Dropdown --}}
    <div class="flex items-center gap-4">
        {{-- System Health Pill --}}
        <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#FAF8F4] border border-[#C5A059]/30 text-slate-800 text-xs font-semibold shadow-2xs">
            <span class="w-2 h-2 rounded-full bg-[#C5A059] animate-pulse"></span>
            <span>EMAC Systems Operational</span>
        </div>

        {{-- User Profile Dropdown (Alpine) --}}
        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
            <button
                type="button"
                @click="open = !open"
                class="flex items-center gap-3 p-1.5 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer"
            >
                <img src="{{ auth()->user()?->avatar_url }}" alt="{{ auth()->user()?->name }}" class="w-8 h-8 rounded-lg object-cover ring-2 ring-[#C5A059]/40">
                <div class="hidden md:block text-left">
                    <div class="text-xs font-bold text-slate-800 leading-tight">{{ auth()->user()?->name }}</div>
                    <div class="text-[10px] text-[#B8903B] font-medium">{{ auth()->user()?->roles->pluck('name')->first() ?? 'Staff Member' }}</div>
                </div>
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 mt-2 w-56 rounded-2xl bg-white shadow-xl ring-1 ring-black/5 divide-y divide-slate-100 py-1.5 z-50 border border-slate-100"
                style="display: none;"
            >
                <div class="px-4 py-3">
                    <p class="text-xs font-bold text-slate-800">{{ auth()->user()?->name }}</p>
                    <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()?->email }}</p>
                    <span class="inline-block mt-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-[#C5A059]/15 text-[#8F6B20] border border-[#C5A059]/30">
                        {{ auth()->user()?->roles->pluck('name')->first() ?? 'Staff Member' }}
                    </span>
                </div>

                <div class="py-1">
                    <a href="{{ route('dashboard.settings.index') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-slate-700 hover:bg-[#C5A059]/10 hover:text-[#B8903B] transition-colors">
                        <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>Account Settings</span>
                    </a>
                </div>

                <div class="py-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            <span>Sign Out</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
