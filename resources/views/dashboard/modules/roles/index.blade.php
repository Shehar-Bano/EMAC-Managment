<x-dashboard.layout :title="'Security Roles — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">Security Roles</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Security Roles & Access</h1>
            <p class="text-xs text-slate-500 mt-1">Configure functional role permissions, access levels, and authorization boundaries</p>
        </div>

        <div class="flex items-center gap-2.5">
            @can('roles.view')
                <x-button href="{{ route('dashboard.permissions.index') }}" variant="secondary" size="sm">
                    <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    Permission Matrix
                </x-button>
            @endcan

            @can('roles.create')
                <x-button href="{{ route('dashboard.roles.create') }}" variant="primary" size="sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Create Security Role
                </x-button>
            @endcan
        </div>
    </x-slot:header>

    {{-- Filter Bar --}}
    <x-filter-bar
        :action="route('dashboard.roles.index')"
        :resetUrl="route('dashboard.roles.index')"
        searchPlaceholder="Search roles by name or description..."
        :hasStatus="false"
    />

    {{-- Main Roles Table Card --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <x-table
            :headers="[
                'Sr. No',
                'Role Name & Identifier',
                'Description',
                'Staff Assigned',
                'Permissions',
                'Role Type',
                ['label' => 'Actions', 'align' => 'right']
            ]"
        >
            @forelse ($roles as $role)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    {{-- Sr. No --}}
                    <td class="w-16 px-6 py-4 font-mono text-xs text-slate-500 font-semibold">
                        {{ $roles->firstItem() ? ($roles->firstItem() + $loop->index) : $loop->iteration }}
                    </td>

                    {{-- Name & Slug --}}
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-900">{{ $role->name }}</div>
                        <div class="text-[11px] font-mono text-[#8F6B20]">{{ $role->slug }}</div>
                    </td>

                    {{-- Description --}}
                    <td class="px-6 py-4 text-xs text-slate-600 max-w-sm">
                        {{ $role->description ?? 'No specific description.' }}
                    </td>

                    {{-- Assigned Users Count --}}
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                            {{ $role->users_count }} staff
                        </span>
                    </td>

                    {{-- Permissions Count --}}
                    <td class="px-6 py-4">
                        @if ($role->slug === 'super-admin')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-900 text-[#D4AF37] border border-[#C5A059]/40">
                                Full Access (Bypass)
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-[#C5A059]/10 text-[#8F6B20] border border-[#C5A059]/30">
                                {{ $role->permissions_count }} permissions
                            </span>
                        @endif
                    </td>

                    {{-- System Badge --}}
                    <td class="px-6 py-4">
                        @if ($role->is_system)
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#C5A059]/15 text-[#8F6B20] border border-[#C5A059]/30">System Protected</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-600">Custom Role</span>
                        @endif
                    </td>

                    {{-- Action Dropdown Menu --}}
                    <td class="px-6 py-4 text-right">
                        <x-action-dropdown>
                            @can('roles.edit')
                                <a href="{{ route('dashboard.roles.edit', $role) }}" class="flex items-center gap-2 px-4 py-2 text-xs text-slate-700 hover:bg-[#C5A059]/10 hover:text-[#B8903B] transition-colors">
                                    <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    <span>Edit Permissions</span>
                                </a>
                            @endcan

                            @can('roles.delete')
                                @if (! $role->is_system)
                                    <form method="POST" action="{{ route('dashboard.roles.destroy', $role) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="button"
                                            data-confirm-delete="Are you sure you want to delete role '{{ $role->name }}'? Ensure no staff members are actively using it."
                                            class="w-full flex items-center gap-2 px-4 py-2 text-xs text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer text-left font-medium"
                                        >
                                            <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            <span>Delete Role</span>
                                        </button>
                                    </form>
                                @endif
                            @endcan
                        </x-action-dropdown>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                        No security roles found.
                    </td>
                </tr>
            @endforelse
        </x-table>

        {{-- Pagination --}}
        <x-pagination :paginator="$roles" />
    </div>

</x-dashboard.layout>
