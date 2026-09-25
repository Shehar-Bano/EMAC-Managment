<?php

namespace App\Http\Controllers\Web\Admin\RegionalServicePrice;

use App\Actions\RegionalServicePrice\BulkDeleteRegionalServicePricesAction;
use App\Actions\RegionalServicePrice\CreateRegionalServicePriceAction;
use App\Actions\RegionalServicePrice\DeleteRegionalServicePriceAction;
use App\Actions\RegionalServicePrice\ToggleRegionalServicePriceStatusAction;
use App\Actions\RegionalServicePrice\UpdateRegionalServicePriceAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegionalServicePrice\BulkDeleteRegionalServicePriceRequest;
use App\Http\Requests\RegionalServicePrice\StoreRegionalServicePriceRequest;
use App\Http\Requests\RegionalServicePrice\UpdateRegionalServicePriceRequest;
use App\Models\Category;
use App\Models\Region;
use App\Models\RegionalServicePrice;
use App\Models\Subcategory;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RegionalServicePriceController extends Controller
{
    /**
     * Display a listing of regional service prices with filters and pagination.
     */
    public function index(Request $request): View
    {
        $this->authorize('regional_prices.view');

        $perPageParam = strtolower($request->get('per_page', '10'));
        $query = RegionalServicePrice::with(['region', 'category', 'subcategory'])
            ->scopeValidRelations()
            ->filter($request->only(['search', 'region_id', 'category_id', 'subcategory_id', 'status', 'from_date', 'to_date']))
            ->latest('id');

        if ($perPageParam === 'all') {
            $totalCount = (clone $query)->count();
            $prices = $query->paginate(max($totalCount, 1));
        } else {
            $perPage = in_array((int) $perPageParam, [10, 20, 50, 100], true) ? (int) $perPageParam : 10;
            $prices = $query->paginate($perPage);
        }

        // Active, non-deleted filters
        $regions = Region::active()->orderBy('name')->get();
        $categories = Category::active()->orderBy('sort_order')->orderBy('name')->get();
        $subcategories = Subcategory::active()->orderBy('sort_order')->orderBy('name')->get();

        return view('dashboard.modules.regional-service-prices.index', compact('prices', 'regions', 'categories', 'subcategories'));
    }

    /**
     * Show the form for creating a new regional service price.
     */
    public function create(): View
    {
        $this->authorize('regional_prices.create');

        $regions = Region::active()->orderBy('name')->get();
        $categories = Category::active()->orderBy('sort_order')->orderBy('name')->get();
        $subcategories = Subcategory::active()->orderBy('sort_order')->orderBy('name')->get();

        return view('dashboard.modules.regional-service-prices.create', compact('regions', 'categories', 'subcategories'));
    }

    /**
     * Store a newly created regional service price in storage.
     */
    public function store(StoreRegionalServicePriceRequest $request, CreateRegionalServicePriceAction $action): RedirectResponse
    {
        $priceModel = $action->execute($request->validated());

        return redirect()
            ->route('dashboard.regional-service-prices.index')
            ->with('success', 'Regional service price created successfully.');
    }

    /**
     * Display the specified regional service price.
     */
    public function show(RegionalServicePrice $regionalServicePrice): View
    {
        $this->authorize('regional_prices.view');

        $regionalServicePrice->load(['region', 'category', 'subcategory']);

        return view('dashboard.modules.regional-service-prices.show', compact('regionalServicePrice'));
    }

    /**
     * Show the form for editing the specified regional service price.
     */
    public function edit(RegionalServicePrice $regionalServicePrice): View
    {
        $this->authorize('regional_prices.edit');

        $regionalServicePrice->load(['region', 'category', 'subcategory']);

        $regions = Region::active()->orderBy('name')->get();
        $categories = Category::active()->orderBy('sort_order')->orderBy('name')->get();
        $subcategories = Subcategory::where('category_id', $regionalServicePrice->category_id)
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('dashboard.modules.regional-service-prices.edit', compact('regionalServicePrice', 'regions', 'categories', 'subcategories'));
    }

    /**
     * Update the specified regional service price in storage.
     */
    public function update(UpdateRegionalServicePriceRequest $request, RegionalServicePrice $regionalServicePrice, UpdateRegionalServicePriceAction $action): RedirectResponse
    {
        $action->execute($regionalServicePrice, $request->validated());

        return redirect()
            ->route('dashboard.regional-service-prices.index')
            ->with('success', 'Regional service price updated successfully.');
    }

    /**
     * Remove the specified regional service price from storage (soft delete).
     */
    public function destroy(RegionalServicePrice $regionalServicePrice, DeleteRegionalServicePriceAction $action): RedirectResponse
    {
        $this->authorize('regional_prices.delete');

        try {
            $action->execute($regionalServicePrice);

            return redirect()
                ->route('dashboard.regional-service-prices.index')
                ->with('success', 'Regional service price deleted/archived successfully.');
        } catch (Exception $e) {
            return redirect()
                ->route('dashboard.regional-service-prices.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk delete selected regional service prices.
     */
    public function bulkDelete(BulkDeleteRegionalServicePriceRequest $request, BulkDeleteRegionalServicePricesAction $action): JsonResponse|RedirectResponse
    {
        $deleted = $action->execute($request->validated('selected_ids'));
        $message = "Deleted {$deleted} selected regional pricing record(s).";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted' => $deleted,
            ]);
        }

        return redirect()->route('dashboard.regional-service-prices.index')->with('success', $message);
    }

    /**
     * Toggle status between active and inactive.
     */
    public function toggleStatus(RegionalServicePrice $regionalServicePrice, ToggleRegionalServicePriceStatusAction $action): RedirectResponse
    {
        $this->authorize('regional_prices.status');

        try {
            $updated = $action->execute($regionalServicePrice);

            return back()->with('success', 'Status updated to '.ucfirst($updated->status).'.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * AJAX Endpoint: Get active subcategories for a given category.
     */
    public function getSubcategories(int $categoryId): JsonResponse
    {
        $subcategories = Subcategory::where('category_id', $categoryId)
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'icon', 'category_id']);

        return response()->json([
            'success' => true,
            'subcategories' => $subcategories,
        ]);
    }

    /**
     * Export pricing to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $this->authorize('regional_prices.view');

        $prices = RegionalServicePrice::with(['region', 'category', 'subcategory'])
            ->scopeValidRelations()
            ->filter($request->only(['search', 'region_id', 'category_id', 'subcategory_id', 'status', 'from_date', 'to_date']))
            ->latest('id')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="regional_prices_export_'.date('Y_m_d_His').'.csv"',
        ];

        return response()->stream(function () use ($prices) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Region', 'Category', 'Subcategory', 'Price', 'Currency', 'Status', 'Notes', 'Created At']);

            foreach ($prices as $item) {
                fputcsv($handle, [
                    $item->id,
                    $item->region?->name ?? '—',
                    $item->category?->name ?? '—',
                    $item->subcategory?->name ?? '—',
                    $item->price,
                    $item->currency,
                    ucfirst($item->status),
                    $item->notes ?? '—',
                    $item->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
