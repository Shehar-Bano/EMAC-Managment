<?php

namespace App\Http\Controllers\Web;

use App\Enums\AccountStatus;
use App\Enums\AuthSource;
use App\Enums\ProfileStatus;
use App\Enums\ServiceRequestPriority;
use App\Enums\ServiceRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\LegalDocument;
use App\Models\Region;
use App\Models\Role;
use App\Models\ServiceRequest;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

        $user = Auth::user();
        $userAddress = null;
        $defaultRegionId = null;

        if ($user) {
            $userAddress = $user->addresses()->where('is_primary', true)->first() ?: $user->addresses()->first();
            $defaultRegionId = $userAddress?->region_id;
        }

        if (! $defaultRegionId) {
            $defaultRegionId = $request->filled('region_id') ? (int) $request->get('region_id') : ($regions->first()?->id ?? 1);
        }

        return view('website.contact', compact('categories', 'regions', 'user', 'userAddress', 'defaultRegionId'));
    }

    /**
     * Handle the website service / quote request and store directly into service_requests table with type = 'web'.
     */
    public function submitContact(Request $request): RedirectResponse|JsonResponse
    {
        $user = Auth::user();

        $videoValidator = function ($attribute, $value, $fail) {
            if (! $value instanceof UploadedFile) {
                return;
            }
            $allowedExts = ['mp4', 'mov', 'webm', 'avi', 'mkv', '3gp', 'wmv', 'flv', 'mpeg', 'mpg', 'm4v', 'ogg', 'qt', 'ts', 'm4p'];
            $allowedMimes = [
                'video/mp4', 'video/quicktime', 'video/webm', 'video/x-msvideo', 'video/3gpp',
                'video/avi', 'video/mpeg', 'video/x-matroska', 'video/ogg', 'video/x-m4v',
                'video/x-flv', 'video/x-ms-wmv', 'application/octet-stream',
            ];
            $ext = strtolower($value->getClientOriginalExtension());
            $mime = strtolower($value->getMimeType() ?: '');
            if (! in_array($ext, $allowedExts, true) && ! in_array($mime, $allowedMimes, true)) {
                $fail('The video must be a valid video file (MP4, MOV, WEBM, AVI, MKV, max 200MB).');
            }
        };

        $rules = [
            'region_id' => ['required', 'integer', 'exists:regions,id'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'subcategory_id' => ['required', 'integer', 'exists:subcategories,id'],
            'property_information' => ['nullable', 'string', 'max:500'],
            'preferred_service_date' => ['nullable', 'date'],
            'preferred_service_time' => ['nullable', 'string', 'max:100'],
            'is_emergency' => ['nullable', 'boolean'],
            'priority' => ['nullable', 'string', 'in:normal,emergency'],
            'message' => ['required', 'string', 'min:5', 'max:5000'],
            'photographs' => ['nullable', 'array', 'max:10'],
            'photographs.*' => ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/webp,image/jpg,image/heic,image/heif,image/*', 'max:10240'],
            'video' => ['nullable', 'file', 'max:204800', $videoValidator],
            'videos' => ['nullable', 'array', 'max:3'],
            'videos.*' => ['nullable', 'file', 'max:204800', $videoValidator],
        ];

        if (! $user) {
            $rules['name'] = ['required', 'string', 'max:150'];
            $rules['email'] = ['required', 'email', 'max:255'];
            $rules['phone'] = ['nullable', 'string', 'max:50'];
        }

        $messages = [
            'video.uploaded' => 'The video failed to upload. The file might exceed the server upload limit or the upload was interrupted.',
            'videos.*.uploaded' => 'One of the video files failed to upload.',
            'photographs.*.uploaded' => 'One of the photos failed to upload.',
        ];

        $validated = $request->validate($rules, $messages);

        $serviceRequest = DB::transaction(function () use ($validated, $request, $user) {
            // 1. Resolve User
            if (! $user) {
                $email = strtolower(trim($validated['email']));
                $user = User::withTrashed()->where('email', $email)->first();

                if (! $user) {
                    $user = User::create([
                        'name' => $validated['name'],
                        'email' => $email,
                        'phone' => $validated['phone'] ?? '+1 (000) 000-0000',
                        'password' => Hash::make(Str::random(16)),
                        'role' => 'customer',
                        'source' => AuthSource::EMAIL,
                        'account_status' => AccountStatus::PENDING,
                        'profile_status' => ProfileStatus::INCOMPLETE,
                        'status' => 'active',
                    ]);

                    $customerRole = Role::where('slug', 'customer')->first();
                    if ($customerRole) {
                        $user->roles()->syncWithoutDetaching([$customerRole->id]);
                    }
                }
            }

            // 2. Resolve Region & User Address
            $region = Region::find($validated['region_id']);
            $userAddress = $user->addresses()->where('is_primary', true)->first() ?: $user->addresses()->first();

            $addressText = ! empty($validated['property_information'])
                ? trim($validated['property_information'])
                : ($region ? "Property in {$region->name}" : 'Customer Service Address');

            if (! $userAddress) {
                $userAddress = $user->addresses()->create([
                    'region_id' => $region?->id,
                    'country' => $region?->country ?? 'USA',
                    'state' => $region?->name ?? 'Service Territory',
                    'city' => $region?->name ?? 'Local City',
                    'zipcode' => '00000',
                    'address' => $addressText,
                    'is_primary' => true,
                ]);
            } else {
                if (! empty($validated['property_information'])) {
                    $userAddress->update([
                        'address' => $validated['property_information'],
                        'region_id' => $region?->id ?? $userAddress->region_id,
                    ]);
                }
            }

            // 3. Resolve Category / Subcategory
            $categoryId = (int) $validated['category_id'];
            $subcategoryId = (int) $validated['subcategory_id'];

            $marketName = $region?->name ?? 'Service Territory';
            $propertyInfo = ! empty($validated['property_information'])
                ? $validated['property_information']
                : ($userAddress?->address ?? "Service Location: {$marketName}");

            // 4. Create Service Request Record directly in service_requests table
            $sr = ServiceRequest::create([
                'user_id' => $user->id,
                'user_address_id' => $userAddress?->id,
                'category_id' => $categoryId,
                'subcategory_id' => $subcategoryId,
                'description' => $validated['message'],
                'property_information' => $propertyInfo,
                'preferred_service_date' => $validated['preferred_service_date'] ?? now()->addDays(2)->format('Y-m-d'),
                'preferred_service_time' => $validated['preferred_service_time'] ?? 'Flexible',
                'priority' => ServiceRequestPriority::fromInput($request->input('priority'), $request->input('is_emergency'))->value,
                'additional_notes' => 'Submitted via Website Online Quote Request form',
                'status' => ServiceRequestStatus::PENDING,
                'type' => 'web',
            ]);

            // 5. Store Multiple Attached Photographs
            if ($request->hasFile('photographs')) {
                foreach ($request->file('photographs') as $photo) {
                    if ($photo->isValid()) {
                        $path = $photo->store('service_requests/photos', 'public');
                        $sr->photographs()->create([
                            'file_path' => $path,
                            'file_name' => $photo->getClientOriginalName(),
                            'file_size' => $photo->getSize(),
                            'mime_type' => $photo->getClientMimeType(),
                        ]);
                    }
                }
            }

            // 6. Store Video
            $videos = [];
            if ($request->hasFile('video')) {
                $videos[] = $request->file('video');
            }
            if ($request->hasFile('videos')) {
                $uploadedVideos = $request->file('videos');
                if (is_array($uploadedVideos)) {
                    $videos = array_merge($videos, $uploadedVideos);
                } else {
                    $videos[] = $uploadedVideos;
                }
            }

            foreach ($videos as $video) {
                if ($video instanceof UploadedFile && $video->isValid()) {
                    $path = $video->store('service_requests/videos', 'public');
                    $sr->videos()->create([
                        'file_path' => $path,
                        'file_name' => $video->getClientOriginalName(),
                        'file_size' => $video->getSize(),
                        'mime_type' => $video->getClientMimeType(),
                    ]);
                }
            }

            return $sr;
        });

        $reqNumber = '#REQ-'.str_pad($serviceRequest->id, 5, '0', STR_PAD_LEFT);
        $displayName = $user?->name ?? ($validated['name'] ?? 'Valued Customer');
        $successMsg = "Thank you {$displayName}! Your service request ({$reqNumber}) has been submitted successfully. Our team will review your project details and prepare an estimate promptly.";

        if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'request_number' => $reqNumber,
                'redirect_url' => route('contact'),
            ]);
        }

        return redirect()
            ->route('contact')
            ->with('success', $successMsg);
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
