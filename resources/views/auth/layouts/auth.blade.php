<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#FAF8F4]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Employee Authentication' }} — EMAC Development</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-[#FAF8F4] via-[#F5EFE6] to-[#FAF8F4] text-slate-900 font-sans antialiased min-h-screen flex items-center justify-center p-4 selection:bg-[#C5A059] selection:text-white relative overflow-hidden">

    {{-- Background Geometric Patterns & Subtle Warm Glow --}}
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#C5A05915_1px,transparent_1px),linear-gradient(to_bottom,#C5A05915_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_50%,#000_70%,transparent_100%)] opacity-40 pointer-events-none"></div>
    <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-[#C5A059]/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative w-full max-w-md my-8">
        {{-- Brand Header with Logo --}}
        <div class="text-center mb-8 flex justify-center">
            <a href="{{ route('home') }}" class="group">
                <x-logo :theme="'dark'" size="lg" />
            </a>
        </div>

        {{-- Main Container Card --}}
        <div class="bg-white text-slate-900 rounded-3xl p-8 sm:p-10 shadow-xl shadow-slate-200/60 border border-slate-200/80 relative">
            {{ $slot }}
        </div>

        {{-- Footer Links --}}
        <div class="mt-6 text-center text-xs text-slate-500 flex items-center justify-center gap-4">
            <a href="{{ route('home') }}" class="hover:text-[#8F6B20] transition-colors font-medium">&larr; Return to Public Website</a>
            <span>•</span>
            <span class="text-slate-500">EMAC Development ERP</span>
        </div>
    </div>

    {{-- Global Flash Message SweetAlert Trigger --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (window.ERP) window.ERP.showSuccess(@json(session('success')));
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (window.ERP) window.ERP.showError(@json(session('error')));
            });
        </script>
    @endif
</body>
</html>
