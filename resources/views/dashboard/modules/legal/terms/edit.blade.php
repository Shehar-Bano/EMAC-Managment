<x-dashboard.layout :title="'Edit Terms & Conditions — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <a href="{{ route('dashboard.terms.index') }}" class="text-slate-500 hover:text-slate-700">Terms & Conditions</a>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">Edit</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Edit Terms & Conditions</h1>
            <p class="text-xs text-slate-500 mt-1">Update legal clauses, version number, and publication status with rich text formatting</p>
        </div>

        <div class="flex items-center gap-2.5">
            <x-button href="{{ route('dashboard.terms.index') }}" variant="secondary" size="sm">
                Cancel
            </x-button>
        </div>
    </x-slot:header>

    @push('styles')
        <style>
            .ck-editor__editable_inline {
                min-height: 420px;
                font-size: 14px;
                line-height: 1.7;
                color: #1e293b;
            }
            .ck.ck-editor__main>.ck-editor__editable:focus {
                border-color: #C5A059 !important;
                box-shadow: 0 0 0 3px rgba(197, 160, 89, 0.2) !important;
            }
            .ck.ck-toolbar {
                background: #F8FAFC !important;
                border-color: #E2E8F0 !important;
                border-top-left-radius: 0.5rem !important;
                border-top-right-radius: 0.5rem !important;
            }
            .ck.ck-content {
                border-bottom-left-radius: 0.5rem !important;
                border-bottom-right-radius: 0.5rem !important;
                border-color: #E2E8F0 !important;
            }
        </style>
    @endpush

    <div class="max-w-5xl">
        <form method="POST" action="{{ route('dashboard.terms.update') }}" id="legal-form">
            @csrf
            @method('PUT')

            <x-card title="Terms and Conditions Content" subtitle="Update legal language and metadata">
                <div class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div class="sm:col-span-2">
                            <x-input
                                label="Document Title"
                                name="title"
                                :value="old('title', $document->title)"
                                required
                            />
                        </div>

                        <div>
                            <x-input
                                label="Version Number"
                                name="version"
                                :value="old('version', $document->version)"
                                required
                                placeholder="1.0"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <x-input
                            label="Effective Date"
                            name="effective_date"
                            type="date"
                            :value="old('effective_date', $document->effective_date?->format('Y-m-d'))"
                        />

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Publication Status <span class="text-rose-500">*</span>
                            </label>
                            <select
                                name="status"
                                class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm text-slate-900 focus:border-[#C5A059] focus:ring-2 focus:ring-[#C5A059]/20"
                                required
                            >
                                <option value="active" {{ old('status', $document->status) === 'active' ? 'selected' : '' }}>Active (Published to Website & API)</option>
                                <option value="inactive" {{ old('status', $document->status) === 'inactive' ? 'selected' : '' }}>Inactive / Draft (Hidden from Public)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Terms Content (Rich Text Editor) <span class="text-rose-500">*</span>
                        </label>
                        <textarea
                            name="content"
                            id="editor"
                            rows="16"
                            class="block w-full rounded-lg border border-slate-300 bg-white p-4 text-sm text-slate-900"
                        >{{ old('content', $document->content) }}</textarea>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <x-button href="{{ route('dashboard.terms.index') }}" variant="secondary">
                            Cancel
                        </x-button>

                        <x-button type="submit" variant="primary">
                            Save & Publish Terms
                        </x-button>
                    </div>
                </div>
            </x-card>
        </form>
    </div>

    @push('scripts')
        <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                ClassicEditor
                    .create(document.querySelector('#editor'), {
                        toolbar: {
                            items: [
                                'heading', '|',
                                'bold', 'italic', 'underline', 'link', '|',
                                'bulletedList', 'numberedList', 'blockQuote', '|',
                                'insertTable', 'undo', 'redo'
                            ]
                        },
                        heading: {
                            options: [
                                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                            ]
                        }
                    })
                    .then(editor => {
                        window.editor = editor;
                    })
                    .catch(error => {
                        console.error('CKEditor initialization error:', error);
                    });
            });
        </script>
    @endpush

</x-dashboard.layout>
