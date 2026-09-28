<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ContactInquiry;
use App\Models\LegalDocument;
use App\Models\Region;
use App\Models\RegionalServicePrice;
use App\Models\Subcategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    /**
     * Display the public homepage with dynamic services, markets, and statistics from DB.
     */
    public function home(): View
    {
        $categories = Category::active()
            ->whereNull('deleted_at')
            ->with(['activeSubcategories'])
            ->orderBy('sort_order')
            ->get();

        $regions = Region::active()
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        $stats = [
            'total_categories' => Category::active()->count(),
            'total_services' => Subcategory::active()->count(),
            'markets_count' => $regions->count() ?: 3,
        ];

        return view('website.home', compact('categories', 'regions', 'stats'));
    }

    /**
     * Display the About EMAC page with company history, credentials, and regional operations.
     */
    public function about(): View
    {
        $categories = Category::active()
            ->whereNull('deleted_at')
            ->withCount('activeSubcategories')
            ->orderBy('sort_order')
            ->get();

        $regions = Region::active()
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        return view('website.about', compact('categories', 'regions'));
    }

    /**
     * Display the Services & Solutions page with dynamic categories, subcategories, and regional pricing from DB.
     */
    public function services(Request $request): View
    {
        $regions = Region::active()
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        $selectedRegionId = $request->filled('region_id') ? (int) $request->get('region_id') : $regions->first()?->id;
        $selectedRegion = $regions->firstWhere('id', $selectedRegionId) ?? $regions->first();

        $categories = Category::active()
            ->whereNull('deleted_at')
            ->with([
                'activeSubcategories' => function ($subQuery) use ($selectedRegion) {
                    $subQuery->whereNull('deleted_at')
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->with([
                            'regionalServicePrices' => function ($priceQuery) use ($selectedRegion) {
                                $priceQuery->whereNull('deleted_at')
                                    ->where('status', 'active')
                                    ->when($selectedRegion, fn ($pq) => $pq->where('region_id', $selectedRegion->id))
                                    ->whereHas('region', fn ($r) => $r->whereNull('deleted_at')->where('status', 'active'))
                                    ->with(['region' => fn ($r) => $r->whereNull('deleted_at')]);
                            },
                        ]);
                },
            ])
            ->orderBy('sort_order')
            ->get();

        return view('website.services', compact('categories', 'regions', 'selectedRegion'));
    }

    /**
     * Display the FAQ page.
     */
    public function faq(): View
    {
        return view('website.faq');
    }

    /**
     * Display the Contact & Consultation page with dynamic regions, categories, and subcategories from DB.
     */
    public function contact(Request $request): View
    {
        $regions = Region::active()
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get();

        $categories = Category::active()
            ->whereNull('deleted_at')
            ->with([
                'activeSubcategories' => function ($subQuery) {
                    $subQuery->whereNull('deleted_at')
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->with([
                            'regionalServicePrices' => function ($priceQuery) {
                                $priceQuery->whereNull('deleted_at')
                                    ->where('status', 'active')
                                    ->whereHas('region', fn ($r) => $r->whereNull('deleted_at')->where('status', 'active'))
                                    ->with(['region' => fn ($r) => $r->whereNull('deleted_at')]);
                            },
                        ]);
                },
            ])
            ->orderBy('sort_order')
            ->get();

        return view('website.contact', compact('categories', 'regions'));
    }

    /**
     * Handle the contact / service inquiry form submission with multiple images and video.
     */
    public function submitContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'market' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'integer', 'exists:subcategories,id'],
            'message' => ['required', 'string', 'min:5', 'max:5000'],
            'photographs' => ['nullable', 'array', 'max:10'],
            'photographs.*' => ['file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'video' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/webm,video/x-msvideo,video/3gpp', 'max:51200'],
        ]);

        // Resolve region and market name
        $region = null;
        if (! empty($validated['region_id'])) {
            $region = Region::find($validated['region_id']);
        }

        $marketName = $region?->name ?? $validated['market'] ?? 'General Inquiry';
        $currency = $region?->currency ?? 'USD';
        $estimatedPrice = null;

        // Auto calculate / attach estimated price if subcategory and region exist
        if ($region && ! empty($validated['subcategory_id'])) {
            $priceRecord = RegionalServicePrice::where('region_id', $region->id)
                ->where('subcategory_id', $validated['subcategory_id'])
                ->where('status', 'active')
                ->whereNull('deleted_at')
                ->first();

            if ($priceRecord) {
                $estimatedPrice = (float) $priceRecord->price;
                $currency = $priceRecord->currency;
            }
        }

        // Process multiple photographs
        $storedPhotos = [];
        if ($request->hasFile('photographs')) {
            foreach ($request->file('photographs') as $photo) {
                if ($photo->isValid()) {
                    $storedPhotos[] = $photo->store('inquiries/photographs', 'public');
                }
            }
        }

        // Process video
        $videoPath = null;
        if ($request->hasFile('video') && $request->file('video')->isValid()) {
            $videoPath = $request->file('video')->store('inquiries/videos', 'public');
        }

        ContactInquiry::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'region_id' => $region?->id,
            'market' => $marketName,
            'category_id' => $validated['category_id'] ?? null,
            'subcategory_id' => $validated['subcategory_id'] ?? null,
            'estimated_price' => $estimatedPrice,
            'currency' => $currency,
            'message' => $validated['message'],
            'photographs' => ! empty($storedPhotos) ? $storedPhotos : null,
            'video' => $videoPath,
            'status' => 'new',
        ]);

        return redirect()
            ->route('contact')
            ->with('success', "Thank you {$validated['name']}! Your quote request for {$marketName} has been submitted successfully. Our regional coordinator will review your request and media files and get back to you promptly.");
    }

    /**
     * Display the Privacy Policy page.
     */
    public function privacy(): View
    {
        $document = LegalDocument::privacy()->active()->latest()->first();

        return view('website.privacy', compact('document'));
    }

    /**
     * Display the Terms of Service page.
     */
    public function terms(): View
    {
        $document = LegalDocument::terms()->active()->latest()->first();

        return view('website.terms', compact('document'));
    }
}
