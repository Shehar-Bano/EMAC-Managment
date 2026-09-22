<x-dashboard.layout :title="$subcategory->name . ' — Subcategory Details — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <a href="{{ route('dashboard.categories.index') }}" class="hover:text-[#C5A059]">Categories</a>
        <span class="text-slate-400">/</span>
        <a href="{{ route('dashboard.subcategories.index') }}" class="hover:text-[#C5A059]">Subcategories</a>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">{{ $subcategory->name }}</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-[#FAF8F4] border border-[#C5A059]/30 flex items-center justify-center text-xl text-[#8F6B20] shrink-0 ring-2 ring-[#C5A059]/40 shadow-sm overflow-hidden font-bold">
                @if ($subcategory->image_url)
                    <img src="{{ $subcategory->image_url }}" alt="{{ $subcategory->name }}" class="w-full h-full object-cover">
                @elseif ($subcategory->icon)
                    <span>{{ $subcategory->icon }}</span>
                @else
                    <span class="text-sm font-black text-[#8F6B20]">{{ strtoupper(substr($subcategory->name, 0, 2)) }}</span>
                @endif
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 flex items-center gap-3">
                    {{ $subcategory->name }}
                    <x-status-badge :status="$subcategory->status" />
                </h1>
                <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-2">
                    <span class="font-mono text-slate-600">slug: {{ $subcategory->slug }}</span>
                    <span>•</span>
                    <span>Parent: {{ $subcategory->category?->name ?? 'Unassigned' }}</span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <x-button href="{{ route('dashboard.subcategories.index') }}" variant="secondary" size="sm">
                &larr; Back to Subcategories
            </x-button>

            @can('subcategories.edit')
                <x-button href="{{ route('dashboard.subcategories.edit', $subcategory) }}" variant="primary" size="sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit Subcategory
                </x-button>
            @endcan
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Metadata Card --}}
        <div class="lg:col-span-5 space-y-6">
            <x-card title="Subcategory Specification" subtitle="Classification parameters and system IDs">
                <dl class="divide-y divide-slate-100 text-xs">
                    <div class="py-3 flex justify-between">
                        <dt class="font-semibold text-slate-500">Parent Category</dt>
                        <dd>
                            @if ($subcategory->category)
                                <a href="{{ route('dashboard.categories.show', $subcategory->category) }}" class="font-bold text-[#8F6B20] hover:underline">
                                    {{ $subcategory->category->name }}
                                </a>
                            @else
                                <span class="text-slate-400">None</span>
                            @endif
                        </dd>
                    </div>
                    <div class="py-3 flex justify-between items-center">
                        <dt class="font-semibold text-slate-500">Visual Image</dt>
                        <dd>
                            @if ($subcategory->image_url)
                                <img src="{{ $subcategory->image_url }}" alt="{{ $subcategory->name }}" class="w-14 h-14 rounded-xl object-cover ring-1 ring-[#C5A059]/50 shadow-2xs">
                            @else
                                <span class="text-xs text-slate-400 font-medium">None</span>
                            @endif
                        </dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-semibold text-slate-500">Status</dt>
                        <dd><x-status-badge :status="$subcategory->status" /></dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-semibold text-slate-500">Created On</dt>
                        <dd class="font-mono text-slate-700">{{ $subcategory->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-semibold text-slate-500">Last Modified</dt>
                        <dd class="font-mono text-slate-700">{{ $subcategory->updated_at->diffForHumans() }}</dd>
                    </div>
                </dl>
            </x-card>
        </div>

        {{-- Description & Parent Category Card --}}
        <div class="lg:col-span-7 space-y-6">
            <x-card title="Scope Description" subtitle="Specific details and trade capabilities">
                @if ($subcategory->description)
                    <p class="text-xs text-slate-700 leading-relaxed">{{ $subcategory->description }}</p>
                @else
                    <p class="text-xs text-slate-400">No detailed scope description provided for this subcategory.</p>
                @endif
            </x-card>

            @if ($subcategory->category)
                <x-card title="Parent Category Summary" subtitle="Primary umbrella division">
                    <div class="flex items-center gap-3.5 p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <div class="w-14 h-14 rounded-xl bg-[#FAF8F4] border border-[#C5A059]/30 ring-1 ring-[#C5A059]/20 flex items-center justify-center text-lg text-[#8F6B20] overflow-hidden shrink-0 shadow-2xs">
                            @if ($subcategory->category->image_url)
                                <img src="{{ $subcategory->category->image_url }}" alt="{{ $subcategory->category->name }}" class="w-full h-full object-cover">
                            @elseif ($subcategory->category->icon)
                                <span>{{ $subcategory->category->icon }}</span>
                            @else
                                <span class="text-xs font-bold">🏛️</span>
                            @endif
                        </div>
                        <div>
                            <div class="font-bold text-sm text-slate-900">{{ $subcategory->category->name }}</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">{{ $subcategory->category->description ? Str::limit($subcategory->category->description, 110) : 'Standard division.' }}</div>
                        </div>
                    </div>
                </x-card>
            @endif
        </div>
    </div>

</x-dashboard.layout>
