@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'hint' => null,
    'icon' => null,
])

@php
    $hasError = $name && $errors->has($name);
    $inputValue = old($name, $value);
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
        @if ($icon)
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-400">
                {!! $icon !!}
            </div>
        @endif

        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ $inputValue }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->merge([
                'class' => 'block w-full rounded-lg border text-xs transition-all duration-150 ' .
                    ($icon ? 'pl-8 ' : 'pl-3 ') .
                    'pr-3 py-1.5 ' .
                    ($hasError
                        ? 'border-rose-300 text-rose-900 placeholder-rose-300 focus:border-rose-500 focus:ring-rose-500 bg-rose-50/20'
                        : 'border-slate-300 text-slate-900 placeholder-slate-400 focus:border-[#C5A059] focus:ring-[#C5A059] bg-white hover:border-slate-400') .
                    ($disabled ? ' bg-slate-100 cursor-not-allowed opacity-75' : '')
            ]) }}
        >
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
