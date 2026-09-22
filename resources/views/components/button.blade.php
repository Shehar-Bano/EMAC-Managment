@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'icon' => null,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold rounded-lg transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none cursor-pointer';

    $variants = [
        'primary' => 'bg-gradient-to-r from-[#D4AF37] via-[#C5A059] to-[#B8903B] hover:from-[#C5A059] hover:to-[#9E7B31] text-white shadow-sm shadow-[#C5A059]/20 focus:ring-[#C5A059] border border-[#D4AF37]/30',
        'secondary' => 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 shadow-2xs focus:ring-slate-400',
        'danger' => 'bg-rose-600 hover:bg-rose-700 text-white shadow-xs focus:ring-rose-500 border border-transparent',
        'success' => 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs focus:ring-emerald-500 border border-transparent',
        'warning' => 'bg-amber-500 hover:bg-amber-600 text-white shadow-xs focus:ring-amber-400 border border-transparent',
        'outline' => 'bg-transparent hover:bg-[#FAF3E0] text-[#B08945] hover:text-[#8F6C34] border border-[#C5A059]/50 focus:ring-[#C5A059]',
        'ghost' => 'bg-transparent hover:bg-slate-100 text-slate-600 hover:text-slate-900 border-transparent focus:ring-slate-300',
    ];

    $sizes = [
        'xs' => 'px-2 py-0.5 text-[10px] gap-1',
        'sm' => 'px-2.5 py-1 text-[11px] gap-1.5',
        'md' => 'px-3 py-1.5 text-xs gap-1.5',
        'lg' => 'px-3.5 py-2 text-xs gap-2',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <span class="shrink-0">{!! $icon !!}</span>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <span class="shrink-0">{!! $icon !!}</span>
        @endif
        {{ $slot }}
    </button>
@endif
