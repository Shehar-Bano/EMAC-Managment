<x-dashboard.layout :title="'Permission Groups & Matrix — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <a href="{{ route('dashboard.roles.index') }}" class="hover:text-[#C5A059]">Security Roles</a>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">Permission Matrix</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Permission Groups & Matrix</h1>
            <p class="text-xs text-slate-500 mt-1">Central catalog of system permission groups and granular operational abilities</p>
        </div>

        <div>
            <x-button href="{{ route('dashboard.roles.index') }}" variant="secondary" size="sm">
                &larr; Back to Roles
            </x-button>
        </div>
    </x-slot:header>

    <div class="space-y-6">
        @foreach ($groups as $group)
            <x-card :title="$group->name" :subtitle="$group->description">
                <x-slot:actions>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-[#C5A059]/15 text-[#8F6B20] border border-[#C5A059]/30">
                        {{ $group->permissions->count() }} Actions
                    </span>
                </x-slot:actions>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($group->permissions as $permission)
                        <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/50 hover:bg-white hover:border-[#C5A059]/50 transition-colors">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-xs text-slate-900">{{ $permission->label }}</span>
                                <span class="text-[10px] font-mono text-[#8F6B20] bg-white px-1.5 py-0.5 rounded border border-slate-200">{{ $permission->name }}</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1">{{ $permission->description }}</p>

                            <div class="mt-3 pt-2 border-t border-slate-200/60 flex flex-wrap gap-1">
                                <span class="text-[10px] text-slate-400 font-semibold mr-1">Roles:</span>
                                @forelse ($permission->roles as $r)
                                    <span class="text-[10px] px-1.5 py-0.2 rounded font-bold {{ $r->slug === 'super-admin' ? 'bg-slate-900 text-[#D4AF37]' : 'bg-[#C5A059]/10 text-[#8F6B20] border border-[#C5A059]/20' }}">
                                        {{ $r->name }}
                                    </span>
                                @empty
                                    <span class="text-[10px] text-slate-400 italic">None</span>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>
        @endforeach
    </div>

</x-dashboard.layout>
