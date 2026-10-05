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
     * Display a listing of quotes for the authenticated user.
     * Optionally filter by service_request_id or status.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = (int) $request->get('per_page', 10);

        $query = Quote::with(['serviceRequest.category', 'serviceRequest.subcategory', 'serviceRequest.address', 'sender'])
            ->where(function ($q) use ($user) {
                if (! $user->isSuperAdmin()) {
                    $q->where('user_id', $user->id);
                }
            })
            ->when($request->filled('service_request_id'), function ($q) use ($request) {
                $q->where('service_request_id', $request->service_request_id);
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->latest('id');

        if ($request->get('all') || $request->get('per_page') === 'all') {
            $quotes = $query->get();

            return ApiResponse::success(
                data: QuoteResource::collection($quotes)->resolve(),
                message: 'Quotes retrieved successfully.'
            );
        }

        $quotes = $query->paginate($perPage);

        return ApiResponse::paginated(
            data: QuoteResource::collection($quotes),
            message: 'Quotes retrieved successfully.'
        );
    }

    /**
     * Display details of a specific quote by quote ID.
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
     * Get the quote associated with a specific service request ID.
     */
    public function getByServiceRequest(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $quote = Quote::with(['serviceRequest.category', 'serviceRequest.subcategory', 'serviceRequest.address', 'sender'])
            ->where('service_request_id', $id)
            ->where(function ($q) use ($user) {
                if (! $user->isSuperAdmin()) {
                    $q->where('user_id', $user->id);
                }
            })
            ->latest('id')
            ->first();

        if (! $quote) {
            return ApiResponse::notFound('No quote found for this service request.');
        }

        return ApiResponse::success(
            data: (new QuoteResource($quote))->resolve(),
            message: 'Quote retrieved successfully.'
        );
    }

    /**
     * Customer responds to a quote using the quote ID (approve, decline, or ask_question).
     */
    public function respond(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $quote = Quote::where(function ($q) use ($user) {
            if (! $user->isSuperAdmin()) {
                $q->where('user_id', $user->id);
            }
        })->findOrFail($id);

        return $this->processResponse($quote, $request);
    }

    /**
     * Customer responds to a quote using the service request ID (approve, decline, or ask_question).
     */
    public function respondByServiceRequest(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $quote = Quote::where('service_request_id', $id)
            ->where(function ($q) use ($user) {
                if (! $user->isSuperAdmin()) {
                    $q->where('user_id', $user->id);
                }
            })
            ->latest('id')
            ->first();

        if (! $quote) {
            return ApiResponse::notFound('No quote found for this service request.');
        }

        return $this->processResponse($quote, $request);
    }

    /**
     * Process quote response action.
     */
    protected function processResponse(Quote $quote, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'string', 'in:approve,decline,ask_question,review_requested'],
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
        } elseif ($action === 'review_requested') {
            $quote->update([
                'status' => QuoteStatus::REVIEW_REQUESTED,
                'customer_notes' => $notes,
            ]);
            $message = 'Review request sent to service team.';
        } else {
            $quote->update([
                'status' => QuoteStatus::ASK_FOR_QUESTION,
                'customer_notes' => $notes,
            ]);
            $message = 'Your question has been sent to our service team.';
        }

        if (! empty($notes)) {
            $quote->messages()->create([
                'user_id' => $request->user()->id,
                'sender_type' => 'customer',
                'message' => $notes,
            ]);
        }

        return ApiResponse::success(
            data: (new QuoteResource($quote->fresh(['sender', 'messages.user'])))->resolve(),
            message: $message
        );
    }
}
