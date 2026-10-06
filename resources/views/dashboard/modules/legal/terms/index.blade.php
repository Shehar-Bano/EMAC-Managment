<x-dashboard.layout :title="'Terms & Conditions — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">Terms & Conditions</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Terms & Conditions Management</h1>
            <p class="text-xs text-slate-500 mt-1">Manage public contractual terms, house plan licensing rules, and customer agreements</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('terms') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 shadow-xs transition-colors">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                View Public Page
            </a>

            @can('terms.edit')
                <x-button href="{{ route('dashboard.terms.edit') }}" variant="primary" size="sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit Terms Document
                </x-button>
            @endcan
        </div>
    </x-slot:header>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-medium flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Document Overview Card --}}
        <div class="lg:col-span-1 space-y-6">
            <x-card title="Document Metadata" subtitle="Version & publication status">
                <div class="space-y-4 text-xs">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <span class="text-slate-500">Document Type</span>
                        <span class="font-bold text-slate-800 uppercase">Terms & Conditions</span>
                    </div>

                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <span class="text-slate-500">Document Status</span>
                        @if(($document->status ?? 'active') === 'active')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                                Active (Public)
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-800">
                                Inactive / Draft
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <span class="text-slate-500">Current Version</span>
                        <span class="font-bold text-slate-900 font-mono">v{{ $document->version ?? '1.0' }}</span>
                    </div>

                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <span class="text-slate-500">Effective Date</span>
                        <span class="font-medium text-slate-800">{{ $document->effective_date?->format('F d, Y') ?? 'September 01, 2026' }}</span>
                    </div>

                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <span class="text-slate-500">Last Modified</span>
                        <span class="font-medium text-slate-800">{{ $document->updated_at?->diffForHumans() ?? 'Recently' }}</span>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Document Live Preview Card --}}
        <div class="lg:col-span-2">
            <x-card title="{{ $document->title ?? 'Terms and Conditions' }}" subtitle="Live preview of legal content">
                <div class="prose prose-slate max-w-none text-xs sm:text-sm text-slate-700 leading-relaxed space-y-4">
                    {!! $document->content ?? '<p class="text-slate-400 italic">No content configured.</p>' !!}
                </div>
            </x-card>
        </div>
    </div>

</x-dashboard.layout>
