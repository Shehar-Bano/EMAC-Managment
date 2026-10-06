<x-dashboard.layout :title="'Edit Profile — ' . $user->name . ' — EMAC Development ERP'">

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
        <span class="font-semibold text-slate-800">Edit {{ $user->name }}</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Edit {{ $typeInfo['singular'] }} Account</h1>
            <p class="text-xs text-slate-500 mt-1">Update profile information, contact channels, physical addresses, and security role permissions</p>
        </div>

        <div>
            <x-button href="{{ route('dashboard.users.index', ['type' => $activeType]) }}" variant="secondary" size="sm">
                &larr; Back to {{ $typeInfo['plural'] }}
            </x-button>
        </div>
    </x-slot:header>

    <div class="max-w-4xl">
        <form method="POST" action="{{ route('dashboard.users.update', $user) }}" enctype="multipart/form-data" autocomplete="off" class="space-y-6" x-data="{
            photoPreview: null,
            removeAvatar: false,
            addresses: {{ json_encode(old('addresses', $user->addresses->count() > 0 ? $user->addresses->map(fn($a) => ['region_id' => $a->region_id, 'country' => $a->country, 'state' => $a->state, 'city' => $a->city, 'zipcode' => $a->zipcode, 'address' => $a->address])->values()->toArray() : [['region_id' => '', 'country' => '', 'state' => '', 'city' => '', 'zipcode' => '', 'address' => '']])) }},
            addAddress() {
                this.addresses.push({ region_id: '', country: '', state: '', city: '', zipcode: '', address: '' });
            },
            removeAddress(index) {
                if (this.addresses.length > 1) {
                    this.addresses.splice(index, 1);
                } else {
                    this.addresses = [{ region_id: '', country: '', state: '', city: '', zipcode: '', address: '' }];
                }
            }
        }">
            @csrf
            @method('PUT')
            {{-- Trap inputs to prevent aggressive browser autofill of admin credentials --}}
            <input type="text" style="display:none" aria-hidden="true" tabindex="-1" autocomplete="false">
            <input type="password" style="display:none" aria-hidden="true" tabindex="-1" autocomplete="false">

            <input type="hidden" name="type" value="{{ $activeType }}">

            {{-- Account Information & Profile Photo Card --}}
            <x-card title="Account Identity & Profile Photo" subtitle="User ID: #{{ $user->id }}">
                {{-- Avatar Upload & Current Display Section --}}
                <div class="mb-6 p-4 rounded-2xl bg-[#FAF8F4] border border-[#C5A059]/25 flex flex-col sm:flex-row items-center gap-5">
                    <div class="relative shrink-0">
                        <template x-if="photoPreview">
                            <img :src="photoPreview" alt="Profile Preview" class="w-20 h-20 rounded-2xl object-cover ring-2 ring-[#C5A059] shadow-sm">
                        </template>
                        <template x-if="!photoPreview">
                            <div class="relative">
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-2xl object-cover ring-2 ring-[#C5A059]/40 shadow-sm" :class="{ 'opacity-40 grayscale': removeAvatar }">
                            </div>
                        </template>
                    </div>
                    <div class="flex-1 text-center sm:text-left">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">Update Profile Photo</label>
                        <p class="text-xs text-slate-500 mb-2">PNG, JPG, JPEG, or WEBP up to 2MB. Leave unchanged to keep current image.</p>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <input
                                type="file"
                                name="avatar"
                                id="avatar"
                                accept="image/*"
                                @change="
                                    removeAvatar = false;
                                    const file = $event.target.files[0];
                                    if (file) {
                                        const reader = new FileReader();
                                        reader.onload = (e) => { photoPreview = e.target.result; };
                                        reader.readAsDataURL(file);
                                    }
                                "
                                class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#C5A059]/20 file:text-[#8F6B20] hover:file:bg-[#C5A059]/30 file:cursor-pointer"
                            >
                            @if ($user->avatar)
                                <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 font-semibold cursor-pointer whitespace-nowrap">
                                    <input type="checkbox" name="remove_avatar" value="1" x-model="removeAvatar" class="rounded text-rose-600 focus:ring-rose-500">
                                    <span>Remove Photo</span>
                                </label>
                            @endif
                        </div>
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
                        :value="$user->name"
                        placeholder="e.g. Eleanor Vance"
                        required
                        autofocus
                    />

                    {{-- Email --}}
                    <x-input
                        label="Email Address"
                        name="email"
                        type="email"
                        :value="$user->email"
                        placeholder="e.g. eleanor.vance@emac.test"
                        required
                        autocomplete="off"
                    />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
                    {{-- Phone --}}
                    <x-input
                        label="Contact Phone"
                        name="phone"
                        :value="$user->phone"
                        placeholder="+1 (555) 012-3456"
                    />

                    {{-- Status --}}
                    <x-select label="Account Status" name="status" required>
                        <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active Account</option>
                        <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive / Suspended</option>
                    </x-select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
                    {{-- Password (Optional) --}}
                    <x-input
                        label="New Password"
                        name="password"
                        type="password"
                        placeholder="Leave blank to keep existing password..."
                        autocomplete="new-password"
                        hint="Only enter a value if you wish to reset this user's password."
                    />
                </div>
            </x-card>

            @if ($activeType === 'technicians' || $user->category_id || $user->role === 'technician' || $user->hasRole('technician'))
                {{-- Technician Skills & Specialization Card --}}
                <div x-data="{
                    categories: {{ json_encode($categories->map(fn($c) => [
                        'id' => $c->id,
                        'name' => $c->name,
                        'slug' => $c->slug,
                        'subcategories' => $c->subcategories->map(fn($s) => [
                            'id' => $s->id,
                            'name' => $s->name,
                            'slug' => $s->slug,
                            'icon' => $s->icon,
                        ]),
                    ])) }},
                    selectedCategoryId: '{{ old('category_id', $user->category_id ?? '') }}',
                    selectedSubcategories: {{ json_encode(array_map('intval', old('subcategories', $user->subcategories->pluck('id')->toArray()))) }},
                    get availableSubcategories() {
                        if (!this.selectedCategoryId) return [];
                        const found = this.categories.find(c => String(c.id) === String(this.selectedCategoryId));
                        return found ? found.subcategories : [];
                    },
                    onCategoryChange() {
                        const availableIds = this.availableSubcategories.map(s => Number(s.id));
                        this.selectedSubcategories = this.selectedSubcategories.filter(id => availableIds.includes(Number(id)));
                    },
                    toggleSubcategory(id) {
                        id = Number(id);
                        const index = this.selectedSubcategories.indexOf(id);
                        if (index > -1) {
                            this.selectedSubcategories.splice(index, 1);
                        } else {
                            this.selectedSubcategories.push(id);
                        }
                    },
                    selectAllSubcategories() {
                        this.selectedSubcategories = this.availableSubcategories.map(s => Number(s.id));
                    },
                    clearSubcategories() {
                        this.selectedSubcategories = [];
                    }
                }">
                    <x-card title="Technician Skills & Operational Specialization" subtitle="Assign primary trade skill and authorized field service sub-skills">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
                            {{-- Skill (Main Category Single Select) --}}
                            <div>
                                <label for="category_id" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                                    Skill <span class="text-rose-600">*</span>
                                </label>
                                <select
                                    name="category_id"
                                    id="category_id"
                                    x-model="selectedCategoryId"
                                    @change="onCategoryChange()"
                                    required
                                    class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 bg-white shadow-sm focus:border-[#C5A059] focus:ring-[#C5A059] transition-colors"
                                >
                                    <option value="">-- Select Primary Skill (Category) --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ (string) old('category_id', $user->category_id) === (string) $category->id ? 'selected' : '' }}>
                                            {{ $category->name }} ({{ $category->subcategories->count() }} Sub Skills)
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-slate-500 mt-1">Select the technician's core trade expertise.</p>
                                @error('category_id')
                                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Duty Status --}}
                            <div>
                                <label for="duty_status" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                                    Duty Status
                                </label>
                                <select
                                    name="duty_status"
                                    id="duty_status"
                                    class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 bg-white shadow-sm focus:border-[#C5A059] focus:ring-[#C5A059] transition-colors"
                                >
                                    <option value="on_duty" {{ old('duty_status', $user->duty_status ?? 'on_duty') === 'on_duty' ? 'selected' : '' }}>🟢 On Duty (Available for jobs)</option>
                                    <option value="off_duty" {{ old('duty_status', $user->duty_status) === 'off_duty' ? 'selected' : '' }}>⚪ Off Duty (Unavailable)</option>
                                    <option value="break" {{ old('duty_status', $user->duty_status) === 'break' ? 'selected' : '' }}>🟡 On Break</option>
                                </select>
                                <p class="text-[11px] text-slate-500 mt-1">Current operational dispatch availability.</p>
                                @error('duty_status')
                                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Sub Skills (Subcategories Multi-Select) --}}
                        <div class="mb-6 p-4 rounded-2xl bg-slate-50/80 border border-slate-200">
                            <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-200">
                                <div>
                                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                                        Sub Skills <span class="text-rose-600">*</span>
                                    </label>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Select one or multiple authorized services (minimum 1 required)</p>
                                </div>
                                <div class="flex items-center gap-2" x-show="availableSubcategories.length > 0">
                                    <button
                                        type="button"
                                        @click="selectAllSubcategories()"
                                        class="text-[11px] font-semibold text-[#8F6B20] hover:text-[#735518] hover:underline cursor-pointer"
                                    >Select All</button>
                                    <span class="text-slate-300">|</span>
                                    <button
                                        type="button"
                                        @click="clearSubcategories()"
                                        class="text-[11px] font-semibold text-slate-500 hover:text-slate-700 hover:underline cursor-pointer"
                                    >Clear</button>
                                </div>
                            </div>

                            <template x-if="!selectedCategoryId">
                                <div class="py-6 text-center text-xs text-slate-400">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    Please select a <strong>Skill</strong> above to see available Sub Skills.
                                </div>
                            </template>

                            <template x-if="selectedCategoryId && availableSubcategories.length === 0">
                                <div class="py-6 text-center text-xs text-slate-500">
                                    No active sub-skills found for this category.
                                </div>
                            </template>

                            <div x-show="selectedCategoryId && availableSubcategories.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                                <template x-for="sub in availableSubcategories" :key="sub.id">
                                    <label
                                        @click="toggleSubcategory(sub.id)"
                                        class="flex items-center gap-2.5 p-2.5 rounded-xl border text-xs cursor-pointer transition-all select-none"
                                        :class="selectedSubcategories.includes(Number(sub.id))
                                            ? 'bg-amber-50/80 border-[#C5A059] text-slate-900 font-semibold shadow-xs ring-1 ring-[#C5A059]/40'
                                            : 'bg-white border-slate-200 text-slate-700 hover:border-slate-300 hover:bg-slate-50'"
                                    >
                                        <input
                                            type="checkbox"
                                            name="subcategories[]"
                                            :value="sub.id"
                                            :checked="selectedSubcategories.includes(Number(sub.id))"
                                            class="rounded border-slate-300 text-[#C5A059] focus:ring-[#C5A059] w-4 h-4 pointer-events-none"
                                        >
                                        <span class="truncate" x-text="sub.name"></span>
                                    </label>
                                </template>
                            </div>

                            @error('subcategories')
                                <p class="mt-2 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                            @error('subcategories.*')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Professional Credentials & Experience --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-5">
                            <div>
                                <x-input
                                    label="Years of Experience"
                                    name="experience_years"
                                    type="number"
                                    min="0"
                                    max="60"
                                    placeholder="e.g. 8"
                                    :value="old('experience_years', $user->experience_years ?? 0)"
                                />
                            </div>
                            <div>
                                <x-input
                                    label="Certification ID"
                                    name="certification_id"
                                    :value="old('certification_id', $user->certification_id)"
                                    placeholder="e.g. EMAC-TECH-CERT-0024"
                                />
                            </div>
                            <div>
                                <x-input
                                    label="Certification Body"
                                    name="certification_body"
                                    :value="old('certification_body', $user->certification_body)"
                                    placeholder="e.g. Cayman Islands Trade Guild"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                            <div>
                                <x-input
                                    label="Emergency Contact Name"
                                    name="emergency_contact_name"
                                    :value="old('emergency_contact_name', $user->emergency_contact_name)"
                                    placeholder="e.g. Sarah Davis"
                                />
                            </div>
                            <div>
                                <x-input
                                    label="Emergency Contact Phone"
                                    name="emergency_contact_phone"
                                    :value="old('emergency_contact_phone', $user->emergency_contact_phone)"
                                    placeholder="e.g. +1 345 925 1102"
                                />
                            </div>
                        </div>

                        <div>
                            <label for="bio" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                                Technician Bio / Professional Summary
                            </label>
                            <textarea
                                name="bio"
                                id="bio"
                                rows="3"
                                placeholder="Lead Master Technician specializing in luxury residential hydraulics, high-pressure line diagnostics..."
                                class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 bg-white shadow-sm focus:border-[#C5A059] focus:ring-[#C5A059] transition-colors"
                            >{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </x-card>
                </div>
            @endif

            {{-- Multiple User Addresses Card --}}
            <x-card title="Registered User Addresses" subtitle="Store multiple physical, service, and mailing addresses">
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

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 uppercase mb-1">State / Region</label>
                                    <select
                                        :name="`addresses[${index}][region_id]`"
                                        x-model="addr.region_id"
                                        class="block w-full rounded-lg border border-slate-300 px-3 py-1.5 text-xs text-slate-900 bg-white focus:border-[#C5A059] focus:ring-[#C5A059]"
                                    >
                                        <option value="">Select Territory / Region...</option>
                                        @foreach ($regions as $region)
                                            <option value="{{ $region->id }}">{{ $region->name }} ({{ $region->code ?: $region->currency }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 uppercase mb-1">Country</label>
                                    <input
                                        type="text"
                                        :name="`addresses[${index}][country]`"
                                        x-model="addr.country"
                                        placeholder="e.g. Cayman Islands / US"
                                        class="block w-full rounded-lg border border-slate-300 px-3 py-1.5 text-xs text-slate-900 bg-white focus:border-[#C5A059] focus:ring-[#C5A059]"
                                    >
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 uppercase mb-1">City / District</label>
                                    <input
                                        type="text"
                                        :name="`addresses[${index}][city]`"
                                        x-model="addr.city"
                                        placeholder="e.g. George Town / Miami"
                                        class="block w-full rounded-lg border border-slate-300 px-3 py-1.5 text-xs text-slate-900 bg-white focus:border-[#C5A059] focus:ring-[#C5A059]"
                                    >
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 uppercase mb-1">Zipcode / Postal Code</label>
                                    <input
                                        type="text"
                                        :name="`addresses[${index}][zipcode]`"
                                        x-model="addr.zipcode"
                                        placeholder="e.g. KY1-1102 / 33101"
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

            {{-- Role Assignment Radio Selection --}}
            <x-card title="Assigned Security Role" subtitle="Select the primary role that determines system-wide permission access">
                @php
                    $selectedRoleId = (int) old('role_id', old('roles.0', ($user->roles->first()?->id ?? 0)));
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach ($roles as $role)
                        @php
                            $isChecked = ($role->id === $selectedRoleId);
                        @endphp
                        <label class="flex items-start gap-3 p-3.5 rounded-xl border transition-all cursor-pointer {{ $isChecked ? 'border-[#C5A059] bg-amber-50/50 ring-1 ring-[#C5A059]' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                            <input
                                type="radio"
                                name="role_id"
                                value="{{ $role->id }}"
                                {{ $isChecked ? 'checked' : '' }}
                                class="mt-0.5 border-slate-300 text-[#C5A059] focus:ring-[#C5A059] w-4 h-4 cursor-pointer"
                            >
                            <div class="text-xs">
                                <span class="font-bold text-slate-900 block">{{ $role->name }}</span>
                                <span class="text-slate-500 text-[11px]">{{ $role->description ?? 'Standard system role.' }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('role_id')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
                @error('roles')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror

                <x-slot:footer>
                    <div class="flex items-center justify-end gap-3 w-full">
                        <x-button href="{{ route('dashboard.users.index', ['type' => $activeType]) }}" variant="secondary">
                            Cancel
                        </x-button>
                        <x-button type="submit" variant="primary">
                            Save Changes
                        </x-button>
                    </div>
                </x-slot:footer>
            </x-card>
        </form>
    </div>

</x-dashboard.layout>
