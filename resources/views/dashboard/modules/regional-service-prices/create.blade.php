<x-dashboard.layout :title="'Create Regional Service Price — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <a href="{{ route('dashboard.regional-service-prices.index') }}" class="hover:text-[#C5A059]">Regional Pricing</a>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">Add Service Price</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Add Regional Service Price</h1>
            <p class="text-xs text-slate-500 mt-1">Configure localized base service rate for a specific territory and subcategory</p>
        </div>

        <div>
            <x-button href="{{ route('dashboard.regional-service-prices.index') }}" variant="secondary" size="sm">
                &larr; Back to Regional Pricing
            </x-button>
        </div>
    </x-slot:header>

    <div class="max-w-3xl" x-data="{
        selectedCategory: '{{ old('category_id', '') }}',
        selectedRegion: '{{ old('region_id', request('region_id', '')) }}',
        currency: 'USD',
        subcategories: @js($subcategories),
        regions: @js($regions),
        filteredSubcategories: [],
        init() {
            this.updateSubcategories();
            this.updateCurrency();
        },
        updateSubcategories() {
            if (!this.selectedCategory) {
                this.filteredSubcategories = [];
            } else {
                this.filteredSubcategories = this.subcategories.filter(sub => sub.category_id == this.selectedCategory);
            }
        },
        updateCurrency() {
            const found = this.regions.find(r => r.id == this.selectedRegion);
            if (found && found.currency) {
                this.currency = found.currency;
            } else {
                this.currency = 'USD';
            }
        }
    }">
        <form method="POST" action="{{ route('dashboard.regional-service-prices.store') }}" class="space-y-6">
            @csrf

            <x-card title="Price Configuration" subtitle="Select territory, service category, subcategory, and base price">
                <div class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Region --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Target Region *</label>
                            <select
                                name="region_id"
                                required
                                x-model="selectedRegion"
                                @change="updateCurrency()"
                                class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none transition-colors"
                            >
                                <option value="">-- Select Region --</option>
                                @foreach ($regions as $r)
                                    <option value="{{ $r->id }}" {{ old('region_id', request('region_id')) == $r->id ? 'selected' : '' }}>
                                        {{ $r->name }} ({{ $r->currency }})
                                    </option>
                                @endforeach
                            </select>
                            @error('region_id')
                                <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Category --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Service Category *</label>
                            <select
                                name="category_id"
                                required
                                x-model="selectedCategory"
                                @change="updateSubcategories()"
                                class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none transition-colors"
                            >
                                <option value="">-- Select Category --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Subcategory --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Subcategory Service *</label>
                            <select
                                name="subcategory_id"
                                required
                                class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none transition-colors"
                            >
                                <option value="">-- Select Subcategory --</option>
                                <template x-for="sub in filteredSubcategories" :key="sub.id">
                                    <option :value="sub.id" x-text="sub.name" :selected="sub.id == '{{ old('subcategory_id') }}'"></option>
                                </template>
                            </select>
                            <p x-show="!selectedCategory" class="mt-1 text-[11px] text-amber-600">Please select a category first.</p>
                            @error('subcategory_id')
                                <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Price --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Base Service Price (<span x-text="currency"></span>) *
                            </label>
                            <div class="flex rounded-lg border border-slate-300 bg-white overflow-hidden focus-within:border-[#C5A059] focus-within:ring-1 focus-within:ring-[#C5A059] transition-all">
                                <span class="inline-flex items-center px-3 bg-slate-100 text-slate-600 font-mono text-xs font-bold border-r border-slate-200 select-none shrink-0" x-text="currency">
                                    USD
                                </span>
                                <input
                                    type="number"
                                    name="price"
                                    step="0.01"
                                    min="0"
                                    max="9999999.99"
                                    value="{{ old('price') }}"
                                    placeholder="0.00"
                                    required
                                    class="w-full px-3.5 py-2 text-xs bg-white border-0 outline-none font-mono font-bold text-slate-900 placeholder:text-slate-400"
                                >
                            </div>
                            @error('price')
                                <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pricing Notes / Tier Guidelines</label>
                        <textarea
                            name="notes"
                            rows="3"
                            placeholder="Optional rate description, inclusion notes, or hourly rate details..."
                            class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none transition-colors"
                        >{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="w-full sm:w-1/2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Status *</label>
                        <select
                            name="status"
                            class="w-full px-3.5 py-2 text-xs bg-white border border-slate-300 rounded-lg focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] outline-none transition-colors"
                        >
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 pt-5 border-t border-slate-200 flex items-center justify-end gap-3">
                    <x-button href="{{ route('dashboard.regional-service-prices.index') }}" variant="secondary" size="sm">
                        Cancel
                    </x-button>
                    <x-button type="submit" variant="primary" size="sm">
                        Save Service Price
                    </x-button>
                </div>
            </x-card>
        </form>
    </div>

</x-dashboard.layout>
