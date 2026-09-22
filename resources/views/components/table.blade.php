@props([
    'headers' => [],
    'hasSelectAll' => false,
])

<div class="overflow-x-auto">
    <table {{ $attributes->merge(['class' => 'w-full text-left border-collapse text-xs text-slate-600']) }}>
        <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
            <tr>
                @if ($hasSelectAll)
                    <th scope="col" class="w-10 px-3 py-2.5 text-center">
                        <input
                            type="checkbox"
                            class="table-select-all rounded-sm border-slate-300 text-[#C5A059] focus:ring-[#C5A059] w-3.5 h-3.5 cursor-pointer"
                            title="Select all on this page"
                        >
                    </th>
                @endif

                @foreach ($headers as $header)
                    <th scope="col" class="px-3.5 py-2.5 font-bold {{ is_array($header) && isset($header['align']) && $header['align'] === 'right' ? 'text-right' : (is_array($header) && isset($header['align']) && $header['align'] === 'center' ? 'text-center' : 'text-left') }}">
                        {{ is_array($header) ? $header['label'] : $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
            {{ $slot }}
        </tbody>
    </table>
</div>
