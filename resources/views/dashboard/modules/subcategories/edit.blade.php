<x-dashboard.layout :title="'Edit Subcategory — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <a href="{{ route('dashboard.categories.index') }}" class="hover:text-[#C5A059]">Categories</a>
        <span class="text-slate-400">/</span>
        <a href="{{ route('dashboard.subcategories.index') }}" class="hover:text-[#C5A059]">Subcategories</a>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">Edit {{ $subcategory->name }}</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Edit Subcategory</h1>
            <p class="text-xs text-slate-500 mt-1">Update classification parameters, parent category mapping and status</p>
        </div>

        <div>
            <x-button href="{{ route('dashboard.subcategories.index') }}" variant="secondary" size="sm">
                &larr; Back to Subcategories
            </x-button>
        </div>
    </x-slot:header>

    <div class="max-w-4xl">
        <form method="POST" action="{{ route('dashboard.subcategories.update', $subcategory) }}" enctype="multipart/form-data" class="space-y-6" x-data="{
            iconPreview: null,
            iconFileName: '',
            removeIcon: false,
            imagePreview: null,
            imageFileName: '',
            removeImage: false,
            handleIconSelect(event) {
                const file = event.target.files[0];
                if (file) {
                    this.iconFileName = file.name;
                    this.removeIcon = false;
                    const reader = new FileReader();
                    reader.onload = (e) => { this.iconPreview = e.target.result; };
                    reader.readAsDataURL(file);
                }
            },
            cancelNewIcon() {
                this.iconPreview = null;
                this.iconFileName = '';
                document.getElementById('subcategory-edit-icon-input').value = '';
            },
            handleImageSelect(event) {
                const file = event.target.files[0];
                if (file) {
                    this.imageFileName = file.name;
                    this.removeImage = false;
                    const reader = new FileReader();
                    reader.onload = (e) => { this.imagePreview = e.target.result; };
                    reader.readAsDataURL(file);
                }
            },
            cancelNewImage() {
                this.imagePreview = null;
                this.imageFileName = '';
                document.getElementById('subcategory-edit-image-input').value = '';
            }
        }">
            @csrf
            @method('PUT')

            <x-card title="Subcategory Information & Visual Media" subtitle="Subcategory ID: #{{ $subcategory->id }}">
                <div class="space-y-5 mb-6">
                    {{-- 1. Subcategory Icon Component --}}
                    <div class="p-4 rounded-2xl bg-[#FAF8F4] border border-[#C5A059]/30 shadow-2xs">
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <label class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#8F6B20]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                                <span>Subcategory Icon</span>
                            </label>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#C5A059]/20 text-[#8F6B20] border border-[#C5A059]/30">
                                1:1 Vector / Icon (128&times;128px)
                            </span>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            {{-- Icon Preview Box --}}
                            <div class="relative shrink-0">
                                <template x-if="iconPreview">
                                    <div class="relative w-16 h-16">
                                        <img :src="iconPreview" alt="New Icon Preview" class="w-16 h-16 rounded-xl object-cover ring-2 ring-[#C5A059] shadow-sm">
                                        <button
                                            type="button"
                                            @click="cancelNewIcon()"
                                            class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-rose-600 text-white flex items-center justify-center hover:bg-rose-700 shadow-sm transition-transform hover:scale-110 text-xs cursor-pointer"
                                            title="Cancel new icon"
                                        >
                                            &times;
                                        </button>
                                    </div>
                                </template>
                                <template x-if="!iconPreview">
                                    <div>
                                        @if ($subcategory->icon_url && (str_contains($subcategory->icon_url, '/') || str_contains($subcategory->icon_url, '.')))
                                            <div class="relative w-16 h-16">
                                                <img
                                                    src="{{ $subcategory->icon_url }}"
                                                    alt="{{ $subcategory->name }} Icon"
                                                    class="w-16 h-16 rounded-xl object-cover ring-2 ring-[#C5A059]/40 shadow-sm transition-opacity"
                                                    :class="{ 'opacity-30 grayscale ring-rose-400': removeIcon }"
                                                >
                                                <template x-if="removeIcon">
                                                    <span class="absolute inset-0 flex items-center justify-center text-[9px] font-bold text-rose-700 bg-rose-50/80 rounded-xl">Remove</span>
                                                </template>
                                            </div>
                                        @else
                                            <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-[#E5C158]/15 to-[#C5A059]/25 border-2 border-dashed border-[#C5A059]/50 flex flex-col items-center justify-center text-slate-400">
                                                <svg class="w-6 h-6 text-[#8F6B20]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                            </div>
                                        @endif
                                    </div>
                                </template>
                            </div>

                            <div class="flex-1 min-w-0 space-y-2">
                                <div class="relative border-2 border-dashed border-slate-300 hover:border-[#C5A059] rounded-xl p-3 bg-white/80 hover:bg-white flex items-center justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-semibold text-slate-800 truncate" x-text="iconFileName ? iconFileName : '{{ $subcategory->icon ? 'Replace icon' : 'Upload icon file' }}'"></p>
                                        <p class="text-[11px] text-slate-500">SVG, PNG, JPG (Max 5MB)</p>
                                    </div>
                                    <label for="subcategory-edit-icon-input" class="shrink-0 px-3.5 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs cursor-pointer inline-flex items-center gap-1.5 shadow-2xs">
                                        <span x-text="iconPreview ? 'Change' : '{{ $subcategory->icon ? 'Replace' : 'Browse' }}'">Browse</span>
                                    </label>
                                    <input
                                        type="file"
                                        name="icon"
                                        id="subcategory-edit-icon-input"
                                        accept="image/*,.svg"
                                        @change="handleIconSelect($event)"
                                        class="sr-only"
                                    >
                                </div>
                                @if ($subcategory->icon)
                                    <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 font-semibold cursor-pointer select-none">
                                        <input
                                            type="checkbox"
                                            name="remove_icon"
                                            value="1"
                                            x-model="removeIcon"
                                            @change="if(removeIcon) { cancelNewIcon(); }"
                                            class="rounded text-rose-600 focus:ring-rose-500 text-xs cursor-pointer"
                                        >
                                        <span>Remove existing icon</span>
                                    </label>
                                @endif
                                @error('icon')
                                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- 2. Subcategory Cover Image Component --}}
                    <div class="p-4 rounded-2xl bg-[#FAF8F4] border border-[#C5A059]/30 shadow-2xs">
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <label class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-[#8F6B20]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>Subcategory Cover Image</span>
                            </label>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#C5A059]/20 text-[#8F6B20] border border-[#C5A059]/30">
                                Banner / Card Image (16:9 or Landscape)
                            </span>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            {{-- Image Preview Box --}}
                            <div class="relative shrink-0">
                                <template x-if="imagePreview">
                                    <div class="relative w-24 h-16">
                                        <img :src="imagePreview" alt="New Cover Preview" class="w-24 h-16 rounded-xl object-cover ring-2 ring-[#C5A059] shadow-sm">
                                        <button
                                            type="button"
                                            @click="cancelNewImage()"
                                            class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-rose-600 text-white flex items-center justify-center hover:bg-rose-700 shadow-sm transition-transform hover:scale-110 text-xs cursor-pointer"
                                            title="Cancel new image"
                                        >
                                            &times;
                                        </button>
                                    </div>
                                </template>
                                <template x-if="!imagePreview">
                                    <div>
                                        @if ($subcategory->image_url)
                                            <div class="relative w-24 h-16">
                                                <img
                                                    src="{{ $subcategory->image_url }}"
                                                    alt="{{ $subcategory->name }}"
                                                    class="w-24 h-16 rounded-xl object-cover ring-2 ring-[#C5A059]/40 shadow-sm transition-opacity"
                                                    :class="{ 'opacity-30 grayscale ring-rose-400': removeImage }"
                                                >
                                                <template x-if="removeImage">
                                                    <span class="absolute inset-0 flex items-center justify-center text-[9px] font-bold text-rose-700 bg-rose-50/80 rounded-xl">Remove</span>
                                                </template>
                                            </div>
                                        @else
                                            <div class="w-24 h-16 rounded-xl bg-gradient-to-br from-[#E5C158]/15 to-[#C5A059]/25 border-2 border-dashed border-[#C5A059]/50 flex flex-col items-center justify-center text-slate-400">
                                                <svg class="w-6 h-6 text-[#8F6B20]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif
                                    </div>
                                </template>
                            </div>

                            <div class="flex-1 min-w-0 space-y-2">
                                <div class="relative border-2 border-dashed border-slate-300 hover:border-[#C5A059] rounded-xl p-3 bg-white/80 hover:bg-white flex items-center justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-semibold text-slate-800 truncate" x-text="imageFileName ? imageFileName : '{{ $subcategory->image ? 'Replace image' : 'Upload image file' }}'"></p>
                                        <p class="text-[11px] text-slate-500">WEBP, PNG, JPG (Max 5MB)</p>
                                    </div>
                                    <label for="subcategory-edit-image-input" class="shrink-0 px-3.5 py-1.5 rounded-lg bg-gradient-to-r from-[#C5A059] to-[#D4AF37] hover:from-[#B8903B] hover:to-[#C5A059] text-slate-950 font-bold text-xs cursor-pointer inline-flex items-center gap-1.5 shadow-2xs">
                                        <span x-text="imagePreview ? 'Change' : '{{ $subcategory->image ? 'Replace' : 'Browse' }}'">Browse</span>
                                    </label>
                                    <input
                                        type="file"
                                        name="image"
                                        id="subcategory-edit-image-input"
                                        accept="image/*"
                                        @change="handleImageSelect($event)"
                                        class="sr-only"
                                    >
                                </div>
                                @if ($subcategory->image)
                                    <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 font-semibold cursor-pointer select-none">
                                        <input
                                            type="checkbox"
                                            name="remove_image"
                                            value="1"
                                            x-model="removeImage"
                                            @change="if(removeImage) { cancelNewImage(); }"
                                            class="rounded text-rose-600 focus:ring-rose-500 text-xs cursor-pointer"
                                        >
                                        <span>Remove existing image</span>
                                    </label>
                                @endif
                                @error('image')
                                    <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
                    {{-- Parent Category Selection --}}
                    <x-select label="Parent Category" name="category_id" required>
                        <option value="">-- Choose Parent Category --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $subcategory->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </x-select>

                    {{-- Name --}}
                    <x-input
                        label="Subcategory Name"
                        name="name"
                        :value="$subcategory->name"
                        placeholder="e.g. Master Space Planning"
                        required
                        autofocus
                    />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
                    {{-- Slug --}}
                    <div class="sm:col-span-2">
                        <x-input
                            label="Custom Slug"
                            name="slug"
                            :value="$subcategory->slug"
                            placeholder="e.g. master-space-planning"
                            hint="Leave blank to re-sync with name."
                        />
                    </div>

                    {{-- Status --}}
                    <div>
                        <x-select label="Publication Status" name="status" required>
                            <option value="active" {{ old('status', $subcategory->status) === 'active' ? 'selected' : '' }}>Active (Public & Listed)</option>
                            <option value="inactive" {{ old('status', $subcategory->status) === 'inactive' ? 'selected' : '' }}>Inactive / Draft</option>
                        </x-select>
                    </div>
                </div>

                {{-- Description --}}
                <div class="mb-6">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Subcategory Description</label>
                    <textarea
                        name="description"
                        rows="4"
                        placeholder="Describe the specialized trade scope, materials or deliverables included..."
                        class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-xs text-slate-900 bg-white placeholder-slate-400 focus:border-[#C5A059] focus:ring-[#C5A059]"
                    >{{ old('description', $subcategory->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <x-slot:footer>
                    <div class="flex items-center justify-end gap-3 w-full">
                        <x-button href="{{ route('dashboard.subcategories.index') }}" variant="secondary">
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
