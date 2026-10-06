<?php

namespace App\Http\Controllers\Web\Admin\Region;

use App\Actions\Region\BulkDeleteRegionsAction;
use App\Actions\Region\CreateRegionAction;
use App\Actions\Region\DeleteRegionAction;
use App\Actions\Region\ToggleRegionStatusAction;
use App\Actions\Region\UpdateRegionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Region\BulkDeleteRegionRequest;
use App\Http\Requests\Region\StoreRegionRequest;
use App\Http\Requests\Region\UpdateRegionRequest;
use App\Models\Region;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RegionController extends Controller
{
    /**
     * Display a listing of regions with filters and dynamic pagination.
     */
    public function index(Request $request): View
    {
        $this->authorize('regions.view');

        $perPageParam = strtolower($request->get('per_page', '10'));
        $query = Region::withCount(['servicePrices'])
            ->filter($request->only(['search', 'status', 'from_date', 'to_date']))
            ->orderBy('name')
            ->latest('id');

        if ($perPageParam === 'all') {
            $totalCount = (clone $query)->count();
            $regions = $query->paginate(max($totalCount, 1));
        } else {
            $perPage = in_array((int) $perPageParam, [10, 20, 50, 100], true) ? (int) $perPageParam : 10;
            $regions = $query->paginate($perPage);
        }

        return view('dashboard.modules.regions.index', compact('regions'));
    }

    /**
     * Show the form for creating a new region.
     */
    public function create(): View
    {
        $this->authorize('regions.create');

        return view('dashboard.modules.regions.create');
    }

    /**
     * Store a newly created region in storage.
     */
    public function store(StoreRegionRequest $request, CreateRegionAction $action): RedirectResponse
    {
        $region = $action->execute($request->validated());

        return redirect()
            ->route('dashboard.regions.index')
            ->with('success', "Region '{$region->name}' created successfully.");
    }

    /**
     * Display the specified region.
     */
    public function show(Region $region): View
    {
        $this->authorize('regions.view');

        $region->load([
            'servicePrices' => function ($query) {
                $query->with(['category', 'subcategory'])->latest('id');
            },
        ]);

        return view('dashboard.modules.regions.show', compact('region'));
    }

    /**
     * Show the form for editing the specified region.
     */
    public function edit(Region $region): View
    {
        $this->authorize('regions.edit');

        return view('dashboard.modules.regions.edit', compact('region'));
    }

    /**
     * Update the specified region in storage.
     */
    public function update(UpdateRegionRequest $request, Region $region, UpdateRegionAction $action): RedirectResponse
    {
        $action->execute($region, $request->validated());

        return redirect()
            ->route('dashboard.regions.index')
            ->with('success', "Region '{$region->name}' updated successfully.");
    }

    /**
     * Remove the specified region from storage.
     */
    public function destroy(Region $region, DeleteRegionAction $action): RedirectResponse
    {
        $this->authorize('regions.delete');

        try {
            $regionName = $region->name;
            $action->execute($region);

            return redirect()
                ->route('dashboard.regions.index')
                ->with('success', "Region '{$regionName}' has been archived/deleted.");
        } catch (Exception $e) {
            return redirect()
                ->route('dashboard.regions.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk delete selected regions.
     */
    public function bulkDelete(BulkDeleteRegionRequest $request, BulkDeleteRegionsAction $action): JsonResponse|RedirectResponse
    {
        $deleted = $action->execute($request->validated('selected_ids'));
        $message = "Deleted {$deleted} selected region(s).";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted' => $deleted,
            ]);
        }

        return redirect()->route('dashboard.regions.index')->with('success', $message);
    }

    /**
     * Toggle the active/inactive status of a region.
     */
    public function toggleStatus(Region $region, ToggleRegionStatusAction $action): RedirectResponse
    {
        $this->authorize('regions.status');

        try {
            $updatedRegion = $action->execute($region);

            return back()->with('success', "Status for '{$updatedRegion->name}' changed to ".ucfirst($updatedRegion->status).'.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Export regions matching active filter criteria to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $this->authorize('regions.view');

        $regions = Region::withCount(['servicePrices'])
            ->filter($request->only(['search', 'status', 'from_date', 'to_date']))
            ->orderBy('name')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="regions_export_'.date('Y_m_d_His').'.csv"',
        ];

        return response()->stream(function () use ($regions) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Slug', 'Code', 'Currency', 'Pricing Entries Count', 'Status', 'Created At']);

            foreach ($regions as $region) {
                fputcsv($handle, [
                    $region->id,
                    $region->name,
                    $region->slug,
                    $region->code ?? '—',
                    $region->currency,
                    $region->service_prices_count,
                    ucfirst($region->status),
                    $region->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
