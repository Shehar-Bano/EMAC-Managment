<x-dashboard.layout :title="$user->name . ' — User Profile — EMAC Development ERP'">

    @php
        $typeNames = [
            'customers' => ['singular' => 'Customer', 'plural' => 'Customers Directory'],
            'technicians' => ['singular' => 'Technician', 'plural' => 'Technicians & Field Staff'],
            'admins' => ['singular' => 'Administrator', 'plural' => 'Administrator Users'],
        ];
        $typeInfo = $typeNames[$activeType] ?? $typeNames['customers'];
    @endphp

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <a href="{{ route('dashboard.users.index') }}" class="hover:text-[#8F6B20]">User Management</a>
        <span class="text-slate-400">/</span>
        <a href="{{ route('dashboard.users.index', ['type' => $activeType]) }}" class="hover:text-[#8F6B20]">User Directory</a>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">{{ $user->name }}</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div class="flex items-center gap-4">
            @if ($user->avatar)
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-14 h-14 rounded-2xl object-cover ring-2 ring-[#C5A059]/40 shadow-md shrink-0" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="hidden w-14 h-14 rounded-2xl bg-slate-900 text-[#C5A059] items-center justify-center font-bold text-xl ring-2 ring-[#C5A059]/40 shadow-md shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @else
                <div class="w-14 h-14 rounded-2xl bg-slate-900 text-[#C5A059] flex items-center justify-center font-bold text-xl ring-2 ring-[#C5A059]/40 shadow-md shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 flex items-center gap-3">
                    {{ $user->name }}
                    <x-status-badge :status="$user->status" />
                </h1>
                <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-2">
                    <span>{{ $user->email }}</span>
                    @if ($user->phone)
                        <span>•</span>
                        <span class="font-mono">{{ $user->phone }}</span>
                    @endif
                    <span>•</span>
                    <span>User ID: #{{ $user->id }}</span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <x-button href="{{ route('dashboard.users.index', ['type' => $activeType]) }}" variant="secondary" size="sm">
                &larr; Back to {{ $typeInfo['plural'] }}
            </x-button>

            @can('users.edit')
                <x-button href="{{ route('dashboard.users.edit', ['user' => $user, 'type' => $activeType]) }}" variant="primary" size="sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit Account
                </x-button>
            @endcan
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Profile Details Card --}}
        <div class="lg:col-span-4 space-y-6">
            <x-card title="Account Identity & Status" subtitle="System records and authentication">
                <dl class="divide-y divide-slate-100 text-xs">
                    <div class="py-3 flex justify-between items-center">
                        <dt class="font-semibold text-slate-500">Account Status</dt>
                        <dd><x-status-badge :status="$user->status" /></dd>
                    </div>
                    <div class="py-3 flex justify-between items-center">
                        <dt class="font-semibold text-slate-500">Contact Phone</dt>
                        <dd class="font-mono text-slate-800">{{ $user->phone ?? 'Not provided' }}</dd>
                    </div>
                    <div class="py-3 flex justify-between items-center">
                        <dt class="font-semibold text-slate-500">Registered Source</dt>
                        <dd class="font-semibold uppercase text-slate-800">{{ $user->source?->value ?? ($user->source ?? 'App / Email') }}</dd>
                    </div>
                    <div class="py-3 flex justify-between items-center">
                        <dt class="font-semibold text-slate-500">Registered On</dt>
                        <dd class="font-mono text-slate-700">{{ $user->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div class="py-3 flex justify-between items-center">
                        <dt class="font-semibold text-slate-500">Last Login</dt>
                        <dd class="font-mono text-slate-700">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never logged in' }}</dd>
                    </div>
                </dl>
            </x-card>

            <x-card title="Registered Addresses" subtitle="Physical & Service locations ({{ $user->addresses->count() }})">
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
                                {{ collect([$addr->city, $addr->region?->name ?: $addr->state, $addr->zipcode, $addr->country])->filter()->join(', ') ?: 'Location details pending' }}
                            </p>
                            <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                                @if($addr->zipcode)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-mono font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        📮 Zipcode: {{ $addr->zipcode }}
                                    </span>
                                @endif
                                @if($addr->region)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-[#C5A059]/15 text-[#8F6B20] border border-[#C5A059]/30">
                                        📍 Region: {{ $addr->region->name }} ({{ $addr->region->code ?: $addr->region->currency }})
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 text-xs py-2 text-center">No addresses registered for this user.</p>
                    @endforelse
                </div>
            </x-card>
        </div>

            {{-- Technician Skills & Operational Specialization Card --}}
            @if ($user->category || $user->subcategories->count() > 0 || $user->role === 'technician' || $user->hasRole('technician'))
                <x-card title="Technician Operational Profile & Skills" subtitle="Core expertise, sub-skills authorization, and credentials">
                    <div class="space-y-4 text-xs">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-3.5 rounded-xl bg-slate-50/70 border border-slate-200">
                            <div>
                                <span class="text-slate-400 block text-[11px] uppercase font-bold">Primary Skill</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                    {{ $user->category?->name ?? 'None Assigned' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px] uppercase font-bold">Duty Status</span>
                                <span class="font-semibold text-slate-800 mt-0.5 inline-flex items-center gap-1.5">
                                    @if($user->duty_status === 'on_duty')
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> On Duty
                                    @elseif($user->duty_status === 'off_duty')
                                        <span class="w-2 h-2 rounded-full bg-slate-400"></span> Off Duty
                                    @elseif($user->duty_status === 'break')
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> On Break
                                    @else
                                        {{ ucfirst(str_replace('_', ' ', $user->duty_status ?? 'on_duty')) }}
                                    @endif
                                </span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px] uppercase font-bold">Experience</span>
                                <span class="font-bold text-slate-900 mt-0.5 block">
                                    {{ $user->experience_years ? $user->experience_years . ' Years' : 'Not specified' }}
                                </span>
                            </div>
                        </div>

                        {{-- Sub Skills --}}
                        <div>
                            <span class="text-slate-500 block text-xs font-bold uppercase mb-2">Authorized Sub Skills ({{ $user->subcategories->count() }})</span>
                            <div class="flex flex-wrap gap-2">
                                @forelse ($user->subcategories as $sub)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-[#C5A059]/15 text-[#8F6B20] border border-[#C5A059]/30">
                                        <span>🔧</span>
                                        <span>{{ $sub->name }}</span>
                                    </span>
                                @empty
                                    <span class="text-slate-400 text-xs italic">No sub-skills assigned.</span>
                                @endforelse
                            </div>
                        </div>

                        {{-- Credentials & Emergency Contact --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                            <div>
                                <span class="text-slate-400 block text-[11px] uppercase font-bold">Certification</span>
                                <p class="text-slate-800 font-medium mt-0.5">
                                    {{ $user->certification_id ?? 'No Certificate ID' }}
                                    @if ($user->certification_body)
                                        <span class="text-slate-500 block text-[11px]">({{ $user->certification_body }})</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px] uppercase font-bold">Emergency Contact</span>
                                <p class="text-slate-800 font-medium mt-0.5">
                                    {{ $user->emergency_contact_name ?? 'Not provided' }}
                                    @if ($user->emergency_contact_phone)
                                        <span class="text-slate-500 font-mono block text-[11px]">{{ $user->emergency_contact_phone }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if ($user->bio)
                            <div class="pt-2 border-t border-slate-100">
                                <span class="text-slate-400 block text-[11px] uppercase font-bold mb-1">Technician Bio</span>
                                <p class="text-slate-700 leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-200/80">{{ $user->bio }}</p>
                            </div>
                        @endif
                    </div>
                </x-card>
            @endif

            {{-- Assigned Security Roles --}}
            <x-card title="Assigned Security Roles" subtitle="Roles governing this account's access and privileges">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse ($user->roles as $role)
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:border-[#C5A059]/40 transition-colors">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-sm text-slate-900">{{ $role->name }}</span>
                                @if ($role->slug === 'super-admin')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-900 text-[#D4AF37] border border-slate-700">Root Admin</span>
                                @elseif ($role->slug === 'technician')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">Technician</span>
                                @elseif ($role->slug === 'customer')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#C5A059]/15 text-[#8F6B20] border border-[#C5A059]/30">Customer</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#C5A059]/10 text-[#8F6B20] border border-[#C5A059]/20">Standard</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 mt-1">{{ $role->description ?? 'Standard system role.' }}</p>
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

            {{-- If Customer: Customer Requests & Quotes History --}}
            @if ($user->serviceRequests->count() > 0 || $user->quotes->count() > 0)
                <x-card title="Service Requests & Quotations History" subtitle="Customer orders and issued estimates">
                    <div class="space-y-3">
                        @foreach ($user->serviceRequests as $sr)
                            <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 flex items-center justify-between">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('dashboard.service-requests.show', $sr) }}" class="font-bold text-xs text-slate-900 hover:text-[#C5A059] font-mono">
                                            #REQ-{{ str_pad($sr->id, 5, '0', STR_PAD_LEFT) }}
                                        </a>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700">
                                            {{ ucfirst(str_replace('_', ' ', $sr->status->value ?? $sr->status)) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-1">{{ $sr->description }}</p>
                                </div>
                                <x-button href="{{ route('dashboard.service-requests.show', $sr) }}" variant="secondary" size="xs">
                                    View Request
                                </x-button>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            @endif
        </div>
    </div>

</x-dashboard.layout>
