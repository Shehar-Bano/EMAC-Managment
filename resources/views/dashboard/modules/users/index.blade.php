<x-dashboard.layout :title="'User Directory — EMAC Development ERP'">

    @php
        $typeTitles = [
            'customers' => [
                'title' => 'Customers',
                'tab_name' => 'Customers',
                'desc' => 'Manage mobile app registered customers, client accounts, and contact addresses',
                'btn' => 'Add Customer',
                'empty' => 'No customer accounts found',
            ],
            'technicians' => [
                'title' => 'Technicians',
                'tab_name' => 'Technicians',
                'desc' => 'Manage operational workforce, field technicians, service personnel, and staff allocations',
                'btn' => 'Add Technician',
                'empty' => 'No technician accounts found',
            ],
            'admins' => [
                'title' => 'Administrator Users',
                'tab_name' => 'Administrator Users',
                'desc' => 'Manage system administrators, security managers, and administrative governance',
                'btn' => 'Add Administrator',
                'empty' => 'No administrator accounts found',
            ],
            'all' => [
                'title' => 'All Users',
                'tab_name' => 'All Users',
                'desc' => 'Complete directory of all registered customers, employees, and administrators',
                'btn' => 'Add User',
                'empty' => 'No users found',
            ],
        ];

        $currentMeta = $typeTitles[$activeType] ?? $typeTitles['customers'];
    @endphp

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <a href="{{ route('dashboard.users.index') }}" class="hover:text-[#8F6B20]">User Management</a>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">User Directory</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">User Directory</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold font-mono
                    @if ($activeType === 'customers') bg-[#C5A059]/15 text-[#8F6B20] border border-[#C5A059]/30
                    @elseif ($activeType === 'technicians') bg-blue-100 text-blue-800 border border-blue-200
                    @elseif ($activeType === 'admins') bg-slate-900 text-[#D4AF37] border border-slate-700
                    @else bg-slate-100 text-slate-700 border border-slate-200 @endif
                ">
                    {{ $users->total() }} {{ $currentMeta['tab_name'] }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">{{ $currentMeta['desc'] }}</p>
        </div>

        <div class="flex items-center gap-2.5">
            @can('users.export')
                <x-button href="{{ route('dashboard.users.export', array_merge(request()->query(), ['type' => $activeType])) }}" variant="secondary" size="sm">
                    <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Export CSV
                </x-button>
            @endcan

            @can('users.create')
                <x-button href="{{ route('dashboard.users.create', ['type' => $activeType]) }}" variant="primary" size="sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    {{ $currentMeta['btn'] }}
                </x-button>
            @endcan
        </div>
    </x-slot:header>

    {{-- Three Segmented Tabs (Customers, Technicians, Administrator Users) --}}
    <div class="mb-6 bg-white p-2 rounded-2xl border border-slate-200/90 shadow-2xs">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
            {{-- Tab 1: Customers (App Registrations) --}}
            <a
                href="{{ route('dashboard.users.index', ['type' => 'customers']) }}"
                class="flex items-center justify-between px-4 py-3 rounded-xl transition-all {{ $activeType === 'customers' ? 'bg-gradient-to-r from-[#FAF6EE] to-[#F5EAD4] border-2 border-[#C5A059] shadow-xs' : 'hover:bg-slate-50 border border-transparent text-slate-600' }}"
            >
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ $activeType === 'customers' ? 'bg-[#C5A059] text-white shadow-2xs' : 'bg-slate-100 text-slate-500' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <div class="text-xs font-black {{ $activeType === 'customers' ? 'text-slate-950' : 'text-slate-700' }}">Customers</div>
                        <div class="text-[10px] text-slate-500">App Registrations</div>
                    </div>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold {{ $activeType === 'customers' ? 'bg-[#8F6B20] text-white' : 'bg-slate-100 text-slate-600' }}">
                    {{ $counts['customers'] ?? 0 }}
                </span>
            </a>

            {{-- Tab 2: Technicians (Employees) --}}
            <a
                href="{{ route('dashboard.users.index', ['type' => 'technicians']) }}"
                class="flex items-center justify-between px-4 py-3 rounded-xl transition-all {{ $activeType === 'technicians' ? 'bg-gradient-to-r from-[#FAF6EE] to-[#F5EAD4] border-2 border-[#C5A059] shadow-xs' : 'hover:bg-slate-50 border border-transparent text-slate-600' }}"
            >
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ $activeType === 'technicians' ? 'bg-[#C5A059] text-white shadow-2xs' : 'bg-slate-100 text-slate-500' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <div class="text-xs font-black {{ $activeType === 'technicians' ? 'text-slate-950' : 'text-slate-700' }}">Technicians</div>
                        <div class="text-[10px] text-slate-500">Field Employees & Staff</div>
                    </div>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold {{ $activeType === 'technicians' ? 'bg-[#8F6B20] text-white' : 'bg-slate-100 text-slate-600' }}">
                    {{ $counts['technicians'] ?? 0 }}
                </span>
            </a>

            {{-- Tab 3: Administrator Users (Admin) --}}
            <a
                href="{{ route('dashboard.users.index', ['type' => 'admins']) }}"
                class="flex items-center justify-between px-4 py-3 rounded-xl transition-all {{ $activeType === 'admins' ? 'bg-gradient-to-r from-[#FAF6EE] to-[#F5EAD4] border-2 border-[#C5A059] shadow-xs' : 'hover:bg-slate-50 border border-transparent text-slate-600' }}"
            >
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ $activeType === 'admins' ? 'bg-[#C5A059] text-white shadow-2xs' : 'bg-slate-100 text-slate-500' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <div class="text-xs font-black {{ $activeType === 'admins' ? 'text-slate-950' : 'text-slate-700' }}">Administrator Users</div>
                        <div class="text-[10px] text-slate-500">Root & System Admin</div>
                    </div>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold {{ $activeType === 'admins' ? 'bg-[#8F6B20] text-white' : 'bg-slate-100 text-slate-600' }}">
                    {{ $counts['admins'] ?? 0 }}
                </span>
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <x-filter-bar
        :action="route('dashboard.users.index', ['type' => $activeType])"
        :resetUrl="route('dashboard.users.index', ['type' => $activeType])"
        nameLabel="Name / Email / Phone"
        searchPlaceholder="Search {{ strtolower($currentMeta['title']) }}..."
        :hasStatus="true"
        :hasDates="true"
    >
        <input type="hidden" name="type" value="{{ $activeType }}">

        <x-slot:extraFilters>
            <div class="w-32 sm:w-36">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Role Filter</label>
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
                <span>accounts selected on this page</span>
            </div>
            <form id="bulk-delete-form" method="POST" action="{{ route('dashboard.users.bulk-delete') }}">
                @csrf
                @method('DELETE')
                <input type="hidden" name="type" value="{{ $activeType }}">
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
                'User Profile',
                'Location / Address',
                'Assigned Role',
                'Status',
                'Registered Date',
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
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-xl object-cover ring-1 ring-[#C5A059]/30 shadow-2xs shrink-0">
                            <div>
                                <a href="{{ route('dashboard.users.show', ['user' => $user, 'type' => $activeType]) }}" class="font-bold text-xs text-slate-900 hover:text-[#C5A059] transition-colors">
                                    {{ $user->name }}
                                </a>
                                <div class="text-[10px] text-slate-500 flex items-center gap-1.5 mt-0.5">
                                    <span>{{ $user->email }}</span>
                                    @if ($user->phone)
                                        <span>•</span>
                                        <span class="font-mono">{{ $user->phone }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- Primary Address --}}
                    <td class="px-3.5 py-2">
                        @php
                            $primaryAddr = $user->addresses->firstWhere('is_primary', true) ?? $user->addresses->first();
                        @endphp
                        @if ($primaryAddr)
                            <div class="text-xs text-slate-800 font-medium truncate max-w-[200px]" title="{{ $primaryAddr->address }}">
                                {{ $primaryAddr->city ? $primaryAddr->city . ', ' : '' }}{{ $primaryAddr->state ? $primaryAddr->state : $primaryAddr->country }}
                            </div>
                            <div class="text-[10px] text-slate-400 truncate max-w-[200px]">
                                {{ $primaryAddr->address }}
                            </div>
                        @else
                            <span class="text-slate-400 text-xs italic">No address added</span>
                        @endif
                    </td>

                    {{-- Roles --}}
                    <td class="px-3.5 py-2">
                        <div class="flex flex-wrap gap-1">
                            @forelse ($user->roles as $role)
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold
                                    @if ($role->slug === 'super-admin') bg-slate-900 text-[#D4AF37] border border-slate-700
                                    @elseif ($role->slug === 'technician') bg-blue-100 text-blue-800 border border-blue-200
                                    @elseif ($role->slug === 'customer') bg-[#C5A059]/15 text-[#8F6B20] border border-[#C5A059]/30
                                    @else bg-slate-100 text-slate-700 border border-slate-200 @endif
                                ">
                                    {{ $role->name }}
                                </span>
                            @empty
                                @if ($user->role)
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[11px] italic">No role</span>
                                @endif
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
                            <a href="{{ route('dashboard.users.show', ['user' => $user, 'type' => $activeType]) }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-slate-700 hover:bg-[#C5A059]/10 hover:text-[#B8903B] transition-colors">
                                <svg class="w-3.5 h-3.5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <span>View Profile</span>
                            </a>

                            @can('users.edit')
                                <a href="{{ route('dashboard.users.edit', ['user' => $user, 'type' => $activeType]) }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-slate-700 hover:bg-[#C5A059]/10 hover:text-[#B8903B] transition-colors">
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
                                    <form method="POST" action="{{ route('dashboard.users.destroy', ['user' => $user, 'type' => $activeType]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="button"
                                            data-confirm-delete="Are you sure you want to delete user account '{{ $user->name }}'? This will revoke all assigned roles."
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
                    <td colspan="7" class="px-4 py-10 text-center text-slate-400">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <p class="text-sm font-bold text-slate-700">{{ $currentMeta['empty'] }}</p>
                            <p class="text-xs text-slate-400 mt-1">Try resetting search filters or create a new user under this tab.</p>
                            @can('users.create')
                                <div class="mt-4">
                                    <x-button href="{{ route('dashboard.users.create', ['type' => $activeType]) }}" variant="primary" size="sm">
                                        + {{ $currentMeta['btn'] }}
                                    </x-button>
                                </div>
                            @endcan
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
