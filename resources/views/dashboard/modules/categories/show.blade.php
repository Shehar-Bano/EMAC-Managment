<x-dashboard.layout :title="$category->name . ' — Category Details — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <a href="{{ route('dashboard.categories.index') }}" class="hover:text-[#C5A059]">Categories</a>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">{{ $category->name }}</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-[#FAF8F4] border border-[#C5A059]/30 flex items-center justify-center text-[#8F6B20] shrink-0 overflow-hidden font-bold text-xl ring-2 ring-[#C5A059]/40 shadow-sm">
                @if ($category->image_url)
                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                @elseif ($category->icon)
                    <span>{{ $category->icon }}</span>
                @else
                    <span>{{ substr($category->name, 0, 2) }}</span>
                @endif
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 flex items-center gap-3">
                    {{ $category->name }}
                    <x-status-badge :status="$category->status" />
                </h1>
                <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-2">
                    <span class="font-mono text-slate-600">slug: {{ $category->slug }}</span>
                    <span>•</span>
                    <span>{{ $category->subcategories->count() }} Subcategories</span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <x-button href="{{ route('dashboard.categories.index') }}" variant="secondary" size="sm">
                &larr; Back to Categories
            </x-button>

            @can('categories.edit')
                <x-button href="{{ route('dashboard.categories.edit', $category) }}" variant="primary" size="sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit Category
                </x-button>
            @endcan
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Category Details Card --}}
        <div class="lg:col-span-4 space-y-6">
            <x-card title="Category Overview" subtitle="Classification meta parameters">
                <dl class="divide-y divide-slate-100 text-xs">
                    <div class="py-3 flex justify-between">
                        <dt class="font-semibold text-slate-500">Publication Status</dt>
                        <dd><x-status-badge :status="$category->status" /></dd>
                    </div>
                    <div class="py-3 flex justify-between items-center">
                        <dt class="font-semibold text-slate-500">Visual Image</dt>
                        <dd>
                            @if ($category->image_url)
                                <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-14 h-14 rounded-xl object-cover ring-1 ring-[#C5A059]/50 shadow-2xs">
                            @else
                                <span class="text-xs text-slate-400 font-medium">None</span>
                            @endif
                        </dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-semibold text-slate-500">Created On</dt>
                        <dd class="font-mono text-slate-700">{{ $category->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-semibold text-slate-500">Last Modified</dt>
                        <dd class="font-mono text-slate-700">{{ $category->updated_at->diffForHumans() }}</dd>
                    </div>
                </dl>
            </x-card>

            @if ($category->description)
                <x-card title="Scope & Scope Summary">
                    <p class="text-xs text-slate-600 leading-relaxed">{{ $category->description }}</p>
                </x-card>
            @endif
        </div>

        {{-- Subcategories Section --}}
        <div class="lg:col-span-8 space-y-6">
            <x-card
                title="Associated Subcategories"
                subtitle="Specific sub-divisions and specialised services under this umbrella"
            >
                <x-slot:actions>
                    @can('subcategories.create')
                        <x-button href="{{ route('dashboard.subcategories.create', ['category_id' => $category->id]) }}" variant="primary" size="sm">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Subcategory
                        </x-button>
                    @endcan
                </x-slot:actions>

                <div class="divide-y divide-slate-100">
                    @forelse ($category->subcategories as $subcategory)
                        <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/50 rounded-xl px-2 transition-colors">
                            <div class="flex items-center gap-3.5 flex-1">
                                <div class="w-14 h-14 rounded-xl bg-[#FAF8F4] border border-[#C5A059]/30 ring-1 ring-[#C5A059]/20 flex items-center justify-center text-[#8F6B20] shrink-0 overflow-hidden font-bold text-sm shadow-xs">
                                    @if ($subcategory->image_url)
                                        <img src="{{ $subcategory->image_url }}" alt="{{ $subcategory->name }}" class="w-full h-full object-cover">
                                    @elseif ($subcategory->icon)
                                        <span>{{ $subcategory->icon }}</span>
                                    @else
                                        <span class="text-xs font-black text-[#8F6B20]">{{ strtoupper(substr($subcategory->name, 0, 2)) }}</span>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('dashboard.subcategories.show', $subcategory) }}" class="font-bold text-sm text-slate-900 hover:text-[#C5A059] transition-colors">
                                            {{ $subcategory->name }}
                                        </a>
                                        <x-status-badge :status="$subcategory->status" />
                                    </div>
                                    <div class="text-[11px] font-mono text-slate-500 mt-0.5">slug: {{ $subcategory->slug }}</div>
                                    @if ($subcategory->description)
                                        <p class="text-xs text-slate-600 mt-1 line-clamp-1">{{ $subcategory->description }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                @can('subcategories.edit')
                                    <a
                                        href="{{ route('dashboard.subcategories.edit', $subcategory) }}"
                                        class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700 hover:bg-[#C5A059]/15 hover:text-[#8F6B20] transition-colors"
                                    >
                                        Edit
                                    </a>
                                @endcan

                                @can('subcategories.delete')
                                    <form method="POST" action="{{ route('dashboard.subcategories.destroy', $subcategory) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="button"
                                            data-confirm-delete="Delete subcategory '{{ $subcategory->name }}'?"
                                            class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors cursor-pointer"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 text-slate-400 text-xs">
                            No subcategories registered yet under this category.
                        </div>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>

</x-dashboard.layout>
