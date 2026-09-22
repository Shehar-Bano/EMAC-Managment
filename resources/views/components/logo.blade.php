@props([
    'variant' => 'full', // 'full', 'icon', 'stacked'
    'theme' => 'dark',    // 'dark' (for light bg, text is dark) or 'light' (for dark bg, text is white)
    'size' => 'md',       // 'sm', 'md', 'lg', 'xl'
])

@php
    $iconSizes = [
        'sm' => 'w-8 h-8',
        'md' => 'w-10 h-10',
        'lg' => 'w-14 h-14',
        'xl' => 'w-20 h-20',
    ][$size] ?? 'w-10 h-10';

    $textSizes = [
        'sm' => 'text-xs',
        'md' => 'text-sm',
        'lg' => 'text-lg',
        'xl' => 'text-2xl',
    ][$size] ?? 'text-sm';

    $isLightBg = $theme === 'dark';
    $emacColor = $isLightBg ? 'text-[#8F6B20]' : 'text-white';
    $devColor = $isLightBg ? 'text-[#C5A059]' : 'text-[#D4AF37]';
@endphp

@if ($variant === 'stacked')
    <div {{ $attributes->merge(['class' => 'inline-flex flex-col items-center text-center select-none']) }}>
        {{-- Geometric Gold Emblem --}}
        <div class="{{ $iconSizes }} relative flex items-center justify-center my-0.5">
            <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full drop-shadow-xs">
                <defs>
                    <linearGradient id="emacGoldGradStacked" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#E5C158"/>
                        <stop offset="50%" stop-color="#C5A059"/>
                        <stop offset="100%" stop-color="#A57F2E"/>
                    </linearGradient>
                </defs>
                <path d="M40 18 H76 V82 H40 V18 Z" stroke="url(#emacGoldGradStacked)" stroke-width="4.5" stroke-linejoin="miter"/>
                <path d="M52 20 V34 H64 V20" stroke="url(#emacGoldGradStacked)" stroke-width="4" stroke-linejoin="miter"/>
                <path d="M52 44 H68 M52 53 H64 M52 62 H68 M52 44 V62" stroke="url(#emacGoldGradStacked)" stroke-width="4" stroke-linecap="square"/>
                <path d="M52 82 V74 H64 V82" stroke="url(#emacGoldGradStacked)" stroke-width="4" stroke-linejoin="miter"/>
                <path d="M22 46 L40 37 V82 H22 V46 Z" stroke="url(#emacGoldGradStacked)" stroke-width="4.5" stroke-linejoin="miter"/>
                <line x1="24" y1="55" x2="38" y2="48" stroke="url(#emacGoldGradStacked)" stroke-width="3.5"/>
                <line x1="24" y1="64" x2="38" y2="57" stroke="url(#emacGoldGradStacked)" stroke-width="3.5"/>
                <line x1="24" y1="73" x2="38" y2="66" stroke="url(#emacGoldGradStacked)" stroke-width="3.5"/>
            </svg>
        </div>

        <span class="{{ $textSizes }} font-extrabold tracking-[0.16em] uppercase mt-1 font-sans">
            <span class="{{ $emacColor }}">EMAC</span> <span class="{{ $devColor }}">DEVELOPMENT</span>
        </span>
    </div>
@else
    <div {{ $attributes->merge(['class' => 'inline-flex items-center gap-3 select-none']) }}>
        {{-- Geometric Gold Architectural Logo SVG --}}
        <div class="{{ $iconSizes }} shrink-0 relative flex items-center justify-center">
            <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full drop-shadow-xs">
                <defs>
                    <linearGradient id="emacGoldGradHoriz" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#E5C158"/>
                        <stop offset="50%" stop-color="#C5A059"/>
                        <stop offset="100%" stop-color="#A57F2E"/>
                    </linearGradient>
                </defs>
                <path d="M40 18 H76 V82 H40 V18 Z" stroke="url(#emacGoldGradHoriz)" stroke-width="4.5" stroke-linejoin="miter"/>
                <path d="M52 20 V34 H64 V20" stroke="url(#emacGoldGradHoriz)" stroke-width="4" stroke-linejoin="miter"/>
                <path d="M52 44 H68 M52 53 H64 M52 62 H68 M52 44 V62" stroke="url(#emacGoldGradHoriz)" stroke-width="4" stroke-linecap="square"/>
                <path d="M52 82 V74 H64 V82" stroke="url(#emacGoldGradHoriz)" stroke-width="4" stroke-linejoin="miter"/>
                <path d="M22 46 L40 37 V82 H22 V46 Z" stroke="url(#emacGoldGradHoriz)" stroke-width="4.5" stroke-linejoin="miter"/>
                <line x1="24" y1="55" x2="38" y2="48" stroke="url(#emacGoldGradHoriz)" stroke-width="3.5"/>
                <line x1="24" y1="64" x2="38" y2="57" stroke="url(#emacGoldGradHoriz)" stroke-width="3.5"/>
                <line x1="24" y1="73" x2="38" y2="66" stroke="url(#emacGoldGradHoriz)" stroke-width="3.5"/>
            </svg>
        </div>

        @if ($variant !== 'icon')
            <div class="flex items-center text-left">
                <span class="{{ $textSizes }} font-extrabold tracking-[0.14em] uppercase leading-tight font-sans">
                    <span class="{{ $emacColor }}">EMAC</span> <span class="{{ $devColor }}">DEVELOPMENT</span>
                </span>
            </div>
        @endif
    </div>
@endif
