<x-dashboard.layout :title="'Create Category — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <a href="{{ route('dashboard.categories.index') }}" class="hover:text-[#C5A059]">Categories</a>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">Add New Category</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Add New Category</h1>
            <p class="text-xs text-slate-500 mt-1">Create a top-level commercial, residential or architectural classification</p>
        </div>

        <div>
            <x-button href="{{ route('dashboard.categories.index') }}" variant="secondary" size="sm">
                &larr; Back to Categories
            </x-button>
        </div>
    </x-slot:header>

    <div class="max-w-4xl">
        <form method="POST" action="{{ route('dashboard.categories.store') }}" enctype="multipart/form-data" class="space-y-6" x-data="{
            imagePreview: null,
            fileName: '',
            handleFileSelect(event) {
                const file = event.target.files[0];
                if (file) {
                    this.fileName = file.name;
                    const reader = new FileReader();
                    reader.onload = (e) => { this.imagePreview = e.target.result; };
                    reader.readAsDataURL(file);
                }
            },
            clearImage() {
                this.imagePreview = null;
                this.fileName = '';
                document.getElementById('category-image-input').value = '';
            }
        }">
            @csrf

            <x-card title="Category Details & Visual Image" subtitle="Upload category image and define commercial parameters">
                {{-- Category Image Upload Component --}}
                <div class="mb-6 p-5 rounded-2xl bg-[#FAF8F4] border border-[#C5A059]/30 shadow-2xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                        <label class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#8F6B20]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Category Icon / Image
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
                                    <img :src="imagePreview" alt="Preview" class="w-20 h-20 rounded-2xl object-cover ring-2 ring-[#C5A059] shadow-md">
                                    <button
                                        type="button"
                                        @click="clearImage()"
                                        class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-rose-600 text-white flex items-center justify-center hover:bg-rose-700 shadow-sm transition-transform hover:scale-110"
                                        title="Remove selected image"
                                    >
                                        &times;
                                    </button>
                                </div>
                            </template>
                            <template x-if="!imagePreview">
                                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-[#E5C158]/15 to-[#C5A059]/25 border-2 border-dashed border-[#C5A059]/50 flex flex-col items-center justify-center text-slate-400 group-hover:border-[#C5A059] transition-colors">
                                    <svg class="w-8 h-8 text-[#8F6B20]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="text-[10px] font-semibold text-[#8F6B20] mt-0.5">128&times;128 px</span>
                                </div>
                            </template>
                        </div>

                        {{-- Upload Drop Area --}}
                        <div class="flex-1 w-full">
                            <div class="relative border-2 border-dashed border-slate-300 hover:border-[#C5A059] rounded-xl p-4 transition-colors bg-white/80 hover:bg-white text-center sm:text-left flex flex-col sm:flex-row items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold text-slate-800" x-text="fileName ? 'Selected: ' + fileName : 'Select an icon image file from your computer'"></p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Recommended icon size: <strong>Width: 128px &bull; Height: 128px</strong> (Square 1:1 Icon format, 64&times;64px to 256&times;256px)</p>
                                </div>
                                <label for="category-image-input" class="shrink-0 px-4 py-2 rounded-lg bg-gradient-to-r from-[#C5A059] to-[#D4AF37] hover:from-[#B8903B] hover:to-[#C5A059] text-slate-950 font-bold text-xs shadow-xs hover:shadow transition-all cursor-pointer inline-flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    <span x-text="imagePreview ? 'Change Image' : 'Browse Icon'">Browse Icon</span>
                                </label>
                                <input
                                    type="file"
                                    name="image"
                                    id="category-image-input"
                                    accept="image/*"
                                    @change="handleFileSelect($event)"
                                    class="sr-only"
                                >
                            </div>
                            @error('image')
                                <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
                    {{-- Name --}}
                    <div class="sm:col-span-2">
                        <x-input
                            label="Category Name"
                            name="name"
                            placeholder="e.g. Architectural Design & Planning"
                            required
                            autofocus
                        />
                    </div>

                    {{-- Status --}}
                    <div>
                        <x-select label="Publication Status" name="status" required>
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active (Public & Listed)</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive / Draft</option>
                        </x-select>
                    </div>
                </div>

                <div class="mb-6">
                    {{-- Slug (Optional) --}}
                    <x-input
                        label="Custom URL Slug (Optional)"
                        name="slug"
                        placeholder="e.g. architectural-design"
                        hint="Leave blank to automatically generate from category name."
                    />
                </div>

                {{-- Description --}}
                <div class="mb-6">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Category Description</label>
                    <textarea
                        name="description"
                        rows="4"
                        placeholder="Provide a comprehensive summary of services and scopes governed under this category..."
                        class="block w-full rounded-xl border border-slate-300 px-4 py-3 text-xs text-slate-900 bg-white placeholder-slate-400 focus:border-[#C5A059] focus:ring-[#C5A059]"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <x-slot:footer>
                    <div class="flex items-center justify-end gap-3 w-full">
                        <x-button href="{{ route('dashboard.categories.index') }}" variant="secondary">
                            Cancel
                        </x-button>
                        <x-button type="submit" variant="primary">
                            Create Category
                        </x-button>
                    </div>
                </x-slot:footer>
            </x-card>
        </form>
    </div>

</x-dashboard.layout>
