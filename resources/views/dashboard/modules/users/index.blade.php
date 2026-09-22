<x-dashboard.layout :title="'Employee Management — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">Employee Directory</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Employee Management</h1>
            <p class="text-xs text-slate-500 mt-1">Manage corporate personnel, security role assignments, and authentication status</p>
        </div>

        <div class="flex items-center gap-2.5">
            @can('users.export')
                <x-button href="{{ route('dashboard.users.export', request()->query()) }}" variant="secondary" size="sm">
                    <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export CSV
                </x-button>
            @endcan

            @can('users.create')
                <x-button href="{{ route('dashboard.users.create') }}" variant="primary" size="sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Employee
                </x-button>
            @endcan
        </div>
    </x-slot:header>

    {{-- Filter Bar --}}
    <x-filter-bar
        :action="route('dashboard.users.index')"
        :resetUrl="route('dashboard.users.index')"
        nameLabel="User Name / Email"
        searchPlaceholder="Search employees..."
        :hasStatus="true"
        :hasDates="true"
    >
        <x-slot:extraFilters>
            <div class="w-32 sm:w-36">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Role</label>
                <select
                    name="role_id"
                    class="w-full px-2.5 py-1.5 text-xs bg-slate-50/60 border border-slate-300 rounded-lg focus:bg-white focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] transition-colors text-slate-700"
                >
                    <option value="all">All Roles</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r->id }}" {{ request('role_id') == $r->id ? 'selected' : '' }}>
                            {{ $r->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </x-slot:extraFilters>
    </x-filter-bar>

    {{-- Bulk Action Floating Toolbar --}}
    @can('users.bulk-delete')
        <div class="bulk-action-bar hidden items-center justify-between p-3 mb-3 rounded-xl bg-white text-slate-900 shadow-xl transition-all animate-fade-in border-2 border-[#C5A059]">
            <div class="flex items-center gap-2.5 text-xs font-semibold">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gradient-to-r from-[#C5A059] to-[#D4AF37] text-slate-950 text-[11px] font-bold selected-count-badge">0</span>
                <span>personnel selected on this page</span>
            </div>
            <form id="bulk-delete-form" method="POST" action="{{ route('dashboard.users.bulk-delete') }}">
                @csrf
                @method('DELETE')
                <div id="bulk-hidden-inputs"></div>
                <button
                    type="button"
                    id="bulk-delete-btn"
                    onclick="handleBulkDeleteSubmit()"
                    class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-lg bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition-colors cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Delete Selected
                </button>
            </form>
        </div>
    @endcan

    {{-- Main Users Table Card --}}
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <x-table
            :headers="[
                'Sr. No',
                'Employee Profile',
                'Roles Assigned',
                'Status',
                'Joined Date',
                ['label' => 'Actions', 'align' => 'right']
            ]"
            :hasSelectAll="auth()->user()->can('users.bulk-delete')"
        >
            @forelse ($users as $user)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    {{-- Row Selection Checkbox --}}
                    @can('users.bulk-delete')
                        <td class="w-10 px-3 py-2 text-center">
                            @if (auth()->id() !== $user->id && ! ($user->isSuperAdmin() && $user->id === 1))
                                <input
                                    type="checkbox"
                                    name="selected_ids[]"
                                    value="{{ $user->id }}"
                                    class="table-row-checkbox rounded-sm border-slate-300 text-[#C5A059] focus:ring-[#C5A059] w-3.5 h-3.5 cursor-pointer"
                                >
                            @else
                                <span class="text-slate-300" title="Protected account">&mdash;</span>
                            @endif
                        </td>
                    @endcan

                    {{-- Sr. No --}}
                    <td class="w-14 px-3.5 py-2 font-mono text-[11px] text-slate-500 font-semibold">
                        {{ $users->firstItem() ? ($users->firstItem() + $loop->index) : $loop->iteration }}
                    </td>

                    {{-- Name & Email --}}
                    <td class="px-3.5 py-2">
                        <div class="flex items-center gap-2.5">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-7 h-7 rounded-lg object-cover ring-1 ring-[#C5A059]/30 shadow-2xs shrink-0">
                            <div>
                                <a href="{{ route('dashboard.users.show', $user) }}" class="font-bold text-xs text-slate-900 hover:text-[#C5A059] transition-colors">
                                    {{ $user->name }}
                                </a>
                                <div class="text-[10px] text-slate-500 flex items-center gap-1.5 mt-0.5">
                                    <span>{{ $user->email }}</span>
                                    @if ($user->phone)
                                        <span>•</span>
                                        <span>{{ $user->phone }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- Roles --}}
                    <td class="px-3.5 py-2">
                        <div class="flex flex-wrap gap-1">
                            @forelse ($user->roles as $role)
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $role->slug === 'super-admin' ? 'bg-slate-900 text-[#D4AF37]' : 'bg-[#C5A059]/10 text-[#8F6B20] border border-[#C5A059]/20' }}">
                                    {{ $role->name }}
                                </span>
                            @empty
                                <span class="text-slate-400 text-[11px] italic">No roles</span>
                            @endforelse
                        </div>
                    </td>

                    {{-- Status Badge --}}
                    <td class="px-3.5 py-2">
                        <x-status-badge :status="$user->status" />
                    </td>

                    {{-- Created At --}}
                    <td class="px-3.5 py-2 text-[11px] text-slate-500 font-mono">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>

                    {{-- Action Dropdown Menu --}}
                    <td class="px-3.5 py-2 text-right">
                        <x-action-dropdown>
                            <a href="{{ route('dashboard.users.show', $user) }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-slate-700 hover:bg-[#C5A059]/10 hover:text-[#B8903B] transition-colors">
                                <svg class="w-3.5 h-3.5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <span>View Profile</span>
                            </a>

                            @can('users.edit')
                                <a href="{{ route('dashboard.users.edit', $user) }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-slate-700 hover:bg-[#C5A059]/10 hover:text-[#B8903B] transition-colors">
                                    <svg class="w-3.5 h-3.5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    <span>Edit User</span>
                                </a>
                            @endcan

                            @can('users.status')
                                @if (auth()->id() !== $user->id)
                                    <form method="POST" action="{{ route('dashboard.users.status', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-slate-700 hover:bg-amber-50 hover:text-amber-600 transition-colors cursor-pointer text-left">
                                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                            <span>Toggle to {{ $user->status === 'active' ? 'Inactive' : 'Active' }}</span>
                                        </button>
                                    </form>
                                @endif
                            @endcan

                            @can('users.delete')
                                @if (auth()->id() !== $user->id && ! ($user->isSuperAdmin() && $user->id === 1))
                                    <form method="POST" action="{{ route('dashboard.users.destroy', $user) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="button"
                                            data-confirm-delete="Are you sure you want to delete user account '{{ $user->name }}'? This will revoke all role assignments."
                                            class="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer text-left font-medium"
                                        >
                                            <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            <span>Delete User</span>
                                        </button>
                                    </form>
                                @endif
                            @endcan
                        </x-action-dropdown>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-8 h-8 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <p class="text-xs font-semibold text-slate-700">No employees found</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Try resetting search filters or add a new user.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-table>

        {{-- Dynamic Pagination Component --}}
        <x-pagination :paginator="$users" />
    </div>

    {{-- Bulk Delete Script Helper --}}
    @can('users.bulk-delete')
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
