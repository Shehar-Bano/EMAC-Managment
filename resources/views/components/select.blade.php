@props([
    'label' => null,
    'name' => null,
    'required' => false,
    'disabled' => false,
    'hint' => null,
    'placeholder' => null,
])

@php
    $hasError = $name && $errors->has($name);
@endphp

<div class="w-full">
    @if ($label)
        <label for="{{ $name }}" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
            {{ $label }}
            @if ($required)
                <span class="text-rose-500 font-bold ml-0.5">*</span>
            @endif
        </label>
    @endif

    <div class="relative rounded-lg shadow-2xs">
        <select
            name="{{ $name }}"
            id="{{ $name }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->merge([
                'class' => 'block w-full rounded-lg border text-xs transition-all duration-150 pl-3 pr-8 py-1.5 bg-white ' .
                    ($hasError
                        ? 'border-rose-300 text-rose-900 focus:border-rose-500 focus:ring-rose-500 bg-rose-50/20'
                        : 'border-slate-300 text-slate-900 focus:border-[#C5A059] focus:ring-[#C5A059] hover:border-slate-400') .
                    ($disabled ? ' bg-slate-100 cursor-not-allowed opacity-75' : '')
            ]) }}
        >
            @if ($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            {{ $slot }}
        </select>
    </div>

    @if ($hint && ! $hasError)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif

    @if ($hasError)
        <p class="mt-1 text-xs text-rose-600 font-medium flex items-center gap-1">
            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
            {{ $errors->first($name) }}
        </p>
    @endif
</div>
