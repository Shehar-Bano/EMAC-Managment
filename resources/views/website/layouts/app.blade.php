<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'EMAC Development, LLC. | Construction, Design, Plumbing & Property Services' }}</title>
    <meta name="description" content="EMAC Development, LLC. Multi-disciplinary construction, architectural design, Cayman house plans, certified plumbing, and property maintenance across Florida, Jamaica, and the Cayman Islands.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FAF8F4] text-slate-800 font-sans antialiased min-h-screen flex flex-col selection:bg-[#C5A059] selection:text-slate-950 overflow-x-hidden">

    {{-- Top Regional Utility Bar --}}
    <div class="bg-[#F3EFE6] text-slate-600 text-xs py-2 px-4 border-b border-[#E5DFD3]">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-4 text-[11px] font-medium">
                <span class="inline-flex items-center gap-1.5 font-bold text-slate-800">
                    <span class="w-2 h-2 rounded-full bg-[#C5A059] animate-pulse"></span>
                    <span>REGIONAL SERVICE MARKETS:</span>
                </span>
                <span class="inline-flex items-center gap-2 text-slate-700 font-semibold">
                    <span class="hover:text-[#8F6B20] transition-colors">🇰🇾 Cayman Islands</span>
                    <span class="text-slate-300">•</span>
                    <span class="hover:text-[#8F6B20] transition-colors">🇺🇸 Florida</span>
                    <span class="text-slate-300">•</span>
                    <span class="hover:text-[#8F6B20] transition-colors">🇯🇲 Jamaica</span>
                </span>
            </div>
            <div class="flex items-center gap-4 text-[11px]">
                <a href="mailto:info@emacdevelopment.com" class="hover:text-[#8F6B20] transition-colors font-medium flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span>info@emacdevelopment.com</span>
                </a>
                <span class="text-slate-300 hidden sm:inline">•</span>
                <a href="tel:+18005550199" class="hover:text-[#8F6B20] transition-colors font-semibold hidden sm:flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    <span>+1 (800) 555-0199</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Interactive Sticky Glass Navigation Bar --}}
    <header class="sticky top-0 z-40 glass-header border-b border-slate-200/90 shadow-2xs transition-all duration-300" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 transition-all">
                {{-- Brand Logo --}}
                <a href="{{ route('home') }}" class="group flex items-center gap-3">
                    <x-logo :theme="'dark'" size="md" />
                </a>

                {{-- Desktop Navigation Links with Micro-Interaction Underline --}}
                <nav class="hidden lg:flex items-center gap-1.5">
                    <a href="{{ route('home') }}" class="relative px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('home') ? 'text-[#8F6B20] bg-[#C5A059]/15 border border-[#C5A059]/35' : 'text-slate-700 hover:text-[#8F6B20] hover:bg-slate-100/80' }}">
                        Home
                    </a>
                    <a href="{{ route('services') }}" class="relative px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('services') ? 'text-[#8F6B20] bg-[#C5A059]/15 border border-[#C5A059]/35' : 'text-slate-700 hover:text-[#8F6B20] hover:bg-slate-100/80' }}">
                        Services & Trades
                    </a>
                    <a href="{{ route('about') }}" class="relative px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('about') ? 'text-[#8F6B20] bg-[#C5A059]/15 border border-[#C5A059]/35' : 'text-slate-700 hover:text-[#8F6B20] hover:bg-slate-100/80' }}">
                        About EMAC
                    </a>
                    <a href="{{ route('faq') }}" class="relative px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('faq') ? 'text-[#8F6B20] bg-[#C5A059]/15 border border-[#C5A059]/35' : 'text-slate-700 hover:text-[#8F6B20] hover:bg-slate-100/80' }}">
                        FAQ & Support
                    </a>
                    <a href="{{ route('contact') }}" class="relative px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('contact') ? 'text-[#8F6B20] bg-[#C5A059]/15 border border-[#C5A059]/35' : 'text-slate-700 hover:text-[#8F6B20] hover:bg-slate-100/80' }}">
                        Contact & Quote
                    </a>
                </nav>

                {{-- Right CTAs --}}
                <div class="hidden sm:flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard.index') }}" class="btn-premium inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-slate-950 bg-gradient-to-r from-[#D4AF37] via-[#C5A059] to-[#B8903B] rounded-xl shadow-sm shadow-[#C5A059]/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            <span>ERP Dashboard</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-slate-700 hover:text-[#8F6B20] hover:bg-slate-100/80 rounded-xl transition-colors border border-slate-300 hover:border-[#C5A059]/50">
                            <svg class="w-3.5 h-3.5 text-[#8F6B20]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                            <span>Client / Staff Login</span>
                        </a>
                        <a href="{{ route('contact') }}" class="btn-premium inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-slate-950 bg-gradient-to-r from-[#D4AF37] via-[#C5A059] to-[#B8903B] rounded-xl shadow-sm shadow-[#C5A059]/30">
                            <span>Request Quote</span>
                            <svg class="w-3.5 h-3.5 arrow-slide" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    @endauth
                </div>

                {{-- Mobile Menu Toggle --}}
                <div class="flex items-center lg:hidden">
                    <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-colors">
                        <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Dropdown Menu with Smooth Transition --}}
        <div
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="opacity-0 -translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="lg:hidden border-b border-slate-200 bg-white/98 backdrop-blur-lg px-4 pt-2 pb-6 space-y-2 shadow-xl"
            style="display: none;"
        >
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-800 hover:bg-slate-100 hover:text-[#8F6B20] transition-colors">Home</a>
            <a href="{{ route('services') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-800 hover:bg-slate-100 hover:text-[#8F6B20] transition-colors">Services & Trades</a>
            <a href="{{ route('about') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-800 hover:bg-slate-100 hover:text-[#8F6B20] transition-colors">About EMAC</a>
            <a href="{{ route('faq') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-800 hover:bg-slate-100 hover:text-[#8F6B20] transition-colors">FAQ & Support</a>
            <a href="{{ route('contact') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold text-slate-800 hover:bg-slate-100 hover:text-[#8F6B20] transition-colors">Contact & Quote</a>
            <div class="pt-4 border-t border-slate-200 space-y-2">
                @auth
                    <a href="{{ route('dashboard.index') }}" class="btn-premium block w-full text-center px-4 py-2.5 text-xs font-bold text-slate-950 bg-gradient-to-r from-[#D4AF37] to-[#C5A059] rounded-xl">Open ERP Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="block w-full text-center px-4 py-2 text-xs font-semibold text-slate-800 bg-slate-100 rounded-xl">Client / Staff Login</a>
                    <a href="{{ route('contact') }}" class="btn-premium block w-full text-center px-4 py-2.5 text-xs font-bold text-slate-950 bg-gradient-to-r from-[#D4AF37] to-[#C5A059] rounded-xl">Request Service & Quote</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Main Content Slot --}}
    <main class="grow">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="bg-[#F4F0E6] text-slate-700 border-t border-[#E5DFD3] relative overflow-hidden">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#C5A05912_1px,transparent_1px),linear-gradient(to_bottom,#C5A05912_1px,transparent_1px)] bg-[size:3.5rem_3.5rem] pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12">
                {{-- Brand Column --}}
                <div class="lg:col-span-2 space-y-4">
                    <x-logo :theme="'dark'" size="lg" />
                    <p class="text-xs text-slate-600 max-w-sm leading-relaxed mt-3">
                        <strong>EMAC Development, LLC.</strong> provides comprehensive architectural design, Cayman house plans, licensed general contracting, certified plumbing, and property maintenance subscriptions across Florida, Jamaica, and the Cayman Islands.
                    </p>
                    <div class="flex flex-wrap items-center gap-2 pt-2">
                        <span class="text-[11px] px-2.5 py-1 bg-white rounded-md border border-[#C5A059]/40 text-[#8F6B20] font-bold shadow-2xs">Certified General Contractor</span>
                        <span class="text-[11px] px-2.5 py-1 bg-white rounded-md border border-[#C5A059]/40 text-[#8F6B20] font-bold shadow-2xs">Certified Plumbing Contractor</span>
                    </div>
                </div>

                {{-- Regional Markets --}}
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#8F6B20] mb-4">Service Markets</h4>
                    <ul class="space-y-2.5 text-xs">
                        <li><span class="font-bold text-slate-900">🇰🇾 Cayman Islands:</span> <span class="text-slate-600">House Plans, Architecture, Construction, Plumbing, Handyman</span></li>
                        <li><span class="font-bold text-slate-900">🇺🇸 Florida:</span> <span class="text-slate-600">General Contracting, Plumbing, Maintenance, Renovations</span></li>
                        <li><span class="font-bold text-slate-900">🇯🇲 Jamaica:</span> <span class="text-slate-600">Handyman, Property Maintenance, Renovations, Repairs</span></li>
                    </ul>
                </div>

                {{-- Core Services --}}
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#8F6B20] mb-4">Core Divisions</h4>
                    <ul class="space-y-2.5 text-xs">
                        <li><a href="{{ route('services') }}" class="hover:text-[#8F6B20] transition-colors">Architectural Design & Planning</a></li>
                        <li><a href="{{ route('services') }}#house-plans" class="hover:text-[#8F6B20] transition-colors">Cayman House Plans Store</a></li>
                        <li><a href="{{ route('services') }}" class="hover:text-[#8F6B20] transition-colors">General Construction & Builds</a></li>
                        <li><a href="{{ route('services') }}" class="hover:text-[#8F6B20] transition-colors">Certified Plumbing Solutions</a></li>
                        <li><a href="{{ route('services') }}" class="hover:text-[#8F6B20] transition-colors">Handyman & Subscriptions</a></li>
                    </ul>
                </div>

                {{-- Portals & Legal --}}
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#8F6B20] mb-4">Portals & Access</h4>
                    <ul class="space-y-2.5 text-xs">
                        <li><a href="{{ route('contact') }}" class="hover:text-[#8F6B20] transition-colors">Request Quote & Service</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-[#8F6B20] transition-colors">Customer & Staff Portal</a></li>
                        <li><a href="{{ route('faq') }}" class="hover:text-[#8F6B20] transition-colors">Frequently Asked Questions</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-[#8F6B20] transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('terms') }}" class="hover:text-[#8F6B20] transition-colors">Terms & Conditions</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-[#E5DFD3] flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} <strong>EMAC Development, LLC.</strong> All rights reserved.</p>
                <div class="flex items-center gap-6">
                    <a href="{{ route('privacy') }}" class="hover:text-[#8F6B20]">Privacy</a>
                    <a href="{{ route('terms') }}" class="hover:text-[#8F6B20]">Terms</a>
                    <a href="{{ route('contact') }}" class="hover:text-[#8F6B20]">Service Inquiry</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Global Flash Message SweetAlert Trigger --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (window.ERP) {
                    window.ERP.showSuccess(@json(session('success')));
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: @json(session('success')),
                        confirmButtonColor: '#C5A059'
                    });
                }
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (window.ERP) {
                    window.ERP.showError(@json(session('error')));
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: @json(session('error')),
                        confirmButtonColor: '#C5A059'
                    });
                }
            });
        </script>
    @endif
</body>
</html>
