<?php

namespace App\Http\Controllers\Web\Admin\Inquiry;

use App\Http\Controllers\Controller;
use App\Models\ContactInquiry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InquiryController extends Controller
{
    /**
     * Display a listing of client inquiries and leads from the website.
     */
    public function index(Request $request): View
    {
        $this->authorize('inquiries.view');

        $perPageParam = strtolower($request->get('per_page', '10'));
        $query = ContactInquiry::with(['category'])
            ->filter($request->only(['search', 'status', 'market']))
            ->latest('id');

        if ($perPageParam === 'all') {
            $totalCount = (clone $query)->count();
            $inquiries = $query->paginate(max($totalCount, 1));
        } else {
            $perPage = in_array((int) $perPageParam, [10, 20, 50, 100], true) ? (int) $perPageParam : 10;
            $inquiries = $query->paginate($perPage);
        }

        $stats = [
            'total' => ContactInquiry::count(),
            'new' => ContactInquiry::where('status', 'new')->count(),
            'contacted' => ContactInquiry::where('status', 'contacted')->count(),
            'in_progress' => ContactInquiry::where('status', 'in_progress')->count(),
            'closed' => ContactInquiry::where('status', 'closed')->count(),
        ];

        return view('dashboard.modules.inquiries.index', compact('inquiries', 'stats'));
    }

    /**
     * Display the specified inquiry in detail.
     */
    public function show(ContactInquiry $inquiry): View
    {
        $this->authorize('inquiries.view');

        $inquiry->load(['category']);

        return view('dashboard.modules.inquiries.show', compact('inquiry'));
    }

    /**
     * Update the status of an inquiry.
     */
    public function updateStatus(Request $request, ContactInquiry $inquiry): RedirectResponse
    {
        $this->authorize('inquiries.status');

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:new,contacted,in_progress,closed'],
        ]);

        $inquiry->update(['status' => $validated['status']]);

        return back()->with('success', "Inquiry status updated to '".ucfirst(str_replace('_', ' ', $validated['status']))."'.");
    }

    /**
     * Remove the specified inquiry from storage.
     */
    public function destroy(ContactInquiry $inquiry): RedirectResponse
    {
        $this->authorize('inquiries.delete');

        $inquiry->delete();

        return redirect()
            ->route('dashboard.inquiries.index')
            ->with('success', "Inquiry from '{$inquiry->name}' deleted successfully.");
    }

    /**
     * Bulk delete selected inquiries.
     */
    public function bulkDelete(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('inquiries.bulk-delete');

        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:contact_inquiries,id'],
        ]);

        $count = ContactInquiry::whereIn('id', $validated['ids'])->delete();

        $message = "Deleted {$count} inquiry(ies) successfully.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->route('dashboard.inquiries.index')->with('success', $message);
    }

    /**
     * Export inquiries matching filter criteria to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $this->authorize('inquiries.view');

        $inquiries = ContactInquiry::with(['category'])
            ->filter($request->only(['search', 'status', 'market']))
            ->latest('id')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inquiries_export_'.date('Y_m_d_His').'.csv"',
        ];

        return response()->stream(function () use ($inquiries) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Email', 'Phone', 'Market', 'Division', 'Status', 'Message', 'Submitted At']);

            foreach ($inquiries as $inquiry) {
                fputcsv($handle, [
                    $inquiry->id,
                    $inquiry->name,
                    $inquiry->email,
                    $inquiry->phone ?? 'N/A',
                    $inquiry->market,
                    $inquiry->category?->name ?? 'General',
                    ucfirst(str_replace('_', ' ', $inquiry->status)),
                    $inquiry->message,
                    $inquiry->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
