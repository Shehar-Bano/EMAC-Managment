<x-dashboard.layout :title="'Edit Security Role — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <a href="{{ route('dashboard.roles.index') }}" class="hover:text-[#C5A059]">Security Roles</a>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">Edit {{ $role->name }}</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 flex items-center gap-3">
                Edit Role: {{ $role->name }}
                @if ($role->is_system)
                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#C5A059]/15 text-[#8F6B20] border border-[#C5A059]/30">System Protected</span>
                @endif
            </h1>
            <p class="text-xs text-slate-500 mt-1">Modify role parameters and update module permission assignments</p>
        </div>

        <div>
            <x-button href="{{ route('dashboard.roles.index') }}" variant="secondary" size="sm">
                &larr; Back to Roles
            </x-button>
        </div>
    </x-slot:header>

    <form method="POST" action="{{ route('dashboard.roles.update', $role) }}" class="space-y-6 max-w-5xl">
        @csrf
        @method('PUT')

        {{-- Basic Role Info Card --}}
        <x-card title="Role Identification" subtitle="Primary role name and functional objective">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-input
                    label="Role Name"
                    name="name"
                    :value="$role->name"
                    placeholder="e.g. Finance Auditor"
                    required
                    autofocus
                />

                <x-input
                    label="Description"
                    name="description"
                    :value="$role->description"
                    placeholder="Brief explanation of duties and access level..."
                />
            </div>
        </x-card>

        {{-- Permission Groups Matrix Card --}}
        <x-card title="Permission Groups & Granular Actions" subtitle="Configure allowed actions for members holding this role">
            @php
                $assignedPermissionIds = old('permissions', $role->permissions->pluck('id')->toArray());
            @endphp

            <div class="space-y-6">
                @foreach ($permissionGroups as $group)
                    <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50">
                        <div class="group-container">
                            {{-- Group Header with Toggle All --}}
                            <div class="flex items-center justify-between pb-3.5 border-b border-slate-200 mb-4">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-lg bg-[#C5A059]/15 text-[#8F6B20] border border-[#C5A059]/30 flex items-center justify-center font-bold text-xs">
                                        {{ $loop->iteration }}
                                    </span>
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900">{{ $group->name }}</h4>
                                        <p class="text-[11px] text-slate-500">{{ $group->description }}</p>
                                    </div>
                                </div>
                                <label class="inline-flex items-center gap-2 text-xs font-bold text-[#8F6B20] hover:text-[#C5A059] cursor-pointer">
                                    <input
                                        type="checkbox"
                                        onchange="
                                            const checkboxes = this.closest('.group-container').querySelectorAll('.perm-checkbox');
                                            checkboxes.forEach(cb => cb.checked = this.checked);
                                        "
                                        class="rounded-sm border-slate-300 text-[#C5A059] focus:ring-[#C5A059] w-4 h-4 cursor-pointer"
                                    >
                                    <span>Select All in {{ $group->name }}</span>
                                </label>
                            </div>

                            {{-- Granular Permissions Grid --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach ($group->permissions as $permission)
                                    <label class="flex items-start gap-2.5 p-2.5 rounded-xl border border-slate-200/80 bg-white hover:border-[#C5A059] hover:bg-[#C5A059]/5 transition-colors cursor-pointer">
                                        <input
                                            type="checkbox"
                                            name="permissions[]"
                                            value="{{ $permission->id }}"
                                            {{ in_array($permission->id, $assignedPermissionIds) ? 'checked' : '' }}
                                            class="perm-checkbox mt-0.5 rounded-sm border-slate-300 text-[#C5A059] focus:ring-[#C5A059] w-4 h-4 cursor-pointer"
                                        >
                                        <div class="text-xs">
                                            <span class="font-bold text-slate-800 block">{{ $permission->label }}</span>
                                            <span class="text-[10px] text-slate-400 font-mono">{{ $permission->name }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <x-slot:footer>
                <div class="flex items-center justify-end gap-3 w-full">
                    <x-button href="{{ route('dashboard.roles.index') }}" variant="secondary">
                        Cancel
                    </x-button>
                    <x-button type="submit" variant="primary">
                        Save Role Updates
                    </x-button>
                </div>
            </x-slot:footer>
        </x-card>
    </form>

</x-dashboard.layout>
