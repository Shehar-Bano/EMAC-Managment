<x-dashboard.layout :title="'Dashboard — EMAC ERP'">

    <x-slot:header>
        <div>
            <h1 class="text-xl font-bold tracking-tight text-slate-900">Dashboard Overview</h1>
            <p class="text-xs text-slate-500 mt-0.5">User management, categories catalog, and role distribution.</p>
        </div>

        <div class="flex items-center gap-2">
            @can('users.create')
                <x-button href="{{ route('dashboard.users.create') }}" variant="primary" size="sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    New Employee
                </x-button>
            @endcan
        </div>
    </x-slot:header>

    {{-- KPI Metric Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        {{-- Card 1: Total Users --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-slate-500">Total Employees</span>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total_users'] }}</div>
                <div class="text-[11px] font-semibold text-emerald-600 mt-1 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    {{ $stats['active_users'] }} Active Employees
                </div>
            </div>
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-[#8F6B20] flex items-center justify-center shrink-0 border border-[#C5A059]/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
        </div>

        {{-- Card 2: Catalog Categories --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-slate-500">Active Categories</span>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total_categories'] }}</div>
                <div class="text-[11px] text-[#8F6B20] font-semibold mt-1">
                    {{ $stats['total_subcategories'] }} Subcategories / Trades
                </div>
            </div>
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-[#8F6B20] flex items-center justify-center shrink-0 border border-[#C5A059]/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
        </div>

        {{-- Card 3: Security Roles --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-slate-500">Security Roles</span>
                <div class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total_roles'] }}</div>
                <div class="text-[11px] text-slate-500 mt-1">{{ $stats['total_permissions'] }} Permissions Active</div>
            </div>
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-[#8F6B20] flex items-center justify-center shrink-0 border border-[#C5A059]/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
        </div>
    </div>

    {{-- Main Content 2-Column Split --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Recent Users Table --}}
        <div class="lg:col-span-8">
            <x-card title="Recently Registered Employees" subtitle="Latest personnel accounts registered in ERP" :noPadding="true">
                <x-slot:actions>
                    @can('users.view')
                        <a href="{{ route('dashboard.users.index') }}" class="text-xs font-semibold text-[#8F6B20] hover:text-[#ad8442] flex items-center gap-1">
                            <span>View All</span>
                            <span>&rarr;</span>
                        </a>
                    @endcan
                </x-slot:actions>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="px-3.5 py-2.5">Employee</th>
                                <th class="px-3.5 py-2.5">Assigned Roles</th>
                                <th class="px-3.5 py-2.5">Status</th>
                                <th class="px-3.5 py-2.5">Joined</th>
                                <th class="px-3.5 py-2.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600">
                            @forelse ($recentUsers as $user)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-3.5 py-2">
                                        <div class="flex items-center gap-2.5">
                                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-6 h-6 rounded-md object-cover border border-slate-200">
                                            <div>
                                                <div class="font-bold text-slate-900 text-xs">{{ $user->name }}</div>
                                                <div class="text-slate-400 text-[10px]">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3.5 py-2 font-medium">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse ($user->roles as $role)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-[#8F6B20] border border-[#C5A059]/30">
                                                    {{ $role->name }}
                                                </span>
                                            @empty
                                                <span class="text-slate-400 italic text-[10px]">No roles</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-3.5 py-2">
                                        <x-status-badge :status="$user->status" />
                                    </td>
                                    <td class="px-3.5 py-2 text-slate-400 text-[10px] font-mono">
                                        {{ $user->created_at->diffForHumans() }}
                                    </td>
                                    <td class="px-3.5 py-2 text-right">
                                        @can('users.edit')
                                             <a href="{{ route('dashboard.users.edit', $user) }}" class="text-[#8F6B20] hover:underline font-semibold text-xs">
                                                Edit
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-slate-400 text-xs">
                                        No employees found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        {{-- Roles Breakdown --}}
        <div class="lg:col-span-4 space-y-6">
            <x-card title="Role Distribution" subtitle="Active roles and employee headcount">
                <x-slot:actions>
                    @can('roles.view')
                        <a href="{{ route('dashboard.roles.index') }}" class="text-xs font-semibold text-[#8F6B20] hover:text-[#ad8442]">
                            Manage &rarr;
                        </a>
                    @endcan
                </x-slot:actions>

                <div class="space-y-2.5">
                    @foreach ($roles as $role)
                        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 border border-slate-200">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $role->slug === 'super-admin' ? 'bg-[#C5A059]' : 'bg-slate-400' }}"></span>
                                <div>
                                    <div class="text-xs font-bold text-slate-800">{{ $role->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $role->slug }}</div>
                                </div>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white border border-slate-200 text-slate-700">
                                {{ $role->users_count }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </x-card>
        </div>
    </div>
</x-dashboard.layout>
