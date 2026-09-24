<x-dashboard.layout :title="'Service Request #REQ-' . str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT) . ' — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <a href="{{ route('dashboard.service-requests.index') }}" class="hover:text-[#8F6B20]">Customer Requests</a>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">#REQ-{{ str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT) }}</span>
    </x-slot:breadcrumbs>

    @php
        $priorityVal = $serviceRequest->priority instanceof \BackedEnum ? $serviceRequest->priority->value : $serviceRequest->priority;
        $statusVal = $serviceRequest->status instanceof \BackedEnum ? $serviceRequest->status->value : $serviceRequest->status;
    @endphp

    <x-slot:header>
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Request #REQ-{{ str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT) }}</h1>
                <span class="px-3 py-1 rounded-full text-xs font-bold
                    @if ($statusVal === 'pending') bg-amber-100 text-amber-800 border border-amber-300
                    @elseif($statusVal === 'in_review') bg-blue-100 text-blue-800 border border-blue-300
                    @elseif($statusVal === 'approved') bg-indigo-100 text-indigo-800 border border-indigo-300
                    @elseif($statusVal === 'in_progress') bg-purple-100 text-purple-800 border border-purple-300
                    @elseif($statusVal === 'completed') bg-emerald-100 text-emerald-800 border border-emerald-300
                    @else bg-rose-100 text-rose-800 border border-rose-300 @endif
                ">
                    Status: {{ ucfirst(str_replace('_', ' ', $statusVal)) }}
                </span>

                @if ($priorityVal === 'emergency')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-rose-100 text-rose-800 border border-rose-300">
                        <span class="w-2 h-2 rounded-full bg-rose-600 animate-ping"></span>
                        Emergency Priority
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-1">Submitted on {{ $serviceRequest->created_at->format('F d, Y \a\t h:i A') }} ({{ $serviceRequest->created_at->diffForHumans() }})</p>
        </div>

        <div class="flex items-center gap-2.5">
            <x-button href="{{ route('dashboard.service-requests.index') }}" variant="secondary" size="sm">
                &larr; Back to Requests
            </x-button>

            @can('service_requests.delete')
                <form method="POST" action="{{ route('dashboard.service-requests.destroy', $serviceRequest) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button
                        type="button"
                        data-confirm-delete="Are you sure you want to delete this service request record?"
                        class="px-3.5 py-2 text-xs font-semibold rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 transition-colors cursor-pointer"
                    >
                        Delete Request
                    </button>
                </form>
            @endcan
        </div>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Main Request Content --}}
        <div class="lg:col-span-8 space-y-6">
            {{-- Written Description --}}
            <div class="p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs">
                <h2 class="text-base font-bold text-slate-900 mb-4 pb-3 border-b border-slate-100 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Written Description
                </h2>
                <div class="prose prose-slate max-w-none text-sm text-slate-700 leading-relaxed whitespace-pre-line bg-[#FAF8F4] p-5 rounded-2xl border border-slate-200/80">
                    {{ $serviceRequest->description }}
                </div>
            </div>

            {{-- Property Information --}}
            <div class="p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs">
                <h2 class="text-base font-bold text-slate-900 mb-4 pb-3 border-b border-slate-100 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Property Information
                </h2>
                <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50 p-5 rounded-2xl border border-slate-200/80">
                    {{ $serviceRequest->property_information }}
                </div>
            </div>

            {{-- Additional Notes --}}
            @if ($serviceRequest->additional_notes)
                <div class="p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs">
                    <h2 class="text-base font-bold text-slate-900 mb-4 pb-3 border-b border-slate-100 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Additional Notes / Customer Instructions
                    </h2>
                    <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line bg-amber-50/40 p-5 rounded-2xl border border-amber-200/60">
                        {{ $serviceRequest->additional_notes }}
                    </div>
                </div>
            @endif

            {{-- Attached Photographs --}}
            <div class="p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Attached Photographs ({{ $serviceRequest->photographs->count() }})
                    </h2>
                </div>

                @if ($serviceRequest->photographs->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach ($serviceRequest->photographs as $photo)
                            <div class="group relative rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 aspect-square shadow-2xs">
                                <img
                                    src="{{ asset('storage/' . $photo->file_path) }}"
                                    alt="{{ $photo->file_name ?? 'Photograph' }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                >
                                <a
                                    href="{{ asset('storage/' . $photo->file_path) }}"
                                    target="_blank"
                                    class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white font-semibold text-xs gap-1"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    View Full
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 rounded-2xl bg-slate-50 text-center text-xs text-slate-400">
                        No photographs attached to this service request.
                    </div>
                @endif
            </div>

            {{-- Attached Videos --}}
            <div class="p-8 rounded-3xl bg-white border border-slate-200/90 shadow-xs">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        Attached Videos ({{ $serviceRequest->videos->count() }})
                    </h2>
                </div>

                @if ($serviceRequest->videos->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($serviceRequest->videos as $video)
                            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50 space-y-3">
                                <video controls class="w-full rounded-xl bg-black max-h-60">
                                    <source src="{{ asset('storage/' . $video->file_path) }}" type="{{ $video->mime_type ?? 'video/mp4' }}">
                                    Your browser does not support the video tag.
                                </video>
                                <div class="flex items-center justify-between text-xs text-slate-500">
                                    <span class="truncate" title="{{ $video->file_name }}">{{ $video->file_name ?? 'Video File' }}</span>
                                    <a href="{{ asset('storage/' . $video->file_path) }}" target="_blank" download class="text-[#8F6B20] font-semibold hover:underline shrink-0">
                                        Download
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 rounded-2xl bg-slate-50 text-center text-xs text-slate-400">
                        No videos attached to this service request.
                    </div>
                @endif
            </div>
        </div>

        {{-- Metadata & Actions Sidebar --}}
        <div class="lg:col-span-4 space-y-6">
            {{-- Status Workflow Form --}}
            @can('service_requests.status')
                <div class="p-6 rounded-3xl bg-white border border-[#C5A059]/40 shadow-xs">
                    <h3 class="text-sm font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                        <span>Workflow Status</span>
                        <span class="w-2 h-2 rounded-full bg-[#C5A059]"></span>
                    </h3>
                    <form method="POST" action="{{ route('dashboard.service-requests.status', $serviceRequest) }}" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Update Status</label>
                            <select name="status" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:border-[#C5A059] focus:ring-1 focus:ring-[#C5A059] font-semibold">
                                <option value="pending" {{ $statusVal === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_review" {{ $statusVal === 'in_review' ? 'selected' : '' }}>In Review</option>
                                <option value="approved" {{ $statusVal === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="in_progress" {{ $statusVal === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ $statusVal === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $statusVal === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full py-2.5 px-4 text-xs font-bold text-slate-950 bg-gradient-to-r from-[#D4AF37] to-[#C5A059] rounded-xl hover:brightness-105 shadow-2xs transition-all cursor-pointer">
                            Update Workflow Status
                        </button>
                    </form>
                </div>
            @endcan

            {{-- Customer Information Card --}}
            <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100">Customer Details</h3>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-900 text-[#C5A059] flex items-center justify-center font-bold text-sm shrink-0">
                        {{ strtoupper(substr($serviceRequest->user?->name ?? 'C', 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-bold text-slate-900 text-sm">{{ $serviceRequest->user?->name ?? 'N/A' }}</div>
                        <div class="text-xs text-slate-400">Customer ID: #{{ $serviceRequest->user_id }}</div>
                    </div>
                </div>

                <div class="space-y-2.5 pt-2 border-t border-slate-100 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Email:</span>
                        <a href="mailto:{{ $serviceRequest->user?->email }}" class="font-semibold text-slate-800 hover:text-[#8F6B20]">
                            {{ $serviceRequest->user?->email ?? 'N/A' }}
                        </a>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Phone:</span>
                        <a href="tel:{{ $serviceRequest->user?->phone }}" class="font-semibold text-slate-800 hover:text-[#8F6B20]">
                            {{ $serviceRequest->user?->phone ?? 'N/A' }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- Service Location / Address Card --}}
            <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-3">
                <h3 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Service Location
                </h3>
                @if ($serviceRequest->address)
                    <div class="text-xs text-slate-700 leading-relaxed bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                        <div class="font-bold text-slate-900">{{ $serviceRequest->address->address }}</div>
                        <div class="text-slate-500 mt-0.5">
                            {{ $serviceRequest->address->city }}{{ $serviceRequest->address->state ? ', ' . $serviceRequest->address->state : '' }}
                        </div>
                        <div class="text-slate-500 font-semibold">{{ $serviceRequest->address->country }}</div>
                    </div>
                @else
                    <div class="text-xs text-slate-400 bg-slate-50 p-3.5 rounded-xl">
                        No specific address record linked.
                    </div>
                @endif
            </div>

            {{-- Schedule & Priority Card --}}
            <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-3">
                <h3 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100">Schedule & Priority</h3>
                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Preferred Date:</span>
                        <span class="font-bold text-slate-900">{{ $serviceRequest->preferred_service_date?->format('F d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Preferred Time:</span>
                        <span class="font-bold text-slate-900">{{ $serviceRequest->preferred_service_time }}</span>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                        <span class="text-slate-400">Priority Level:</span>
                        <span class="font-bold uppercase
                            @if ($priorityVal === 'emergency') text-rose-600
                            @elseif ($priorityVal === 'high') text-amber-600
                            @elseif ($priorityVal === 'medium') text-blue-600
                            @else text-slate-600 @endif
                        ">
                            {{ $priorityVal }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-dashboard.layout>
