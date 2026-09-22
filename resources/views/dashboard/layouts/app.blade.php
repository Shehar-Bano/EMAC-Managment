<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'ERP Dashboard' }} — EMAC Management</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full font-sans antialiased text-slate-900 selection:bg-indigo-500 selection:text-white" x-data="{ sidebarOpen: false }">

    <div class="min-h-full flex flex-col">
        {{-- Sidebar Backdrop for Mobile --}}
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/80 z-40 lg:hidden"
            @click="sidebarOpen = false"
            style="display: none;"
        ></div>

        {{-- Sidebar Component --}}
        @include('dashboard.layouts.sidebar')

        {{-- Main Content Area --}}
        <div class="lg:pl-64 flex flex-col flex-1 min-h-screen">
            {{-- Top Header --}}
            @include('dashboard.layouts.header', ['breadcrumbs' => $breadcrumbs ?? null])

            {{-- Main Page Content --}}
            <main class="grow p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto space-y-6">
                    {{-- Standard Page Header Slot --}}
                    @if (isset($header))
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            {{ $header }}
                        </div>
                    @endif

                    {{-- Main Slot --}}
                    {{ $slot }}
                </div>
            </main>

            {{-- Footer --}}
            @include('dashboard.layouts.footer')
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
    @if (session('warning'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (window.ERP) window.ERP.showWarning(@json(session('warning')));
            });
        </script>
    @endif
    @if (session('info'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (window.ERP) window.ERP.showInfo(@json(session('info')));
            });
        </script>
    @endif

    @stack('scripts')
</body>
</html>
