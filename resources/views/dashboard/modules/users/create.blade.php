<x-dashboard.layout :title="'Create Employee Account — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <a href="{{ route('dashboard.users.index') }}" class="hover:text-[#C5A059]">User Management</a>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">Add Employee</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Add New Employee</h1>
            <p class="text-xs text-slate-500 mt-1">Register a corporate employee account and configure security role permissions</p>
        </div>

        <div>
            <x-button href="{{ route('dashboard.users.index') }}" variant="secondary" size="sm">
                &larr; Back to Directory
            </x-button>
        </div>
    </x-slot:header>

    <div class="max-w-4xl">
        <form method="POST" action="{{ route('dashboard.users.store') }}" enctype="multipart/form-data" class="space-y-6" x-data="{
            photoPreview: null,
            addresses: {{ json_encode(old('addresses', [['country' => '', 'state' => '', 'city' => '', 'address' => '']])) }},
            addAddress() {
                this.addresses.push({ country: '', state: '', city: '', address: '' });
            },
            removeAddress(index) {
                if (this.addresses.length > 1) {
                    this.addresses.splice(index, 1);
                } else {
                    this.addresses = [{ country: '', state: '', city: '', address: '' }];
                }
            }
        }">
            @csrf

            {{-- Account Identity & Photo Card --}}
            <x-card title="Account Identity & Profile Photo" subtitle="Primary credentials and avatar photograph">
                {{-- Avatar Upload Section --}}
                <div class="mb-6 p-4 rounded-2xl bg-[#FAF8F4] border border-[#C5A059]/25 flex flex-col sm:flex-row items-center gap-5">
                    <div class="relative shrink-0">
                        <template x-if="photoPreview">
                            <img :src="photoPreview" alt="Profile Preview" class="w-20 h-20 rounded-2xl object-cover ring-2 ring-[#C5A059] shadow-sm">
                        </template>
                        <template x-if="!photoPreview">
                            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-[#E5C158]/20 to-[#C5A059]/30 border-2 border-dashed border-[#C5A059]/50 flex flex-col items-center justify-center text-slate-500">
                                <svg class="w-8 h-8 text-[#8F6B20]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                        </template>
                    </div>
                    <div class="flex-1 text-center sm:text-left">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">Upload Profile Photo</label>
                        <p class="text-xs text-slate-500 mb-2">PNG, JPG, JPEG, or WEBP up to 2MB.</p>
                        <input
                            type="file"
                            name="avatar"
                            id="avatar"
                            accept="image/*"
                            @change="
                                const file = $event.target.files[0];
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = (e) => { photoPreview = e.target.result; };
                                    reader.readAsDataURL(file);
                                }
                            "
                            class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#C5A059]/20 file:text-[#8F6B20] hover:file:bg-[#C5A059]/30 file:cursor-pointer"
                        >
                        @error('avatar')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
                    {{-- Name --}}
                    <x-input
                        label="Full Name"
                        name="name"
                        placeholder="e.g. Eleanor Vance"
                        required
                        autofocus
                    />

                    {{-- Email --}}
                    <x-input
                        label="Corporate Email Address"
                        name="email"
                        type="email"
                        placeholder="e.g. eleanor.vance@emac.test"
                        required
                    />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
                    {{-- Phone --}}
                    <x-input
                        label="Contact Phone"
                        name="phone"
                        placeholder="+1 (555) 012-3456"
                    />

                    {{-- Status --}}
                    <x-select label="Initial Account Status" name="status" required>
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active Account</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive / Suspended</option>
                    </x-select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Password --}}
                    <x-input
                        label="Initial Password"
                        name="password"
                        type="password"
                        placeholder="Minimum 8 characters..."
                        required
                        hint="Employee can reset their password on first login."
                    />
                </div>
            </x-card>

            {{-- Multiple User Addresses Card --}}
            <x-card title="Registered User Addresses" subtitle="Store multiple physical and mailing addresses for this user">
                <x-slot:actions>
                    <button
                        type="button"
                        @click="addAddress()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg bg-[#C5A059]/15 text-[#8F6B20] hover:bg-[#C5A059]/25 border border-[#C5A059]/30 transition-colors cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Add Another Address</span>
                    </button>
                </x-slot:actions>

                <div class="space-y-4">
                    <template x-for="(addr, index) in addresses" :key="index">
                        <div class="p-4 rounded-2xl border border-slate-200/90 bg-slate-50/60 relative group hover:border-[#C5A059]/40 transition-colors">
                            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200/80">
                                <span class="text-xs font-bold text-slate-800 flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full bg-[#C5A059]/20 text-[#8F6B20] inline-flex items-center justify-center text-[10px]" x-text="index + 1"></span>
                                    <span x-text="index === 0 ? 'Primary Address' : 'Secondary Address #' + (index + 1)"></span>
                                </span>
                                <button
                                    type="button"
                                    @click="removeAddress(index)"
                                    x-show="addresses.length > 1"
                                    class="text-xs font-semibold text-rose-500 hover:text-rose-700 flex items-center gap-1 cursor-pointer"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span>Remove</span>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 uppercase mb-1">Country</label>
                                    <input
                                        type="text"
                                        :name="`addresses[${index}][country]`"
                                        x-model="addr.country"
                                        placeholder="e.g. United States"
                                        class="block w-full rounded-lg border border-slate-300 px-3 py-1.5 text-xs text-slate-900 bg-white focus:border-[#C5A059] focus:ring-[#C5A059]"
                                    >
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 uppercase mb-1">State / Province</label>
                                    <input
                                        type="text"
                                        :name="`addresses[${index}][state]`"
                                        x-model="addr.state"
                                        placeholder="e.g. California"
                                        class="block w-full rounded-lg border border-slate-300 px-3 py-1.5 text-xs text-slate-900 bg-white focus:border-[#C5A059] focus:ring-[#C5A059]"
                                    >
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 uppercase mb-1">City</label>
                                    <input
                                        type="text"
                                        :name="`addresses[${index}][city]`"
                                        x-model="addr.city"
                                        placeholder="e.g. Los Angeles"
                                        class="block w-full rounded-lg border border-slate-300 px-3 py-1.5 text-xs text-slate-900 bg-white focus:border-[#C5A059] focus:ring-[#C5A059]"
                                    >
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 uppercase mb-1">Street Address Details</label>
                                <textarea
                                    :name="`addresses[${index}][address]`"
                                    x-model="addr.address"
                                    rows="2"
                                    placeholder="Street, Building, Floor, Suite #..."
                                    class="block w-full rounded-lg border border-slate-300 px-3 py-1.5 text-xs text-slate-900 bg-white focus:border-[#C5A059] focus:ring-[#C5A059]"
                                ></textarea>
                            </div>
                        </div>
                    </template>
                </div>
            </x-card>

            {{-- Role Assignment Multi-Checkbox Grid Card --}}
            <x-card title="Assigned Security Roles" subtitle="Grant functional role permissions to this employee">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($roles as $role)
                        <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-[#C5A059] hover:bg-[#C5A059]/5 transition-colors cursor-pointer">
                            <input
                                type="checkbox"
                                name="roles[]"
                                value="{{ $role->id }}"
                                {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}
                                class="mt-0.5 rounded-sm border-slate-300 text-[#C5A059] focus:ring-[#C5A059] w-4 h-4 cursor-pointer"
                            >
                            <div class="text-xs">
                                <span class="font-bold text-slate-900 block">{{ $role->name }}</span>
                                <span class="text-slate-500 text-[11px]">{{ $role->description ?? 'Standard system role.' }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('roles')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror

                <x-slot:footer>
                    <div class="flex items-center justify-end gap-3 w-full">
                        <x-button href="{{ route('dashboard.users.index') }}" variant="secondary">
                            Cancel
                        </x-button>
                        <x-button type="submit" variant="primary">
                            Create Employee Account
                        </x-button>
                    </div>
                </x-slot:footer>
            </x-card>
        </form>
    </div>

</x-dashboard.layout>
