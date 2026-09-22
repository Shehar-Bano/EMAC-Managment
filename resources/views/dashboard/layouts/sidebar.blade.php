<aside
    class="fixed inset-y-0 left-0 z-50 w-64 bg-white text-slate-700 flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 border-r border-slate-200/80 shadow-xs"
    :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
>
    {{-- Sidebar Brand --}}
    <div class="h-20 px-5 flex items-center justify-between border-b border-slate-100 shrink-0">
        <a href="{{ route('dashboard.index') }}" class="flex items-center gap-3 group">
            <x-logo :theme="'dark'" />
        </a>

        {{-- Mobile Close Button --}}
        <button
            type="button"
            @click="sidebarOpen = false"
            class="lg:hidden p-1.5 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    {{-- Navigation Menu --}}
    <div class="grow overflow-y-auto px-3 py-2 space-y-6">
        {{-- Section: Core --}}
        <div>
            <div class="px-3 text-[10px] font-bold uppercase tracking-wider text-[#8F6B20] mb-2">Operational Core</div>
            <div class="space-y-1">
                <a
                    href="{{ route('dashboard.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('dashboard.index') ? 'bg-gradient-to-r from-[#C5A059] to-[#D4AF37] text-slate-950 font-bold shadow-sm shadow-[#C5A059]/25' : 'text-slate-600 hover:bg-amber-50/60 hover:text-[#8F6B20]' }}"
                >
                    <svg class="w-4 h-4 shrink-0 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Dashboard Overview</span>
                </a>

                @can('inquiries.view')
                    @php
                        $unreadInquiriesCount = \App\Models\ContactInquiry::where('status', 'new')->count();
                    @endphp
                    <a
                        href="{{ route('dashboard.inquiries.index') }}"
                        class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('dashboard.inquiries.*') ? 'bg-gradient-to-r from-[#C5A059] to-[#D4AF37] text-slate-950 font-bold shadow-sm shadow-[#C5A059]/25' : 'text-slate-600 hover:bg-amber-50/60 hover:text-[#8F6B20]' }}"
                    >
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 shrink-0 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <span>Leads & Inquiries</span>
                        </div>
                        @if ($unreadInquiriesCount > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-slate-950 animate-pulse">
                                {{ $unreadInquiriesCount }}
                            </span>
                        @endif
                    </a>
                @endcan
            </div>
        </div>

        {{-- Section: Catalog & Classifications --}}
        <div>
            <div class="px-3 text-[10px] font-bold uppercase tracking-wider text-[#8F6B20] mb-2">Catalog & Categories</div>
            <div class="space-y-1">
                @can('categories.view')
                    <a
                        href="{{ route('dashboard.categories.index') }}"
                        class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('dashboard.categories.*') ? 'bg-gradient-to-r from-[#C5A059] to-[#D4AF37] text-slate-950 font-bold shadow-sm shadow-[#C5A059]/25' : 'text-slate-600 hover:bg-amber-50/60 hover:text-[#8F6B20]' }}"
                    >
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 shrink-0 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            <span>Categories</span>
                        </div>
                    </a>
                @endcan

                @can('subcategories.view')
                    <a
                        href="{{ route('dashboard.subcategories.index') }}"
                        class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('dashboard.subcategories.*') ? 'bg-gradient-to-r from-[#C5A059] to-[#D4AF37] text-slate-950 font-bold shadow-sm shadow-[#C5A059]/25' : 'text-slate-600 hover:bg-amber-50/60 hover:text-[#8F6B20]' }}"
                    >
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 shrink-0 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                            <span>Subcategories</span>
                        </div>
                    </a>
                @endcan
            </div>
        </div>

        {{-- Section: Staff & Directory --}}
        <div>
            <div class="px-3 text-[10px] font-bold uppercase tracking-wider text-[#8F6B20] mb-2">Staff & Directory</div>
            <div class="space-y-1">
                @can('users.view')
                    <a
                        href="{{ route('dashboard.users.index') }}"
                        class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('dashboard.users.*') ? 'bg-gradient-to-r from-[#C5A059] to-[#D4AF37] text-slate-950 font-bold shadow-sm shadow-[#C5A059]/25' : 'text-slate-600 hover:bg-amber-50/60 hover:text-[#8F6B20]' }}"
                    >
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 shrink-0 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <span>Users Directory</span>
                        </div>
                    </a>
                @endcan
            </div>
        </div>

        {{-- Section: Legal & Compliance --}}
        <div>
            <div class="px-3 text-[10px] font-bold uppercase tracking-wider text-[#8F6B20] mb-2">Legal & Compliance</div>
            <div class="space-y-1">
                @can('terms.view')
                    <a
                        href="{{ route('dashboard.terms.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('dashboard.terms.*') ? 'bg-gradient-to-r from-[#C5A059] to-[#D4AF37] text-slate-950 font-bold shadow-sm shadow-[#C5A059]/25' : 'text-slate-600 hover:bg-amber-50/60 hover:text-[#8F6B20]' }}"
                    >
                        <svg class="w-4 h-4 shrink-0 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Terms & Conditions</span>
                    </a>
                @endcan

                @can('privacy.view')
                    <a
                        href="{{ route('dashboard.privacy.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('dashboard.privacy.*') ? 'bg-gradient-to-r from-[#C5A059] to-[#D4AF37] text-slate-950 font-bold shadow-sm shadow-[#C5A059]/25' : 'text-slate-600 hover:bg-amber-50/60 hover:text-[#8F6B20]' }}"
                    >
                        <svg class="w-4 h-4 shrink-0 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <span>Privacy Policy</span>
                    </a>
                @endcan
            </div>
        </div>

        {{-- Section: Security & Access Control --}}
        <div>
            <div class="px-3 text-[10px] font-bold uppercase tracking-wider text-[#8F6B20] mb-2">Security & Governance</div>
            <div class="space-y-1">
                @can('roles.view')
                    <a
                        href="{{ route('dashboard.roles.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('dashboard.roles.*') ? 'bg-gradient-to-r from-[#C5A059] to-[#D4AF37] text-slate-950 font-bold shadow-sm shadow-[#C5A059]/25' : 'text-slate-600 hover:bg-amber-50/60 hover:text-[#8F6B20]' }}"
                    >
                        <svg class="w-4 h-4 shrink-0 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <span>Roles & Access</span>
                    </a>

                    <a
                        href="{{ route('dashboard.permissions.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('dashboard.permissions.*') ? 'bg-gradient-to-r from-[#C5A059] to-[#D4AF37] text-slate-950 font-bold shadow-sm shadow-[#C5A059]/25' : 'text-slate-600 hover:bg-amber-50/60 hover:text-[#8F6B20]' }}"
                    >
                        <svg class="w-4 h-4 shrink-0 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        <span>Permission Groups</span>
                    </a>
                @endcan

                @can('settings.view')
                    <a
                        href="{{ route('dashboard.settings.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('dashboard.settings.*') ? 'bg-gradient-to-r from-[#C5A059] to-[#D4AF37] text-slate-950 font-bold shadow-sm shadow-[#C5A059]/25' : 'text-slate-600 hover:bg-amber-50/60 hover:text-[#8F6B20]' }}"
                    >
                        <svg class="w-4 h-4 shrink-0 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>System Settings</span>
                    </a>
                @endcan
            </div>
        </div>
    </div>

    {{-- Sidebar Footer with Quick Return to Website --}}
    <div class="p-3 border-t border-slate-100 shrink-0">
        <a href="{{ route('home') }}" target="_blank" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-[#8F6B20] hover:bg-amber-50/60 transition-colors">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                <span>Public Website</span>
            </span>
            <svg class="w-3.5 h-3.5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
    </div>
</aside>
