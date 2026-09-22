@props([
    'headers' => [],
    'hasSelectAll' => false,
])

<div class="overflow-x-auto">
    <table {{ $attributes->merge(['class' => 'w-full text-left border-collapse text-sm text-slate-600']) }}>
        <thead class="bg-slate-50/80 border-b border-slate-200/80 text-xs font-semibold text-slate-500 uppercase tracking-wider">
            <tr>
                @if ($hasSelectAll)
                    <th scope="col" class="w-12 px-4 py-3.5 text-center">
                        <input
                            type="checkbox"
                            class="table-select-all rounded-sm border-slate-300 text-[#C5A059] focus:ring-[#C5A059] w-4 h-4 cursor-pointer"
                            title="Select all on this page"
                        >
                    </th>
                @endif

                @foreach ($headers as $header)
                    <th scope="col" class="px-6 py-3.5 font-semibold {{ is_array($header) && isset($header['align']) && $header['align'] === 'right' ? 'text-right' : (is_array($header) && isset($header['align']) && $header['align'] === 'center' ? 'text-center' : 'text-left') }}">
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
