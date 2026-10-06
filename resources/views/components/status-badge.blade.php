@props([
    'status' => 'active',
    'size' => 'sm',
])

@php
    $normalized = strtolower(trim((string) $status));

    $variants = [
        // Success (Emerald)
        'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 border-emerald-200',
        'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 border-emerald-200',
        'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 border-emerald-200',
        'success' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 border-emerald-200',
        'paid' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 border-emerald-200',

        // Danger (Rose)
        'inactive' => 'bg-rose-50 text-rose-700 ring-rose-600/20 border-rose-200',
        'rejected' => 'bg-rose-50 text-rose-700 ring-rose-600/20 border-rose-200',
        'cancelled' => 'bg-rose-50 text-rose-700 ring-rose-600/20 border-rose-200',
        'failed' => 'bg-rose-50 text-rose-700 ring-rose-600/20 border-rose-200',

        // Warning (Amber)
        'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20 border-amber-200',
        'in_review' => 'bg-amber-50 text-amber-700 ring-amber-600/20 border-amber-200',
        'processing' => 'bg-amber-50 text-amber-700 ring-amber-600/20 border-amber-200',

        // Info (Sky)
        'info' => 'bg-sky-50 text-sky-700 ring-sky-600/20 border-sky-200',

        // Neutral (Slate)
        'draft' => 'bg-slate-50 text-slate-700 ring-slate-600/20 border-slate-200',
        'archived' => 'bg-slate-50 text-slate-700 ring-slate-600/20 border-slate-200',
    ];

    $classes = $variants[$normalized] ?? 'bg-slate-50 text-slate-700 ring-slate-600/20 border-slate-200';
    $label = ucwords(str_replace(['_', '-'], ' ', (string) $status));

    $dots = [
        'active' => 'bg-emerald-500',
        'approved' => 'bg-emerald-500',
        'completed' => 'bg-emerald-500',
        'success' => 'bg-emerald-500',
        'paid' => 'bg-emerald-500',
        'inactive' => 'bg-rose-500',
        'rejected' => 'bg-rose-500',
        'cancelled' => 'bg-rose-500',
        'failed' => 'bg-rose-500',
        'pending' => 'bg-amber-500',
        'in_review' => 'bg-amber-500',
        'processing' => 'bg-amber-500',
    ];
    $dotClass = $dots[$normalized] ?? 'bg-slate-400';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold ring-1 ring-inset border {$classes}"]) }}>
    <span class="w-1 h-1 rounded-full {{ $dotClass }}"></span>
    {{ $slot->isEmpty() ? $label : $slot }}
</span>
