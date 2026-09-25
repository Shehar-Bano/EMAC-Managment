<?php

namespace App\Http\Controllers\Web\Admin\ServiceRequest;

use App\Enums\ServiceRequestPriority;
use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ServiceRequestController extends Controller
{
    /**
     * Display a listing of customer service requests in ERP dashboard.
     */
    public function index(Request $request): View
    {
        $this->authorize('service_requests.view');

        $perPageParam = strtolower($request->get('per_page', '10'));
        $query = ServiceRequest::with(['user', 'address', 'photographs', 'videos', 'latestQuote'])
            ->filter($request->only(['search', 'quote_status', 'priority', 'date_from', 'date_to']))
            ->latest('id');

        if ($perPageParam === 'all') {
            $totalCount = (clone $query)->count();
            $requests = $query->paginate(max($totalCount, 1));
        } else {
            $perPage = in_array((int) $perPageParam, [10, 20, 50, 100], true) ? (int) $perPageParam : 10;
            $requests = $query->paginate($perPage);
        }

        $stats = [
            'total' => ServiceRequest::count(),
            'awaiting_quote' => ServiceRequest::doesntHave('quotes')->count(),
            'quotes_sent' => ServiceRequest::has('quotes')->count(),
            'emergency' => ServiceRequest::where('priority', ServiceRequestPriority::EMERGENCY)->count(),
            'high' => ServiceRequest::where('priority', ServiceRequestPriority::HIGH)->count(),
        ];

        return view('dashboard.modules.service-requests.index', compact('requests', 'stats'));
    }

    /**
     * Display the specified customer service request in detail.
     */
    public function show(ServiceRequest $serviceRequest): View
    {
        $this->authorize('service_requests.view');

        $serviceRequest->load(['user.addresses', 'address', 'photographs', 'videos', 'quotes.sender']);

        return view('dashboard.modules.service-requests.show', compact('serviceRequest'));
    }

    /**
     * Update the workflow status of a service request.
     */
    public function updateStatus(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $this->authorize('service_requests.status');

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,in_review,approved,in_progress,completed,cancelled'],
        ]);

        $serviceRequest->update(['status' => $validated['status']]);

        return back()->with('success', "Service request #{$serviceRequest->id} status updated to '".ucfirst(str_replace('_', ' ', $validated['status']))."'.");
    }

    /**
     * Remove the specified service request from storage.
     */
    public function destroy(ServiceRequest $serviceRequest): RedirectResponse
    {
        $this->authorize('service_requests.delete');

        $serviceRequest->delete();

        return redirect()
            ->route('dashboard.service-requests.index')
            ->with('success', "Service request #{$serviceRequest->id} deleted successfully.");
    }

    /**
     * Bulk delete selected service requests.
     */
    public function bulkDelete(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('service_requests.bulk-delete');

        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:service_requests,id'],
        ]);

        $count = ServiceRequest::whereIn('id', $validated['ids'])->delete();

        $message = "Deleted {$count} service request(s) successfully.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->route('dashboard.service-requests.index')->with('success', $message);
    }

    /**
     * Export service requests matching filter criteria to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $this->authorize('service_requests.export');

        $requests = ServiceRequest::with(['user', 'address'])
            ->filter($request->only(['search', 'status', 'priority', 'date_from', 'date_to']))
            ->latest('id')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="service_requests_export_'.date('Y_m_d_His').'.csv"',
        ];

        return response()->stream(function () use ($requests) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'ID',
                'Customer Name',
                'Customer Email',
                'Customer Phone',
                'Location / Address',
                'City / Country',
                'Preferred Date',
                'Preferred Time',
                'Priority',
                'Status',
                'Description',
                'Property Information',
                'Additional Notes',
                'Submitted At',
            ]);

            foreach ($requests as $item) {
                fputcsv($handle, [
                    $item->id,
                    $item->user?->name ?? 'N/A',
                    $item->user?->email ?? 'N/A',
                    $item->user?->phone ?? 'N/A',
                    $item->address?->address ?? 'N/A',
                    ($item->address?->city ?? '').' '.($item->address?->country ?? ''),
                    $item->preferred_service_date?->format('Y-m-d') ?? 'N/A',
                    $item->preferred_service_time ?? 'N/A',
                    strtoupper($item->priority instanceof \BackedEnum ? $item->priority->value : $item->priority),
                    ucfirst(str_replace('_', ' ', $item->status instanceof \BackedEnum ? $item->status->value : $item->status)),
                    $item->description,
                    $item->property_information,
                    $item->additional_notes ?? 'N/A',
                    $item->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
