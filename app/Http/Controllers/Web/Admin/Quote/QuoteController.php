<?php

namespace App\Http\Controllers\Web\Admin\Quote;

use App\Actions\Quote\CreateQuoteAction;
use App\Enums\QuoteStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Quote\StoreQuoteRequest;
use App\Models\Quote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuoteController extends Controller
{
    /**
     * Display a listing of quotes.
     */
    public function index(Request $request): View
    {
        $this->authorize('quotes.view');

        $filters = $request->only(['search', 'status']);
        $statusCounts = [
            'all' => Quote::count(),
            'pending' => Quote::where('status', QuoteStatus::PENDING)->count(),
            'approved' => Quote::where('status', QuoteStatus::APPROVED)->count(),
            'declined' => Quote::where('status', QuoteStatus::DECLINED)->count(),
            'ask_for_question' => Quote::where('status', QuoteStatus::ASK_FOR_QUESTION)->count(),
        ];

        $quotes = Quote::with(['user', 'serviceRequest.address', 'sender'])
            ->filter($filters)
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.modules.quotes.index', compact('quotes', 'filters', 'statusCounts'));
    }

    /**
     * Store a newly created quote for a service request.
     */
    public function store(StoreQuoteRequest $request, CreateQuoteAction $action): RedirectResponse
    {
        $this->authorize('quotes.create');

        $quote = $action->execute($request->user(), $request->validated());

        return redirect()
            ->route('dashboard.quotes.show', $quote)
            ->with('success', "Quote {$quote->quote_number} generated and sent successfully.");
    }

    /**
     * Display details of a specific quote.
     */
    public function show(Quote $quote): View
    {
        $this->authorize('quotes.view');

        $quote->load(['user.addresses', 'serviceRequest.address', 'serviceRequest.photographs', 'serviceRequest.videos', 'sender']);

        return view('dashboard.modules.quotes.show', compact('quote'));
    }

    /**
     * Update status of the quote (Approved / Declined / Ask for question).
     */
    public function updateStatus(Request $request, Quote $quote): RedirectResponse
    {
        $this->authorize('quotes.status');

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,approved,declined,ask_for_question'],
            'customer_notes' => ['nullable', 'string', 'max:2000'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $updateData = [
            'status' => $validated['status'],
        ];

        if (isset($validated['customer_notes'])) {
            $updateData['customer_notes'] = $validated['customer_notes'];
        }

        if (isset($validated['admin_notes'])) {
            $updateData['admin_notes'] = $validated['admin_notes'];
        }

        if ($validated['status'] === QuoteStatus::APPROVED->value) {
            $updateData['approved_at'] = now();
            $updateData['declined_at'] = null;
        } elseif ($validated['status'] === QuoteStatus::DECLINED->value) {
            $updateData['declined_at'] = now();
            $updateData['approved_at'] = null;
        }

        $quote->update($updateData);

        return back()->with('success', "Quote status updated to '{$quote->status->label()}'.");
    }

    /**
     * Delete a quote.
     */
    public function destroy(Quote $quote): RedirectResponse
    {
        $this->authorize('quotes.delete');

        $quoteNumber = $quote->quote_number;
        $quote->delete();

        return redirect()
            ->route('dashboard.service-requests.show', $quote->service_request_id)
            ->with('success', "Quote {$quoteNumber} deleted successfully.");
    }

    /**
     * Printable view of the quote.
     */
    public function print(Quote $quote): View
    {
        $this->authorize('quotes.view');

        $quote->load(['user.addresses', 'serviceRequest.address', 'sender']);

        return view('dashboard.modules.quotes.print', compact('quote'));
    }
}
