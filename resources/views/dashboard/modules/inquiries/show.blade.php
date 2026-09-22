<x-dashboard.layout :title="'Inquiry from ' . $inquiry->name . ' — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <a href="{{ route('dashboard.inquiries.index') }}" class="hover:text-[#8F6B20]">Leads & Inquiries</a>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">#{{ $inquiry->id }}</span>
    </x-slot:breadcrumbs>

    <x-slot:header>
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $inquiry->name }}</h1>
                <span class="px-3 py-1 rounded-full text-xs font-bold
                    @if ($inquiry->status === 'new') bg-amber-100 text-amber-800 border border-amber-300
                    @elseif($inquiry->status === 'contacted') bg-blue-100 text-blue-800 border border-blue-300
                    @elseif($inquiry->status === 'in_progress') bg-purple-100 text-purple-800 border border-purple-300
                    @else bg-emerald-100 text-emerald-800 border border-emerald-300 @endif
                ">
                    Status: {{ ucfirst(str_replace('_', ' ', $inquiry->status)) }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Submitted on {{ $inquiry->created_at->format('F d, Y \a\t h:i A') }} ({{ $inquiry->created_at->diffForHumans() }})</p>
        </div>

        <div class="flex items-center gap-2.5">
            <x-button href="{{ route('dashboard.inquiries.index') }}" variant="secondary" size="sm">
                &larr; Back to Inquiries
            </x-button>

            @can('inquiries.delete')
                <form method="POST" action="{{ route('dashboard.inquiries.destroy', $inquiry) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button
                        type="button"
                        data-confirm-delete="Are you sure you want to delete this lead record?"
                        class="px-3.5 py-2 text-xs font-semibold rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 transition-colors cursor-pointer"
                    >
                        Delete Inquiry
                    </button>
                </form>
            @endcan
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Inquiry Content --}}
        <div class="lg:col-span-8 space-y-6">
            <div class="p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs">
                <h2 class="text-base font-bold text-slate-900 mb-4 pb-3 border-b border-slate-100">Project / Service Request Details</h2>
                <div class="prose prose-slate max-w-none text-sm text-slate-700 leading-relaxed whitespace-pre-line bg-[#FAF8F4] p-5 rounded-2xl border border-slate-200/80">
                    {{ $inquiry->message }}
                </div>
            </div>

            {{-- Communication Actions --}}
            <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex flex-wrap items-center justify-between gap-4">
                <div class="text-xs text-slate-600">
                    <span class="font-bold text-slate-900">Direct Actions:</span> Reply directly to this client via email or phone.
                </div>
                <div class="flex items-center gap-2">
                    <a href="mailto:{{ $inquiry->email }}?subject=EMAC Development Inquiry Follow-up - {{ urlencode($inquiry->market) }}" class="px-4 py-2 text-xs font-bold text-slate-950 bg-gradient-to-r from-[#D4AF37] to-[#C5A059] rounded-xl hover:brightness-105 shadow-2xs transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span>Send Email Reply</span>
                    </a>
                    @if ($inquiry->phone)
                        <a href="tel:{{ $inquiry->phone }}" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span>Call Client</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Metadata & Status Sidebar --}}
        <div class="lg:col-span-4 space-y-6">
            {{-- Status Update Form --}}
            @can('inquiries.status')
                <div class="p-6 rounded-3xl bg-white border border-[#C5A059]/30 shadow-xs">
                    <h3 class="text-sm font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">Update Lead Workflow Status</h3>
                    <form method="POST" action="{{ route('dashboard.inquiries.status', $inquiry) }}" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status</label>
                            <select name="status" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] font-semibold">
                                <option value="new" {{ $inquiry->status === 'new' ? 'selected' : '' }}>New Unread Lead</option>
                                <option value="contacted" {{ $inquiry->status === 'contacted' ? 'selected' : '' }}>Contacted / Replied</option>
                                <option value="in_progress" {{ $inquiry->status === 'in_progress' ? 'selected' : '' }}>In Progress / Quoted</option>
                                <option value="closed" {{ $inquiry->status === 'closed' ? 'selected' : '' }}>Closed / Completed</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full py-2.5 px-4 text-xs font-bold text-slate-950 bg-gradient-to-r from-[#D4AF37] to-[#C5A059] rounded-xl hover:brightness-105 shadow-2xs transition-all cursor-pointer">
                            Update Status
                        </button>
                    </form>
                </div>
            @endcan

            {{-- Client Contact Summary --}}
            <div class="p-6 rounded-3xl bg-white border border-slate-200/80 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100">Client Information</h3>

                <div class="text-xs">
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Full Name</span>
                    <span class="font-bold text-slate-900">{{ $inquiry->name }}</span>
                </div>

                <div class="text-xs">
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Email Address</span>
                    <a href="mailto:{{ $inquiry->email }}" class="font-mono text-[#8F6B20] hover:underline">{{ $inquiry->email }}</a>
                </div>

                <div class="text-xs">
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Phone</span>
                    <span class="font-mono text-slate-800">{{ $inquiry->phone ?? 'Not Provided' }}</span>
                </div>

                <div class="text-xs">
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Regional Market</span>
                    <span class="font-bold text-slate-900">
                        @if ($inquiry->market === 'Cayman Islands') 🇰🇾 @elseif($inquiry->market === 'Florida') 🇺🇸 @elseif($inquiry->market === 'Jamaica') 🇯🇲 @else 🌐 @endif
                        {{ $inquiry->market }}
                    </span>
                </div>

                <div class="text-xs">
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Service Division</span>
                    <span class="font-semibold text-[#8F6B20]">
                        {{ $inquiry->category ? $inquiry->category->name : 'General Consultation' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

</x-dashboard.layout>
