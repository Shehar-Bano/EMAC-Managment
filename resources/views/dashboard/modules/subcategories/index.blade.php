<x-dashboard.layout :title="'Subcategories — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <a href="{{ route('dashboard.categories.index') }}" class="hover:text-[#C5A059]">Categories</a>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">Subcategories</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Specialized Subcategories</h1>
            <p class="text-xs text-slate-500 mt-1">Manage specialized service divisions, trades, and scopes organized under parent categories</p>
        </div>

        <div class="flex items-center gap-2.5">
            <x-button href="{{ route('dashboard.categories.index') }}" variant="secondary" size="sm">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                View Categories
            </x-button>

            <x-button href="{{ route('dashboard.subcategories.export', request()->query()) }}" variant="secondary" size="sm">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export CSV
            </x-button>

            @can('subcategories.create')
                <x-button href="{{ route('dashboard.subcategories.create') }}" variant="primary" size="sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Create Subcategory
                </x-button>
            @endcan
        </div>
    </x-slot:header>

    {{-- Filter Bar --}}
    <x-filter-bar
        :action="route('dashboard.subcategories.index')"
        :resetUrl="route('dashboard.subcategories.index')"
        searchPlaceholder="Search subcategories by name, slug or description..."
        :hasStatus="true"
    >
        <x-slot:extraFilters>
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1">Parent Category</label>
                <select
                    name="category_id"
                    class="w-full px-3 py-2 text-sm bg-slate-50/50 border border-slate-300 rounded-lg focus:bg-white focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] transition-colors"
                >
                    <option value="all">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </x-slot:extraFilters>
    </x-filter-bar>

    {{-- Bulk Action Floating Toolbar --}}
    @can('subcategories.bulk-delete')
        <div class="bulk-action-bar hidden items-center justify-between p-3.5 mb-4 rounded-xl bg-white text-slate-900 shadow-xl transition-all animate-fade-in border-2 border-[#C5A059]">
            <div class="flex items-center gap-3 text-xs font-semibold">
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gradient-to-r from-[#C5A059] to-[#D4AF37] text-slate-950 font-bold selected-count-badge">0</span>
                <span>subcategories selected on this page</span>
            </div>
            <form id="bulk-delete-form" method="POST" action="{{ route('dashboard.subcategories.bulk-delete') }}">
                @csrf
                @method('DELETE')
                <div id="bulk-hidden-inputs"></div>
                <button
                    type="button"
                    onclick="handleBulkDeleteSubmit()"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition-colors cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Delete Selected
                </button>
            </form>
        </div>
    @endcan

    {{-- Main Subcategories Table Card --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <x-table
            :headers="[
                'Sr. No',
                'Subcategory Name',
                'Parent Category',
                'Slug Identifier',
                'Status',
                ['label' => 'Actions', 'align' => 'right']
            ]"
            :hasSelectAll="auth()->user()->can('subcategories.bulk-delete')"
        >
            @forelse ($subcategories as $subcategory)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    {{-- Checkbox --}}
                    @can('subcategories.bulk-delete')
                        <td class="w-12 px-4 py-3.5 text-center">
                            <input
                                type="checkbox"
                                name="selected_ids[]"
                                value="{{ $subcategory->id }}"
                                class="table-row-checkbox rounded-sm border-slate-300 text-[#C5A059] focus:ring-[#C5A059] w-4 h-4 cursor-pointer"
                            >
                        </td>
                    @endcan

                    {{-- Sr. No --}}
                    <td class="w-16 px-6 py-4 font-mono text-xs text-slate-500 font-semibold">
                        {{ $subcategories->firstItem() ? ($subcategories->firstItem() + $loop->index) : $loop->iteration }}
                    </td>

                    {{-- Subcategory Name & Description --}}
                    <td class="px-6 py-4">
                        <div>
                            <a href="{{ route('dashboard.subcategories.show', $subcategory) }}" class="font-bold text-slate-900 hover:text-[#C5A059] transition-colors text-sm">
                                {{ $subcategory->name }}
                            </a>
                            @if ($subcategory->description)
                                <div class="text-[11px] text-slate-500 line-clamp-2 max-w-sm mt-0.5">{{ $subcategory->description }}</div>
                            @endif
                        </div>
                    </td>

                    {{-- Parent Category --}}
                    <td class="px-6 py-4">
                        @if ($subcategory->category)
                            <a
                                href="{{ route('dashboard.categories.show', $subcategory->category) }}"
                                class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#C5A059]/10 text-[#8F6B20] border border-[#C5A059]/25 hover:bg-[#C5A059]/20 transition-colors"
                            >
                                <span>{{ $subcategory->category->name }}</span>
                            </a>
                        @else
                            <span class="text-xs text-slate-400">Unassigned</span>
                        @endif
                    </td>

                    {{-- Slug --}}
                    <td class="px-6 py-4 text-xs font-mono text-slate-600">
                        {{ $subcategory->slug }}
                    </td>

                    {{-- Status Column --}}
                    <td class="px-6 py-4">
                        @can('subcategories.status')
                            <form method="POST" action="{{ route('dashboard.subcategories.toggle-status', $subcategory) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="cursor-pointer group" title="Click to toggle status">
                                    <x-status-badge :status="$subcategory->status" />
                                </button>
                            </form>
                        @else
                            <x-status-badge :status="$subcategory->status" />
                        @endcan
                    </td>

                    {{-- Action Dropdown Menu --}}
                    <td class="px-6 py-4 text-right">
                        <x-action-dropdown>
                            @can('subcategories.view')
                                <a href="{{ route('dashboard.subcategories.show', $subcategory) }}" class="flex items-center gap-2 px-4 py-2 text-xs text-slate-700 hover:bg-[#C5A059]/10 hover:text-[#B8903B] transition-colors">
                                    <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <span>View Subcategory</span>
                                </a>
                            @endcan

                            @can('subcategories.edit')
                                <a href="{{ route('dashboard.subcategories.edit', $subcategory) }}" class="flex items-center gap-2 px-4 py-2 text-xs text-slate-700 hover:bg-[#C5A059]/10 hover:text-[#B8903B] transition-colors">
                                    <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    <span>Edit Subcategory</span>
                                </a>
                            @endcan

                            @can('subcategories.delete')
                                <form method="POST" action="{{ route('dashboard.subcategories.destroy', $subcategory) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="button"
                                        data-confirm-delete="Are you sure you want to delete subcategory '{{ $subcategory->name }}'?"
                                        class="w-full flex items-center gap-2 px-4 py-2 text-xs text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer text-left font-medium"
                                    >
                                        <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        <span>Delete Subcategory</span>
                                    </button>
                                </form>
                            @endcan
                        </x-action-dropdown>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                        No subcategories found matching criteria.
                    </td>
                </tr>
            @endforelse
        </x-table>

        {{-- Dynamic Pagination --}}
        <x-pagination :paginator="$subcategories" />
    </div>

    {{-- Bulk Delete Script Helper --}}
    @can('subcategories.bulk-delete')
        <script>
            function handleBulkDeleteSubmit() {
                const checkedBoxes = document.querySelectorAll('.table-row-checkbox:checked');
                if (checkedBoxes.length === 0) return;

                const count = checkedBoxes.length;
                window.ERP.confirmBulkDelete(() => {
                    const hiddenContainer = document.getElementById('bulk-hidden-inputs');
                    hiddenContainer.innerHTML = '';
                    checkedBoxes.forEach(cb => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = cb.value;
                        hiddenContainer.appendChild(input);
                    });
                    document.getElementById('bulk-delete-form').submit();
                }, { count: count });
            }
        </script>
    @endcan

</x-dashboard.layout>
