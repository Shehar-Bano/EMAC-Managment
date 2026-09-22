<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ContactInquiry;
use App\Models\LegalDocument;
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
            ->with(['activeSubcategories'])
            ->orderBy('sort_order')
            ->get();

        $stats = [
            'total_categories' => Category::active()->count(),
            'total_services' => Subcategory::active()->count(),
            'markets_count' => 3, // Florida, Jamaica, Cayman Islands
        ];

        return view('website.home', compact('categories', 'stats'));
    }

    /**
     * Display the About EMAC page with company history, credentials, and regional operations.
     */
    public function about(): View
    {
        $categories = Category::active()->withCount('activeSubcategories')->orderBy('sort_order')->get();

        return view('website.about', compact('categories'));
    }

    /**
     * Display the Services & Solutions page with dynamic categories and subcategories from DB.
     */
    public function services(): View
    {
        $categories = Category::active()
            ->with(['activeSubcategories'])
            ->orderBy('sort_order')
            ->get();

        return view('website.services', compact('categories'));
    }

    /**
     * Display the FAQ page.
     */
    public function faq(): View
    {
        return view('website.faq');
    }

    /**
     * Display the Contact & Consultation page with category selector from DB.
     */
    public function contact(): View
    {
        $categories = Category::active()->orderBy('sort_order')->get();

        return view('website.contact', compact('categories'));
    }

    /**
     * Handle the contact / service inquiry form submission and persist to DB.
     */
    public function submitContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'market' => ['required', 'string', 'in:Cayman Islands,Florida,Jamaica,General Inquiry'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        ContactInquiry::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'market' => $validated['market'],
            'category_id' => $validated['category_id'] ?? null,
            'message' => $validated['message'],
            'status' => 'new',
        ]);

        return redirect()
            ->route('contact')
            ->with('success', "Thank you {$validated['name']}! Your service inquiry for {$validated['market']} has been recorded. Our regional coordinator will contact you promptly.");
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
