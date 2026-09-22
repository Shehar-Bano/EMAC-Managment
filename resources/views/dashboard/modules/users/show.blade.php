<x-dashboard.layout :title="$user->name . ' — Employee Profile — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <a href="{{ route('dashboard.users.index') }}" class="hover:text-[#C5A059]">User Management</a>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">{{ $user->name }}</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div class="flex items-center gap-4">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-14 h-14 rounded-2xl object-cover ring-2 ring-[#C5A059]/40 shadow-md">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 flex items-center gap-3">
                    {{ $user->name }}
                    <x-status-badge :status="$user->status" />
                </h1>
                <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-2">
                    <span>{{ $user->email }}</span>
                    @if ($user->phone)
                        <span>•</span>
                        <span>{{ $user->phone }}</span>
                    @endif
                    <span>•</span>
                    <span>Employee ID: #{{ $user->id }}</span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <x-button href="{{ route('dashboard.users.index') }}" variant="secondary" size="sm">
                &larr; Back to Directory
            </x-button>

            @can('users.edit')
                <x-button href="{{ route('dashboard.users.edit', $user) }}" variant="primary" size="sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit Profile
                </x-button>
            @endcan
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Profile Details Card --}}
        <div class="lg:col-span-4 space-y-6">
            <x-card title="Personnel Information" subtitle="System records and status">
                <dl class="divide-y divide-slate-100 text-xs">
                    <div class="py-3 flex justify-between">
                        <dt class="font-semibold text-slate-500">Account Status</dt>
                        <dd><x-status-badge :status="$user->status" /></dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-semibold text-slate-500">Contact Phone</dt>
                        <dd class="font-mono text-slate-800">{{ $user->phone ?? 'Not provided' }}</dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-semibold text-slate-500">Registered On</dt>
                        <dd class="font-mono text-slate-700">{{ $user->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-semibold text-slate-500">Last Login</dt>
                        <dd class="font-mono text-slate-700">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never logged in' }}</dd>
                    </div>
                </dl>
            </x-card>

            <x-card title="Registered Addresses" subtitle="Physical & Mailing locations ({{ $user->addresses->count() }})">
                <div class="space-y-3 text-xs">
                    @forelse ($user->addresses as $idx => $addr)
                        <div class="p-3 rounded-xl border border-slate-200 bg-slate-50/50">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-slate-900">
                                    {{ $addr->is_primary ? 'Primary Address' : 'Address #' . ($idx + 1) }}
                                </span>
                                @if($addr->is_primary)
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-[#C5A059]/10 text-[#8F6B20]">Primary</span>
                                @endif
                            </div>
                            <p class="text-slate-700 font-medium">{{ $addr->address ?: 'No street specified' }}</p>
                            <p class="text-slate-500 text-[11px] mt-0.5">
                                {{ collect([$addr->city, $addr->state, $addr->country])->filter()->join(', ') ?: 'Location details pending' }}
                            </p>
                        </div>
                    @empty
                        <p class="text-slate-400 text-xs py-2 text-center">No addresses registered for this user.</p>
                    @endforelse
                </div>
            </x-card>
        </div>

        {{-- Effective Permissions Matrix --}}
        <div class="lg:col-span-8 space-y-6">
            <x-card title="Assigned Security Roles" subtitle="Roles governing this employee's abilities">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse ($user->roles as $role)
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:border-[#C5A059]/40 transition-colors">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-sm text-slate-900">{{ $role->name }}</span>
                                @if ($role->slug === 'super-admin')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-900 text-[#D4AF37]">Super Admin</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#C5A059]/10 text-[#8F6B20] border border-[#C5A059]/20">Standard Role</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 mt-1">{{ $role->description ?? 'No description provided.' }}</p>
                            <div class="mt-3 text-[11px] font-semibold text-[#8F6B20]">
                                {{ $role->permissions->count() }} active permissions granted
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 text-center py-6 text-slate-400 text-xs">
                            No security roles assigned.
                        </div>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>

</x-dashboard.layout>
