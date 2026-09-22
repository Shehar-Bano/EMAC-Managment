<?php

namespace App\Http\Controllers\Web\Admin\Legal;

use App\Http\Controllers\Controller;
use App\Models\LegalDocument;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TermsController extends Controller
{
    /**
     * Display the Terms & Conditions management screen.
     */
    public function index(): View
    {
        $this->authorize('terms.view');

        $document = LegalDocument::terms()->latest()->first();

        return view('dashboard.modules.legal.terms.index', compact('document'));
    }

    /**
     * Show form to edit Terms & Conditions.
     */
    public function edit(): View
    {
        $this->authorize('terms.edit');

        $document = LegalDocument::terms()->latest()->firstOrCreate(
            ['type' => 'terms'],
            [
                'slug' => 'terms-and-conditions',
                'title' => 'Terms and Conditions',
                'version' => '1.0',
                'effective_date' => now()->toDateString(),
                'status' => 'active',
                'content' => '<h3>1. Agreement to Terms</h3><p>Enter terms and conditions content here...</p>',
            ]
        );

        return view('dashboard.modules.legal.terms.edit', compact('document'));
    }

    /**
     * Update the Terms & Conditions document.
     */
    public function update(Request $request): RedirectResponse
    {
        $this->authorize('terms.edit');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'version' => ['required', 'string', 'max:50'],
            'effective_date' => ['nullable', 'date'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'content' => ['required', 'string'],
        ]);

        $document = LegalDocument::terms()->latest()->firstOrCreate(
            ['type' => 'terms'],
            [
                'slug' => 'terms-and-conditions',
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
            ->route('dashboard.terms.index')
            ->with('success', 'Terms and Conditions updated successfully.');
    }
}
