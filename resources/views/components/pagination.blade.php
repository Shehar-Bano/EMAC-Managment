@props([
    'paginator',
    'options' => [10, 20, 50, 100, 'all'],
])

@if ($paginator instanceof \Illuminate\Contracts\Pagination\Paginator)
    <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-200 rounded-b-xl flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
        {{-- Left: Record Summary & Per Page Dropdown --}}
        <div class="flex items-center gap-2 text-xs text-slate-600">
            <span>Show</span>
            <form method="GET" action="{{ url()->current() }}" class="inline-block" id="per-page-form">
                {{-- Preserve all current query parameters except per_page and page --}}
                @foreach (request()->except(['per_page', 'page']) as $key => $val)
                    @if (is_array($val))
                        @foreach ($val as $subVal)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $subVal }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endif
                @endforeach

                <select
                    name="per_page"
                    onchange="this.form.submit()"
                    class="bg-white border border-slate-300 text-slate-700 text-xs font-semibold rounded-lg px-2.5 py-1.5 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 cursor-pointer shadow-2xs"
                >
                    @foreach ($options as $opt)
                        @php
                            $val = strtolower((string) $opt);
                            $selected = request('per_page', '10') == $val ? 'selected' : '';
                        @endphp
                        <option value="{{ $val }}" {{ $selected }}>
                            {{ is_numeric($opt) ? $opt : ucfirst($opt) }}
                        </option>
                    @endforeach
                </select>
            </form>
            <span>entries</span>

            @if ($paginator instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <span class="text-slate-400">|</span>
                <span>
                    Showing <strong class="text-slate-800">{{ $paginator->firstItem() ?? 0 }}</strong> to <strong class="text-slate-800">{{ $paginator->lastItem() ?? 0 }}</strong> of <strong class="text-slate-800">{{ $paginator->total() }}</strong> records
                </span>
            @endif
        </div>

        {{-- Right: Pagination Links --}}
        <div>
            {{ $paginator->appends(request()->query())->links() }}
        </div>
    </div>
@endif
