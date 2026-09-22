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
            imagePreview: null,
            fileName: '',
            removeImage: false,
            handleFileSelect(event) {
                const file = event.target.files[0];
                if (file) {
                    this.fileName = file.name;
                    this.removeImage = false;
                    const reader = new FileReader();
                    reader.onload = (e) => { this.imagePreview = e.target.result; };
                    reader.readAsDataURL(file);
                }
            },
            cancelNewImage() {
                this.imagePreview = null;
                this.fileName = '';
                document.getElementById('subcategory-edit-image-input').value = '';
            }
        }">
            @csrf
            @method('PUT')

            <x-card title="Subcategory Details & Visual Image" subtitle="Subcategory ID: #{{ $subcategory->id }}">
                {{-- Subcategory Image Upload Component --}}
                <div class="mb-6 p-5 rounded-2xl bg-[#FAF8F4] border border-[#C5A059]/30 shadow-2xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                        <label class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#8F6B20]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Subcategory Icon / Image
                        </label>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#C5A059]/20 text-[#8F6B20] border border-[#C5A059]/30">
                                Dimensions: 128px (W) &times; 128px (H) [1:1 Icon]
                            </span>
                            <span class="text-[11px] text-slate-500">Max 2MB (SVG, PNG, WEBP, JPG)</span>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-5">
                        {{-- Preview Box --}}
                        <div class="relative shrink-0 group">
                            <template x-if="imagePreview">
                                <div class="relative">
                                    <img :src="imagePreview" alt="New Preview" class="w-20 h-20 rounded-2xl object-cover ring-2 ring-[#C5A059] shadow-md">
                                    <button
                                        type="button"
                                        @click="cancelNewImage()"
                                        class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-rose-600 text-white flex items-center justify-center hover:bg-rose-700 shadow-sm transition-transform hover:scale-110"
                                        title="Cancel new image"
                                    >
                                        &times;
                                    </button>
                                </div>
                            </template>
                            <template x-if="!imagePreview">
                                <div>
                                    @if ($subcategory->image_url)
                                        <div class="relative">
                                            <img
                                                src="{{ $subcategory->image_url }}"
                                                alt="{{ $subcategory->name }}"
                                                class="w-20 h-20 rounded-2xl object-cover ring-2 ring-[#C5A059]/40 shadow-sm transition-opacity"
                                                :class="{ 'opacity-30 grayscale ring-rose-400': removeImage }"
                                            >
                                            <template x-if="removeImage">
                                                <span class="absolute inset-0 flex items-center justify-center text-[10px] font-bold text-rose-700 bg-rose-50/80 rounded-2xl">Marked for Deletion</span>
                                            </template>
                                        </div>
                                    @else
                                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-[#E5C158]/15 to-[#C5A059]/25 border-2 border-dashed border-[#C5A059]/50 flex flex-col items-center justify-center text-slate-400">
                                            <svg class="w-9 h-9 text-[#8F6B20]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span class="text-[10px] font-semibold text-[#8F6B20] mt-1">128&times;128 px</span>
                                        </div>
                                    @endif
                                </div>
                            </template>
                        </div>

                        {{-- Upload Drop Area & Controls --}}
                        <div class="flex-1 w-full space-y-2.5">
                            <div class="relative border-2 border-dashed border-slate-300 hover:border-[#C5A059] rounded-xl p-4 transition-colors bg-white/80 hover:bg-white text-center sm:text-left flex flex-col sm:flex-row items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold text-slate-800" x-text="fileName ? 'Selected: ' + fileName : '{{ $subcategory->image ? 'Select a replacement icon file' : 'Select an icon image file from your computer' }}'"></p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Recommended icon size: <strong>Width: 128px &bull; Height: 128px</strong> (Square 1:1 Icon format, 64&times;64px to 256&times;256px)</p>
                                </div>
                                <label for="subcategory-edit-image-input" class="shrink-0 px-4 py-2 rounded-lg bg-gradient-to-r from-[#C5A059] to-[#D4AF37] hover:from-[#B8903B] hover:to-[#C5A059] text-slate-950 font-bold text-xs shadow-xs hover:shadow transition-all cursor-pointer inline-flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    <span x-text="imagePreview ? 'Change Selection' : '{{ $subcategory->image ? 'Replace Icon' : 'Browse Icon' }}'">Browse Icon</span>
                                </label>
                                <input
                                    type="file"
                                    name="image"
                                    id="subcategory-edit-image-input"
                                    accept="image/*"
                                    @change="handleFileSelect($event)"
                                    class="sr-only"
                                >
                            </div>

                            @if ($subcategory->image)
                                <div class="flex items-center justify-between px-1">
                                    <label class="inline-flex items-center gap-2 text-xs text-rose-600 font-semibold cursor-pointer select-none">
                                        <input
                                            type="checkbox"
                                            name="remove_image"
                                            value="1"
                                            x-model="removeImage"
                                            @change="if(removeImage) { cancelNewImage(); }"
                                            class="rounded text-rose-600 focus:ring-rose-500"
                                        >
                                        <span>Remove existing icon file</span>
                                    </label>
                                </div>
                            @endif

                            @error('image')
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
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
