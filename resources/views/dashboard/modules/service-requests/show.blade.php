<x-dashboard.layout :title="'Service Request #REQ-' . str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT) . ' — EMAC Development ERP'">

    <x-slot:breadcrumbs>
        <span class="text-slate-400">/</span>
        <a href="{{ route('dashboard.service-requests.index') }}" class="hover:text-[#8F6B20] transition-colors">Customer Requests</a>
        <span class="text-slate-400">/</span>
        <span class="font-semibold text-slate-800">#REQ-{{ str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT) }}</span>
    </x-slot:breadcrumbs>

    @php
        $priorityVal = $serviceRequest->priority instanceof \BackedEnum ? $serviceRequest->priority->value : $serviceRequest->priority;
        $reqFormatted = '#REQ-' . str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT);
        $customerName = $serviceRequest->user?->name ?? 'Customer';
        $quotesCount = $serviceRequest->quotes->count();
        $fullAddress = $serviceRequest->address ? trim($serviceRequest->address->address . ', ' . $serviceRequest->address->city . ', ' . ($serviceRequest->address->state ? $serviceRequest->address->state . ', ' : '') . $serviceRequest->address->country) : null;
    @endphp

    <x-slot:header>
        <div class="space-y-1">
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 flex items-center gap-2">
                    <span>Request</span>
                    <span class="font-mono text-[#8F6B20] bg-[#FAF8F4] px-3 py-0.5 rounded-xl border border-[#C5A059]/30 text-xl sm:text-2xl shadow-2xs">{{ $reqFormatted }}</span>
                </h1>

                @if ($priorityVal === 'emergency')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-rose-100 text-rose-800 border border-rose-300 shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-rose-600 animate-ping"></span>
                        Emergency Priority
                    </span>
                @elseif ($priorityVal === 'high')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300 shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        High Priority
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 flex flex-wrap items-center gap-2">
                <span>Submitted by <strong class="text-slate-700 font-semibold">{{ $customerName }}</strong></span>
                <span>&bull;</span>
                <span>{{ $serviceRequest->created_at->format('F d, Y \a\t h:i A') }}</span>
                <span class="text-slate-400">({{ $serviceRequest->created_at->diffForHumans() }})</span>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            @can('quotes.create')
                <button
                    type="button"
                    data-request-id="{{ $serviceRequest->id }}"
                    data-request-number="{{ $reqFormatted }}"
                    data-customer-name="{{ $customerName }}"
                    data-description="{{ $serviceRequest->description }}"
                    onclick="openQuoteModalFromButton(this)"
                    style="background: linear-gradient(135deg, #D4AF37 0%, #B8903B 50%, #8F6B20 100%);"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-extrabold rounded-xl text-white hover:brightness-110 shadow-md hover:shadow-lg transition-all cursor-pointer"
                >
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Send / Issue Quote</span>
                </button>
            @endcan

            <x-button href="{{ route('dashboard.service-requests.index') }}" variant="secondary" size="sm">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Requests
            </x-button>

            @can('service_requests.delete')
                <form method="POST" action="{{ route('dashboard.service-requests.destroy', $serviceRequest) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button
                        type="button"
                        data-confirm-delete="Are you sure you want to delete this service request record?"
                        class="px-3 py-2 text-xs font-semibold rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 transition-colors cursor-pointer"
                        title="Delete this request"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </form>
            @endcan
        </div>
    </x-slot:header>

    <div x-data="{ activeImage: null }" class="space-y-6">

        {{-- Top Executive Info Bar: Customer, Location, Schedule & Quotation (All Info At A Glance) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            {{-- 1. Customer Details --}}
            <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-2.5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-900 text-[#C5A059] flex items-center justify-center font-bold text-sm shrink-0 border border-amber-500/20 shadow-2xs">
                        {{ strtoupper(substr($customerName, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Customer</div>
                        <div class="text-xs font-bold text-slate-900 truncate">{{ $customerName }}</div>
                        <div class="text-[10px] text-slate-400">ID: #{{ $serviceRequest->user_id }}</div>
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-100 space-y-1 text-xs">
                    <div class="flex items-center justify-between gap-1">
                        <span class="text-slate-400 text-[11px]">Phone:</span>
                        @if ($serviceRequest->user?->phone)
                            <a href="tel:{{ $serviceRequest->user->phone }}" class="font-semibold text-slate-800 hover:text-[#8F6B20] truncate">
                                {{ $serviceRequest->user->phone }}
                            </a>
                        @else
                            <span class="text-slate-400 text-[11px]">N/A</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between gap-1">
                        <span class="text-slate-400 text-[11px]">Email:</span>
                        @if ($serviceRequest->user?->email)
                            <a href="mailto:{{ $serviceRequest->user->email }}" class="font-semibold text-slate-800 hover:text-[#8F6B20] truncate max-w-[140px]" title="{{ $serviceRequest->user->email }}">
                                {{ $serviceRequest->user->email }}
                            </a>
                        @else
                            <span class="text-slate-400 text-[11px]">N/A</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 2. Location & Property --}}
            <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-2.5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-[#8F6B20] border border-slate-200 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Service Location</div>
                        <div class="text-xs font-bold text-slate-900 truncate">
                            {{ $serviceRequest->address?->city ?? 'Location' }}{{ $serviceRequest->address?->country ? ', ' . $serviceRequest->address->country : '' }}
                        </div>
                        <div class="text-[10px] text-slate-500 truncate">{{ $serviceRequest->address?->address ?? 'No specific address' }}</div>
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-100">
                    @if ($fullAddress)
                        <a
                            href="https://www.google.com/maps/search/?api=1&query={{ urlencode($fullAddress) }}"
                            target="_blank"
                            class="inline-flex items-center justify-center gap-1 w-full py-1 px-2 text-[11px] font-bold text-[#8F6B20] hover:text-[#C5A059] bg-[#FAF8F4] hover:bg-[#F5EFE6] border border-[#C5A059]/30 rounded-lg transition-colors"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            <span>Open in Google Maps</span>
                        </a>
                    @else
                        <div class="text-[11px] text-slate-400 text-center py-0.5">Address not attached</div>
                    @endif
                </div>
            </div>

            {{-- 3. Schedule & Priority --}}
            <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-2.5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-[#8F6B20] border border-[#C5A059]/30 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Schedule & Urgency</div>
                        <div class="text-xs font-bold text-slate-900 truncate">
                            {{ $serviceRequest->preferred_service_date ? $serviceRequest->preferred_service_date->format('M d, Y') : 'Immediate' }}
                        </div>
                        <div class="text-[10px] text-slate-500 truncate">{{ $serviceRequest->preferred_service_time ?? 'Flexible time' }}</div>
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-400 text-[11px]">Priority:</span>
                    <span class="font-extrabold uppercase text-[11px]
                        @if ($priorityVal === 'emergency') text-rose-600
                        @elseif ($priorityVal === 'high') text-amber-600
                        @elseif ($priorityVal === 'medium') text-blue-600
                        @else text-slate-700 @endif
                    ">
                        {{ $priorityVal }}
                    </span>
                </div>
            </div>

            {{-- 4. Quotes Dispatched --}}
            <div class="p-4 rounded-2xl bg-gradient-to-br from-[#FAF8F4] via-white to-[#FAF8F4] border border-[#C5A059]/40 shadow-2xs space-y-2.5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#C5A059]/20 text-[#8F6B20] border border-[#C5A059]/40 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-[#8F6B20]">Quotes Dispatched</div>
                        <div class="text-xs font-black text-slate-900">
                            {{ $quotesCount }} {{ Str::plural('Quote', $quotesCount) }}
                        </div>
                        <div class="text-[10px] font-semibold {{ $quotesCount > 0 ? 'text-emerald-600' : 'text-amber-600' }}">
                            {{ $quotesCount > 0 ? 'Active Quotation' : 'Awaiting Quote' }}
                        </div>
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-400 text-[11px]">Latest Value:</span>
                    <span class="font-black font-mono text-[#8F6B20] text-xs">
                        {{ $serviceRequest->latestQuote ? '$' . number_format($serviceRequest->latestQuote->total_price, 2) : '—' }}
                    </span>
                </div>
            </div>

        </div>

        {{-- Main Content Grid (Left 8 cols, Right 4 cols) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- Left Column (8 cols): Request Details, Scope, Photos, Videos & Quotes List --}}
            <div class="lg:col-span-8 space-y-6">

                {{-- Written Description / Scope of Work --}}
                <div class="p-6 sm:p-7 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-amber-50 border border-[#C5A059]/30 text-[#8F6B20] flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </span>
                            <span>Customer Request Description</span>
                        </h2>
                        <button
                            type="button"
                            onclick="navigator.clipboard.writeText(`{{ addslashes($serviceRequest->description) }}`); Swal.fire({toast: true, position: 'top-end', icon: 'success', title: 'Description copied to clipboard', showConfirmButton: false, timer: 1500});"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors cursor-pointer"
                            title="Copy Description"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            <span>Copy</span>
                        </button>
                    </div>

                    <div class="p-5 rounded-2xl bg-[#FAF8F4] border border-[#C5A059]/25 text-slate-800 text-sm leading-relaxed whitespace-pre-line font-medium selection:bg-[#C5A059]/30">
                        {{ $serviceRequest->description }}
                    </div>
                </div>

                {{-- Property & Site Details --}}
                <div class="p-6 sm:p-7 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 flex items-center justify-center">
                                <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </span>
                            <span>Property & Site Details</span>
                        </h2>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 text-slate-700 text-xs sm:text-sm leading-relaxed whitespace-pre-line font-medium">
                        {{ $serviceRequest->property_information }}
                    </div>
                </div>

                {{-- Customer Special Instructions (if present) --}}
                @if ($serviceRequest->additional_notes)
                    <div class="p-6 sm:p-7 rounded-3xl bg-gradient-to-br from-amber-50/70 via-white to-amber-50/40 border border-amber-200/80 shadow-xs space-y-3">
                        <h2 class="text-sm font-bold text-amber-900 flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            <span>Customer Special Instructions / Notes</span>
                        </h2>
                        <div class="p-4 rounded-2xl bg-white/90 border border-amber-200/60 text-xs sm:text-sm text-slate-800 leading-relaxed whitespace-pre-line font-medium">
                            {{ $serviceRequest->additional_notes }}
                        </div>
                    </div>
                @endif

                {{-- Attached Photographs Section --}}
                <div class="p-6 sm:p-7 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-amber-50 border border-[#C5A059]/30 text-[#8F6B20] flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </span>
                            <span>Attached Photographs</span>
                        </h2>
                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $serviceRequest->photographs->count() }} Photos
                        </span>
                    </div>

                    @if ($serviceRequest->photographs->count() > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @foreach ($serviceRequest->photographs as $photo)
                                @php
                                    $photoUrl = $photo->file_url ?? asset('storage/' . $photo->file_path);
                                @endphp
                                <div class="group relative rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 aspect-square shadow-2xs hover:shadow-md transition-all duration-300">
                                    <img
                                        src="{{ $photoUrl }}"
                                        alt="{{ $photo->file_name ?? 'Service Request Photograph' }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 cursor-pointer"
                                        @click="activeImage = '{{ $photoUrl }}'"
                                    >
                                    <div class="absolute inset-0 bg-slate-950/50 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-2 p-2 pointer-events-none">
                                        <button
                                            type="button"
                                            @click.stop="activeImage = '{{ $photoUrl }}'"
                                            class="pointer-events-auto px-3 py-1.5 rounded-lg bg-white/95 hover:bg-white text-slate-900 text-xs font-bold shadow-md transition-all cursor-pointer flex items-center gap-1.5"
                                        >
                                            <svg class="w-3.5 h-3.5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                                            <span>Zoom</span>
                                        </button>
                                        <a
                                            href="{{ $photoUrl }}"
                                            target="_blank"
                                            class="pointer-events-auto text-[11px] text-white/90 hover:text-white underline font-semibold"
                                        >
                                            Open Full Resolution &rarr;
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 rounded-2xl bg-slate-50 border border-dashed border-slate-200 text-center">
                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <p class="text-xs font-semibold text-slate-600">No photographs uploaded</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">The customer did not attach any photos to this service request.</p>
                        </div>
                    @endif
                </div>

                {{-- Attached Videos Section --}}
                <div class="p-6 sm:p-7 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-purple-50 border border-purple-200 text-purple-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            </span>
                            <span>Attached Videos</span>
                        </h2>
                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $serviceRequest->videos->count() }} Videos
                        </span>
                    </div>

                    @if ($serviceRequest->videos->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach ($serviceRequest->videos as $video)
                                @php
                                    $videoUrl = $video->file_url ?? asset('storage/' . $video->file_path);
                                @endphp
                                <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/80 space-y-3 shadow-2xs">
                                    <video controls class="w-full rounded-xl bg-black max-h-60 aspect-video shadow-inner">
                                        <source src="{{ $videoUrl }}" type="{{ $video->mime_type ?? 'video/mp4' }}">
                                        Your browser does not support the video tag.
                                    </video>
                                    <div class="flex items-center justify-between text-xs text-slate-600 pt-1">
                                        <span class="truncate font-semibold max-w-[200px]" title="{{ $video->file_name }}">
                                            {{ $video->file_name ?? 'Video Attachment' }}
                                        </span>
                                        <a href="{{ $videoUrl }}" target="_blank" download class="inline-flex items-center gap-1 text-[#8F6B20] font-bold hover:underline shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            <span>Download</span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 rounded-2xl bg-slate-50 border border-dashed border-slate-200 text-center">
                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            </div>
                            <p class="text-xs font-semibold text-slate-600">No videos uploaded</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">The customer did not attach any video clips to this request.</p>
                        </div>
                    @endif
                </div>

                {{-- Issued Price Quotations & History Card --}}
                <div class="p-6 sm:p-7 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-gradient-to-br from-[#FAF8F4] to-[#F5EFE6] border border-[#C5A059]/40 text-[#8F6B20] flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </span>
                            <div>
                                <h2 class="text-sm sm:text-base font-bold text-slate-900">Official Price Quotations & Estimates</h2>
                                <p class="text-xs text-slate-400">Quotes generated and dispatched to this customer</p>
                            </div>
                        </div>

                        @can('quotes.create')
                            <button
                                type="button"
                                data-request-id="{{ $serviceRequest->id }}"
                                data-request-number="{{ $reqFormatted }}"
                                data-customer-name="{{ $customerName }}"
                                data-description="{{ $serviceRequest->description }}"
                                onclick="openQuoteModalFromButton(this)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-[#8F6B20] bg-amber-50 hover:bg-[#C5A059]/20 border border-[#C5A059]/40 rounded-xl transition-all cursor-pointer shadow-2xs"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <span>+ Issue New Quote</span>
                            </button>
                        @endcan
                    </div>

                    @if ($quotesCount > 0)
                        <div class="space-y-3.5">
                            @foreach ($serviceRequest->quotes as $q)
                                <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-slate-50 via-white to-amber-50/20 border border-slate-200/90 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:border-[#C5A059]/50 transition-all shadow-2xs">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2.5">
                                            <a href="{{ route('dashboard.quotes.show', $q) }}" class="font-extrabold text-slate-900 font-mono text-sm sm:text-base hover:text-[#8F6B20] transition-colors">
                                                {{ $q->quote_number }}
                                            </a>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $q->status->badgeClasses() }}">
                                                {{ $q->status->label() }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-slate-600 line-clamp-1">
                                            {{ $q->service_description }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 flex flex-wrap items-center gap-2">
                                            <span>Issued: <strong class="text-slate-600 font-medium">{{ $q->created_at->format('M d, Y · h:i A') }}</strong></span>
                                            <span>&bull;</span>
                                            <span>Expires: <strong class="text-slate-600 font-medium">{{ $q->expires_at ? $q->expires_at->format('M d, Y') : 'N/A' }}</strong></span>
                                            @if ($q->sender)
                                                <span>&bull;</span>
                                                <span>By: <strong class="text-slate-600 font-medium">{{ $q->sender->name }}</strong></span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="sm:text-right shrink-0 flex sm:flex-col items-center sm:items-end justify-between gap-2.5 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                                        <div class="text-base sm:text-lg font-black text-[#8F6B20] font-mono">
                                            ${{ number_format($q->total_price, 2) }}
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a
                                                href="{{ route('dashboard.quotes.show', $q) }}"
                                                class="px-3 py-1.5 text-xs font-bold bg-white hover:bg-slate-100 text-slate-800 rounded-xl border border-slate-200/90 shadow-2xs transition-colors"
                                            >
                                                View Quote &rarr;
                                            </a>
                                            <a
                                                href="{{ route('dashboard.quotes.print', $q) }}"
                                                target="_blank"
                                                class="px-3 py-1.5 text-xs font-bold bg-slate-900 hover:bg-slate-800 text-white rounded-xl shadow-2xs transition-colors flex items-center gap-1"
                                                title="Print official quotation"
                                            >
                                                <svg class="w-3.5 h-3.5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                <span>Print</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 rounded-2xl bg-gradient-to-br from-[#FAF8F4] to-white border border-dashed border-[#C5A059]/40 text-center space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-[#C5A059]/15 border border-[#C5A059]/30 text-[#8F6B20] flex items-center justify-center mx-auto shadow-2xs">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">No Quotes Issued Yet</h3>
                                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                                    Review the customer's request details and attachments above, then issue an official itemized price quote directly.
                                </p>
                            </div>
                            @can('quotes.create')
                                <button
                                    type="button"
                                    data-request-id="{{ $serviceRequest->id }}"
                                    data-request-number="{{ $reqFormatted }}"
                                    data-customer-name="{{ $customerName }}"
                                    data-description="{{ $serviceRequest->description }}"
                                    onclick="openQuoteModalFromButton(this)"
                                    style="background: linear-gradient(135deg, #D4AF37 0%, #B8903B 50%, #8F6B20 100%);"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-extrabold rounded-xl text-white hover:brightness-110 shadow-md transition-all cursor-pointer"
                                >
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    <span>Create & Send Quote Now</span>
                                </button>
                            @endcan
                        </div>
                    @endif
                </div>

            </div>

            {{-- Right Sidebar Column (4 cols): Direct Quote Dispatch & Record Actions --}}
            <div class="lg:col-span-4 space-y-6">

                {{-- Direct Quote Dispatch Card (Luxury Gold Light Theme with High Contrast) --}}
                <div class="p-6 sm:p-7 rounded-3xl bg-gradient-to-br from-[#FAF8F4] via-white to-[#F5EFE6] border-2 border-[#C5A059]/40 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-mono uppercase tracking-widest text-[#8F6B20] font-extrabold bg-[#C5A059]/15 px-2.5 py-1 rounded-lg border border-[#C5A059]/30">
                            Direct Dispatch
                        </span>
                        <span class="w-2.5 h-2.5 rounded-full bg-[#C5A059] animate-pulse"></span>
                    </div>

                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Admin Quote Issuance</h3>
                        <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">
                            Calculate labor, materials, equipment, and send an official proposal directly to this customer's account.
                        </p>
                    </div>

                    @can('quotes.create')
                        <button
                            type="button"
                            data-request-id="{{ $serviceRequest->id }}"
                            data-request-number="{{ $reqFormatted }}"
                            data-customer-name="{{ $customerName }}"
                            data-description="{{ $serviceRequest->description }}"
                            onclick="openQuoteModalFromButton(this)"
                            style="background: linear-gradient(135deg, #D4AF37 0%, #B8903B 50%, #8F6B20 100%);"
                            class="w-full py-3 px-4 rounded-xl text-xs font-black text-white hover:brightness-110 shadow-md hover:shadow-lg transition-all cursor-pointer flex items-center justify-center gap-2"
                        >
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span>Issue Price Quote</span>
                        </button>
                    @endcan

                    @if ($serviceRequest->latestQuote)
                        <div class="pt-3 border-t border-[#C5A059]/20 flex items-center justify-between text-xs">
                            <span class="text-slate-500 font-medium">Latest Quote:</span>
                            <a href="{{ route('dashboard.quotes.show', $serviceRequest->latestQuote) }}" class="font-mono font-bold text-[#8F6B20] hover:underline flex items-center gap-1">
                                <span>{{ $serviceRequest->latestQuote->quote_number }}</span>
                                <span>(${{ number_format($serviceRequest->latestQuote->total_price, 2) }})</span>
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Request Record Metadata Card --}}
                <div class="p-6 rounded-3xl bg-white border border-slate-200/90 shadow-xs space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 pb-2 border-b border-slate-100 flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Request Metadata</span>
                    </h3>

                    <div class="space-y-2.5 text-xs text-slate-600">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Request ID:</span>
                            <span class="font-mono font-bold text-slate-800">{{ $reqFormatted }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Created:</span>
                            <span class="font-medium text-slate-700">{{ $serviceRequest->created_at->format('M d, Y · h:i A') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Photos Attached:</span>
                            <span class="font-bold text-slate-800">{{ $serviceRequest->photographs->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Videos Attached:</span>
                            <span class="font-bold text-slate-800">{{ $serviceRequest->videos->count() }}</span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 space-y-2">
                        <x-button href="{{ route('dashboard.service-requests.index') }}" variant="secondary" size="sm" class="w-full justify-center">
                            &larr; Back to Requests List
                        </x-button>

                        @can('service_requests.delete')
                            <form method="POST" action="{{ route('dashboard.service-requests.destroy', $serviceRequest) }}">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="button"
                                    data-confirm-delete="Are you sure you want to delete this service request record?"
                                    class="w-full py-2 px-3 text-xs font-semibold rounded-xl text-rose-600 hover:bg-rose-50 border border-rose-200 transition-colors cursor-pointer text-center"
                                >
                                    Delete Request Record
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>

            </div>

        </div>

        {{-- Lightbox Modal for Photo Preview --}}
        <div
            x-show="activeImage"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-8 bg-slate-950/85 backdrop-blur-xs"
            @click.self="activeImage = null"
            @keydown.escape.window="activeImage = null"
        >
            <div class="relative max-w-4xl max-h-[90vh] bg-transparent flex flex-col items-center">
                <button
                    type="button"
                    @click="activeImage = null"
                    class="absolute -top-10 right-0 text-white hover:text-[#C5A059] transition-colors p-2 cursor-pointer font-bold text-sm flex items-center gap-1"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    <span>Close Preview</span>
                </button>
                <img
                    :src="activeImage"
                    alt="Full Preview"
                    class="max-w-full max-h-[80vh] object-contain rounded-2xl shadow-2xl border border-white/20"
                >
            </div>
        </div>

    </div>

    {{-- Include Create Quote Modal Popup --}}
    @include('dashboard.modules.quotes.partials.create-modal')

</x-dashboard.layout>
