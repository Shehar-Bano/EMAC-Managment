<x-dashboard.layout :title="'Client Inquiries & Leads — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">Leads & Inquiries</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Website Inquiries & Service Leads</h1>
            <p class="text-xs text-slate-500 mt-1">Manage inbound consultation requests and service inquiries from Florida, Jamaica & Cayman Islands</p>
        </div>

        <div class="flex items-center gap-2.5">
            <x-button href="{{ route('dashboard.inquiries.export', request()->query()) }}" variant="secondary" size="sm">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export Leads CSV
            </x-button>
        </div>
    </x-slot:header>

    {{-- Quick Status Metrics --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5 mb-4">
        <a href="{{ route('dashboard.inquiries.index') }}" class="p-2.5 rounded-xl bg-white border border-slate-200/80 shadow-2xs hover:border-[#C5A059] transition-all">
            <div class="text-[10px] font-bold uppercase text-slate-400">Total Leads</div>
            <div class="text-lg font-extrabold text-slate-900 mt-0.5">{{ $stats['total'] }}</div>
        </a>
        <a href="{{ route('dashboard.inquiries.index', ['status' => 'new']) }}" class="p-2.5 rounded-xl bg-white border border-amber-200/80 shadow-2xs hover:border-amber-400 transition-all">
            <div class="text-[10px] font-bold uppercase text-amber-600">New Unread</div>
            <div class="text-lg font-extrabold text-amber-600 mt-0.5">{{ $stats['new'] }}</div>
        </a>
        <a href="{{ route('dashboard.inquiries.index', ['status' => 'contacted']) }}" class="p-2.5 rounded-xl bg-white border border-blue-200/80 shadow-2xs hover:border-blue-400 transition-all">
            <div class="text-[10px] font-bold uppercase text-blue-600">Contacted</div>
            <div class="text-lg font-extrabold text-blue-600 mt-0.5">{{ $stats['contacted'] }}</div>
        </a>
        <a href="{{ route('dashboard.inquiries.index', ['status' => 'in_progress']) }}" class="p-2.5 rounded-xl bg-white border border-purple-200/80 shadow-2xs hover:border-purple-400 transition-all">
            <div class="text-[10px] font-bold uppercase text-purple-600">In Progress</div>
            <div class="text-lg font-extrabold text-purple-600 mt-0.5">{{ $stats['in_progress'] }}</div>
        </a>
        <a href="{{ route('dashboard.inquiries.index', ['status' => 'closed']) }}" class="p-2.5 rounded-xl bg-white border border-emerald-200/80 shadow-2xs hover:border-emerald-400 transition-all">
            <div class="text-[10px] font-bold uppercase text-emerald-600">Closed / Won</div>
            <div class="text-lg font-extrabold text-emerald-600 mt-0.5">{{ $stats['closed'] }}</div>
        </a>
    </div>

    {{-- Filter Bar --}}
    <x-filter-bar
        :action="route('dashboard.inquiries.index')"
        :resetUrl="route('dashboard.inquiries.index')"
        nameLabel="Client Name"
        searchPlaceholder="Search leads & inquiries..."
        :hasStatus="true"
        :hasDates="true"
        :statusOptions="[
            'all' => 'All Statuses',
            'new' => 'New',
            'contacted' => 'Contacted',
            'in_progress' => 'In Progress',
            'closed' => 'Closed',
        ]"
    >
        <x-slot:extraFilters>
            <div class="w-32 sm:w-36">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Market</label>
                <select
                    name="market"
                    class="w-full px-2.5 py-1.5 text-xs bg-slate-50/60 border border-slate-300 rounded-lg focus:bg-white focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] transition-colors text-slate-700"
                >
                    <option value="all">All Markets</option>
                    <option value="Cayman Islands" {{ request('market') == 'Cayman Islands' ? 'selected' : '' }}>🇰🇾 Cayman</option>
                    <option value="Florida" {{ request('market') == 'Florida' ? 'selected' : '' }}>🇺🇸 Florida</option>
                    <option value="Jamaica" {{ request('market') == 'Jamaica' ? 'selected' : '' }}>🇯🇲 Jamaica</option>
                    <option value="General Inquiry" {{ request('market') == 'General Inquiry' ? 'selected' : '' }}>General</option>
                </select>
            </div>
        </x-slot:extraFilters>
    </x-filter-bar>

    {{-- Bulk Action Floating Toolbar --}}
    @can('inquiries.bulk-delete')
        <div class="bulk-action-bar hidden items-center justify-between p-3 mb-3 rounded-xl bg-white text-slate-900 shadow-xl transition-all animate-fade-in border-2 border-[#C5A059]">
            <div class="flex items-center gap-2.5 text-xs font-semibold">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gradient-to-r from-[#C5A059] to-[#D4AF37] text-slate-950 text-[11px] font-bold selected-count-badge">0</span>
                <span>inquiry(ies) selected on this page</span>
            </div>
            <form id="bulk-delete-form" method="POST" action="{{ route('dashboard.inquiries.bulk-delete') }}">
                @csrf
                @method('DELETE')
                <div id="bulk-hidden-inputs"></div>
                <button
                    type="button"
                    onclick="handleBulkDeleteSubmit()"
                    class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-lg bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition-colors cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Delete Selected
                </button>
            </form>
        </div>
    @endcan

    {{-- Inquiries Table Card --}}
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <x-table
            :headers="[
                'Sr. No',
                'Client / Contact',
                'Market & Division',
                'Message Preview',
                'Status',
                'Submitted',
                ['label' => 'Actions', 'align' => 'right']
            ]"
            :hasSelectAll="auth()->user()->can('inquiries.bulk-delete')"
        >
            @forelse ($inquiries as $inquiry)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    {{-- Checkbox --}}
                    @can('inquiries.bulk-delete')
                        <td class="w-10 px-3 py-2 text-center">
                            <input
                                type="checkbox"
                                name="selected_ids[]"
                                value="{{ $inquiry->id }}"
                                class="table-row-checkbox rounded-sm border-slate-300 text-[#C5A059] focus:ring-[#C5A059] w-3.5 h-3.5 cursor-pointer"
                            >
                        </td>
                    @endcan

                    {{-- Sr. No --}}
                    <td class="w-14 px-3.5 py-2 font-mono text-[11px] text-slate-500 font-semibold">
                        {{ $inquiries->firstItem() ? ($inquiries->firstItem() + $loop->index) : $loop->iteration }}
                    </td>

                    {{-- Client Name & Email --}}
                    <td class="px-3.5 py-2">
                        <div class="font-bold text-xs text-slate-900">
                            <a href="{{ route('dashboard.inquiries.show', $inquiry) }}" class="hover:text-[#C5A059] transition-colors">
                                {{ $inquiry->name }}
                            </a>
                        </div>
                        <div class="text-[10px] text-slate-500 font-mono">{{ $inquiry->email }}</div>
                        @if ($inquiry->phone)
                            <div class="text-[10px] text-slate-400 font-mono">{{ $inquiry->phone }}</div>
                        @endif
                    </td>

                    {{-- Market & Division --}}
                    <td class="px-3.5 py-2">
                        <div class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-800">
                            @if ($inquiry->market === 'Cayman Islands') 🇰🇾 @elseif($inquiry->market === 'Florida') 🇺🇸 @elseif($inquiry->market === 'Jamaica') 🇯🇲 @else 🌐 @endif
                            <span>{{ $inquiry->market }}</span>
                        </div>
                        @if ($inquiry->category)
                            <div class="text-[10px] text-[#8F6B20] font-medium mt-0.5">
                                {{ $inquiry->category->icon }} {{ $inquiry->category->name }}
                            </div>
                        @else
                            <div class="text-[10px] text-slate-400">General Consultation</div>
                        @endif
                    </td>

                    {{-- Message Preview --}}
                    <td class="px-3.5 py-2">
                        <p class="text-[11px] text-slate-600 line-clamp-2 max-w-sm">
                            {{ $inquiry->message }}
                        </p>
                    </td>

                    {{-- Status Badge & Quick Status Selector --}}
                    <td class="px-3.5 py-2">
                        <div class="inline-flex items-center gap-1.5">
                            @if ($inquiry->status === 'new')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">New</span>
                            @elseif ($inquiry->status === 'contacted')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-300">Contacted</span>
                            @elseif ($inquiry->status === 'in_progress')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-300">In Progress</span>
                            @elseif ($inquiry->status === 'closed')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">Closed</span>
                            @endif
                        </div>
                    </td>

                    {{-- Date --}}
                    <td class="px-3.5 py-2 text-[11px] font-mono text-slate-500 whitespace-nowrap">
                        {{ $inquiry->created_at->diffForHumans() }}
                    </td>

                    {{-- Actions --}}
                    <td class="px-3.5 py-2 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <a
                                href="{{ route('dashboard.inquiries.show', $inquiry) }}"
                                class="p-1 rounded-md text-slate-500 hover:text-[#8F6B20] hover:bg-[#FAF8F4] transition-colors"
                                title="View Lead"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>

                            @can('inquiries.delete')
                                <form method="POST" action="{{ route('dashboard.inquiries.destroy', $inquiry) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="button"
                                        data-confirm-delete="Delete inquiry from '{{ $inquiry->name }}'?"
                                        class="p-1 rounded-md text-rose-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                        title="Delete Lead"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-slate-400 text-xs">
                        No inquiries found matching criteria.
                    </td>
                </tr>
            @endforelse
        </x-table>

        <x-pagination :paginator="$inquiries" />
    </div>

    {{-- Bulk Delete Script Helper --}}
    @can('inquiries.bulk-delete')
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
