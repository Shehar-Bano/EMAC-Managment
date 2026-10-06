<?php

namespace App\Http\Controllers\Web\Admin\Legal;

use App\Http\Controllers\Controller;
use App\Models\LegalDocument;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PrivacyController extends Controller
{
    /**
     * Display the Privacy Policy management screen.
     */
    public function index(): View
    {
        $this->authorize('privacy.view');

        $document = LegalDocument::privacy()->latest()->first();

        return view('dashboard.modules.legal.privacy.index', compact('document'));
    }

    /**
     * Show form to edit Privacy Policy.
     */
    public function edit(): View
    {
        $this->authorize('privacy.edit');

        $document = LegalDocument::privacy()->latest()->firstOrCreate(
            ['type' => 'privacy'],
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'version' => '1.0',
                'effective_date' => now()->toDateString(),
                'status' => 'active',
                'content' => '<h3>1. Information We Collect</h3><p>Enter privacy policy content here...</p>',
            ]
        );

        return view('dashboard.modules.legal.privacy.edit', compact('document'));
    }

    /**
     * Update the Privacy Policy document.
     */
    public function update(Request $request): RedirectResponse
    {
        $this->authorize('privacy.edit');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'version' => ['required', 'string', 'max:50'],
            'effective_date' => ['nullable', 'date'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'content' => ['required', 'string'],
        ]);

        $document = LegalDocument::privacy()->latest()->firstOrCreate(
            ['type' => 'privacy'],
            [
                'slug' => 'privacy-policy',
            ]
        );

        $document->update([
            'title' => $validated['title'],
            'version' => $validated['version'],
            'effective_date' => $validated['effective_date'] ?? now()->toDateString(),
            'status' => $validated['status'],
            'content' => $validated['content'],
            'updated_by' => auth()->id(),
        ]);

        return redirect()
            ->route('dashboard.privacy.index')
            ->with('success', 'Privacy Policy updated successfully.');
    }
}
