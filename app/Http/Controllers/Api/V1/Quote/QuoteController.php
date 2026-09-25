<?php

namespace App\Http\Controllers\Api\V1\Quote;

use App\Enums\QuoteStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Quote\QuoteResource;
use App\Models\Quote;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    /**
     * Display details of a specific quote.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $quote = Quote::with(['serviceRequest.address', 'sender'])
            ->where(function ($query) use ($user) {
                if (! $user->isSuperAdmin()) {
                    $query->where('user_id', $user->id);
                }
            })
            ->findOrFail($id);

        return ApiResponse::success(
            data: (new QuoteResource($quote))->resolve(),
            message: 'Quote retrieved successfully.'
        );
    }

    /**
     * Customer responds to a quote (approve, decline, or ask a question).
     */
    public function respond(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $quote = Quote::where('user_id', $user->id)->findOrFail($id);

        $validated = $request->validate([
            'action' => ['required', 'string', 'in:approve,decline,ask_question'],
            'customer_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $action = $validated['action'];
        $notes = $validated['customer_notes'] ?? null;

        if ($action === 'approve') {
            $quote->update([
                'status' => QuoteStatus::APPROVED,
                'customer_notes' => $notes,
                'approved_at' => now(),
                'declined_at' => null,
            ]);
            $message = 'Quote approved successfully.';
        } elseif ($action === 'decline') {
            $quote->update([
                'status' => QuoteStatus::DECLINED,
                'customer_notes' => $notes,
                'declined_at' => now(),
                'approved_at' => null,
            ]);
            $message = 'Quote declined.';
        } else { // ask_question
            $quote->update([
                'status' => QuoteStatus::ASK_FOR_QUESTION,
                'customer_notes' => $notes,
            ]);
            $message = 'Your question has been sent to our service team.';
        }

        return ApiResponse::success(
            data: (new QuoteResource($quote->fresh(['sender'])))->resolve(),
            message: $message
        );
    }
}
