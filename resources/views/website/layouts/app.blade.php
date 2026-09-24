<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'EMAC Development, LLC. | Handyman & Plumbing Services' }}</title>
    <meta name="description" content="EMAC Development, LLC. Professional Handyman repairs, property maintenance, and certified plumbing services across Grand Cayman, Florida, and Jamaica.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col selection:bg-[#C5A059] selection:text-slate-950">

    {{-- Top Regional Utility Bar --}}
    <div class="bg-slate-900 text-slate-300 text-xs py-2 px-4 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-4 text-[11px]">
                <span class="font-semibold text-[#C5A059] uppercase tracking-wider">Service Markets:</span>
                <span class="flex items-center gap-3 text-slate-300">
                    <span>Grand Cayman</span>
                    <span class="text-slate-600">•</span>
                    <span>Florida</span>
                    <span class="text-slate-600">•</span>
                    <span>Jamaica</span>
                </span>
            </div>
            <div class="flex items-center gap-5 text-[11px]">
                <a href="mailto:info@emacdevelopment.com" class="hover:text-white transition-colors flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span>info@emacdevelopment.com</span>
                </a>
                <span class="text-slate-700 hidden sm:inline">|</span>
                <a href="tel:+18005550199" class="hover:text-white font-semibold transition-colors hidden sm:flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    <span>+1 (800) 555-0199</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Main Navbar --}}
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200 transition-all duration-200" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-18">
                {{-- Brand Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <x-logo :theme="'dark'" size="md" />
                </a>

                {{-- Desktop Navigation Links --}}
                <nav class="hidden lg:flex items-center gap-1">
                    <a href="{{ route('home') }}" class="px-3.5 py-2 rounded-lg text-xs font-bold transition-colors {{ request()->routeIs('home') ? 'text-[#8F6B20] bg-amber-50' : 'text-slate-700 hover:text-slate-950 hover:bg-slate-50' }}">
                        Home
                    </a>
                    <a href="{{ route('services') }}" class="px-3.5 py-2 rounded-lg text-xs font-bold transition-colors {{ request()->routeIs('services') ? 'text-[#8F6B20] bg-amber-50' : 'text-slate-700 hover:text-slate-950 hover:bg-slate-50' }}">
                        Services & Trades
                    </a>
                    <a href="{{ route('about') }}" class="px-3.5 py-2 rounded-lg text-xs font-bold transition-colors {{ request()->routeIs('about') ? 'text-[#8F6B20] bg-amber-50' : 'text-slate-700 hover:text-slate-950 hover:bg-slate-50' }}">
                        About Us
                    </a>
                    <a href="{{ route('faq') }}" class="px-3.5 py-2 rounded-lg text-xs font-bold transition-colors {{ request()->routeIs('faq') ? 'text-[#8F6B20] bg-amber-50' : 'text-slate-700 hover:text-slate-950 hover:bg-slate-50' }}">
                        FAQs
                    </a>
                    <a href="{{ route('contact') }}" class="px-3.5 py-2 rounded-lg text-xs font-bold transition-colors {{ request()->routeIs('contact') ? 'text-[#8F6B20] bg-amber-50' : 'text-slate-700 hover:text-slate-950 hover:bg-slate-50' }}">
                        Contact
                    </a>
                </nav>

                {{-- Right CTAs --}}
                <div class="hidden sm:flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-slate-900 bg-[#C5A059] hover:bg-[#b8934b] rounded-lg shadow-xs transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            <span>Dashboard</span>
                        </a>
                    @else
                        <a href="{{ route('contact') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-slate-900 bg-[#C5A059] hover:bg-[#b8934b] rounded-lg shadow-xs transition-colors">
                            <span>Request a Quote</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    @endauth
                </div>

                {{-- Mobile Menu Toggle --}}
                <div class="flex items-center lg:hidden">
                    <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors">
                        <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Drawer Menu --}}
        <div
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="lg:hidden border-b border-slate-200 bg-white px-4 pt-2 pb-6 space-y-1 shadow-lg"
            style="display: none;"
        >
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-800 hover:bg-slate-50">Home</a>
            <a href="{{ route('services') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-800 hover:bg-slate-50">Services & Trades</a>
            <a href="{{ route('about') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-800 hover:bg-slate-50">About Us</a>
            <a href="{{ route('faq') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-800 hover:bg-slate-50">FAQs</a>
            <a href="{{ route('contact') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-800 hover:bg-slate-50">Contact</a>
            <div class="pt-4 border-t border-slate-200 space-y-2">
                @auth
                    <a href="{{ route('dashboard.index') }}" class="block w-full text-center px-4 py-2.5 text-xs font-bold text-slate-900 bg-[#C5A059] rounded-lg">Open Dashboard</a>
                @else
                    <a href="{{ route('contact') }}" class="block w-full text-center px-4 py-2.5 text-xs font-bold text-slate-900 bg-[#C5A059] rounded-lg">Request a Quote</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Main Content Slot --}}
    <main class="grow">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="bg-slate-900 text-slate-400 border-t border-slate-800 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10">
                {{-- Brand Column --}}
                <div class="lg:col-span-2 space-y-4">
                    <x-logo :theme="'dark'" size="md" />
                    <p class="text-xs text-slate-400 max-w-sm leading-relaxed">
                        <strong>EMAC Development, LLC.</strong> delivers licensed handyman repairs, routine property maintenance, and certified plumbing solutions across Grand Cayman, Florida, and Jamaica.
                    </p>
                    <div class="flex items-center gap-3 pt-2 text-[11px] text-slate-300">
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Licensed & Insured
                        </span>
                        <span>•</span>
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Certified Plumbing
                        </span>
                    </div>
                </div>

                {{-- Handyman Services --}}
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-white mb-3">Handyman Services</h4>
                    <ul class="space-y-2 text-slate-400">
                        <li><a href="{{ route('services') }}" class="hover:text-white transition-colors">General repairs</a></li>
                        <li><a href="{{ route('services') }}" class="hover:text-white transition-colors">Drywall & Painting</a></li>
                        <li><a href="{{ route('services') }}" class="hover:text-white transition-colors">Door & Window repairs</a></li>
                        <li><a href="{{ route('services') }}" class="hover:text-white transition-colors">Pressure washing</a></li>
                        <li><a href="{{ route('services') }}" class="hover:text-white transition-colors">Property maintenance</a></li>
                    </ul>
                </div>

                {{-- Plumbing Services --}}
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-white mb-3">Plumbing Services</h4>
                    <ul class="space-y-2 text-slate-400">
                        <li><a href="{{ route('services') }}" class="hover:text-white transition-colors">Water heater replacement</a></li>
                        <li><a href="{{ route('services') }}" class="hover:text-white transition-colors">Faucet & Sink installation</a></li>
                        <li><a href="{{ route('services') }}" class="hover:text-white transition-colors">Toilet repair & replacement</a></li>
                        <li><a href="{{ route('services') }}" class="hover:text-white transition-colors">Pipe & Leak repair</a></li>
                        <li><a href="{{ route('services') }}" class="hover:text-white transition-colors">Drain issues & maintenance</a></li>
                    </ul>
                </div>

                {{-- Quick Links & Portals --}}
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-white mb-3">Company & Support</h4>
                    <ul class="space-y-2 text-slate-400">
                        <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">About EMAC</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition-colors">Request a Quote</a></li>
                        <li><a href="{{ route('faq') }}" class="hover:text-white transition-colors">FAQs & Support</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Customer & Employee Portal</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('terms') }}" class="hover:text-white transition-colors">Terms & Conditions</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] text-slate-500">
                <p>&copy; {{ date('Y') }} EMAC Development, LLC. All rights reserved.</p>
                <div class="flex items-center gap-6">
                    <a href="{{ route('privacy') }}" class="hover:text-slate-300">Privacy Policy</a>
                    <a href="{{ route('terms') }}" class="hover:text-slate-300">Terms of Service</a>
                    <a href="{{ route('contact') }}" class="hover:text-slate-300">Contact Us</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Flash Notifications --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof Swal !== 'undefined') {
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
                if (typeof Swal !== 'undefined') {
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
