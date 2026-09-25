<?php

namespace App\Http\Controllers\Api\V1\Pricing;

use App\Actions\RegionalServicePrice\BulkDeleteRegionalServicePricesAction;
use App\Actions\RegionalServicePrice\CreateRegionalServicePriceAction;
use App\Actions\RegionalServicePrice\DeleteRegionalServicePriceAction;
use App\Actions\RegionalServicePrice\ToggleRegionalServicePriceStatusAction;
use App\Actions\RegionalServicePrice\UpdateRegionalServicePriceAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegionalServicePrice\BulkDeleteRegionalServicePriceRequest;
use App\Http\Requests\RegionalServicePrice\StoreRegionalServicePriceRequest;
use App\Http\Requests\RegionalServicePrice\UpdateRegionalServicePriceRequest;
use App\Http\Resources\RegionalServicePriceResource;
use App\Models\RegionalServicePrice;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RegionalServicePriceController extends Controller
{
    /**
     * Display a listing of regional service prices (filters: region_id, category_id, subcategory_id).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 20);
        $prices = RegionalServicePrice::with(['region', 'category', 'subcategory'])
            ->scopeValidRelations()
            ->filter($request->only(['search', 'region_id', 'category_id', 'subcategory_id', 'status', 'from_date', 'to_date']))
            ->latest('id')
            ->paginate($perPage);

        return RegionalServicePriceResource::collection($prices);
    }

    /**
     * Store a newly created regional price.
     */
    public function store(StoreRegionalServicePriceRequest $request, CreateRegionalServicePriceAction $action): JsonResponse
    {
        $price = $action->execute($request->validated());

        return (new RegionalServicePriceResource($price->load(['region', 'category', 'subcategory'])))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified regional service price.
     */
    public function show(RegionalServicePrice $regionalServicePrice): RegionalServicePriceResource
    {
        return new RegionalServicePriceResource($regionalServicePrice->load(['region', 'category', 'subcategory']));
    }

    /**
     * Update the specified regional service price.
     */
    public function update(UpdateRegionalServicePriceRequest $request, RegionalServicePrice $regionalServicePrice, UpdateRegionalServicePriceAction $action): RegionalServicePriceResource
    {
        $updated = $action->execute($regionalServicePrice, $request->validated());

        return new RegionalServicePriceResource($updated->load(['region', 'category', 'subcategory']));
    }

    /**
     * Remove the specified regional price (soft delete).
     */
    public function destroy(RegionalServicePrice $regionalServicePrice, DeleteRegionalServicePriceAction $action): JsonResponse
    {
        $this->authorize('regional_prices.delete');

        try {
            $action->execute($regionalServicePrice);

            return response()->json([
                'success' => true,
                'message' => 'Regional price archived successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Bulk delete regional prices.
     */
    public function bulkDelete(BulkDeleteRegionalServicePriceRequest $request, BulkDeleteRegionalServicePricesAction $action): JsonResponse
    {
        $deleted = $action->execute($request->validated('selected_ids'));

        return response()->json([
            'success' => true,
            'message' => "Archived {$deleted} selected regional pricing record(s).",
            'deleted' => $deleted,
        ]);
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(RegionalServicePrice $regionalServicePrice, ToggleRegionalServicePriceStatusAction $action): JsonResponse
    {
        $this->authorize('regional_prices.status');

        $updated = $action->execute($regionalServicePrice);

        return response()->json([
            'success' => true,
            'message' => "Status changed to {$updated->status}.",
            'data' => new RegionalServicePriceResource($updated),
        ]);
    }
}
