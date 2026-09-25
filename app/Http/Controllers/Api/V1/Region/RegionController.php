<?php

namespace App\Http\Controllers\Api\V1\Region;

use App\Actions\Region\BulkDeleteRegionsAction;
use App\Actions\Region\CreateRegionAction;
use App\Actions\Region\DeleteRegionAction;
use App\Actions\Region\ToggleRegionStatusAction;
use App\Actions\Region\UpdateRegionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Region\BulkDeleteRegionRequest;
use App\Http\Requests\Region\StoreRegionRequest;
use App\Http\Requests\Region\UpdateRegionRequest;
use App\Http\Resources\RegionResource;
use App\Models\Region;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RegionController extends Controller
{
    /**
     * Display a listing of active, non-deleted regions (or paginated list with filters).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);
        $regions = Region::withCount(['servicePrices'])
            ->filter($request->only(['search', 'status', 'from_date', 'to_date']))
            ->orderBy('name')
            ->latest('id')
            ->paginate($perPage);

        return RegionResource::collection($regions);
    }

    /**
     * Store a newly created region.
     */
    public function store(StoreRegionRequest $request, CreateRegionAction $action): JsonResponse
    {
        $region = $action->execute($request->validated());

        return (new RegionResource($region))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified region.
     */
    public function show(Region $region): RegionResource
    {
        return new RegionResource($region->load(['servicePrices.category', 'servicePrices.subcategory']));
    }

    /**
     * Update the specified region.
     */
    public function update(UpdateRegionRequest $request, Region $region, UpdateRegionAction $action): RegionResource
    {
        $updated = $action->execute($region, $request->validated());

        return new RegionResource($updated);
    }

    /**
     * Remove the specified region (soft delete).
     */
    public function destroy(Region $region, DeleteRegionAction $action): JsonResponse
    {
        $this->authorize('regions.delete');

        try {
            $action->execute($region);

            return response()->json([
                'success' => true,
                'message' => 'Region archived successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Bulk delete regions.
     */
    public function bulkDelete(BulkDeleteRegionRequest $request, BulkDeleteRegionsAction $action): JsonResponse
    {
        $deleted = $action->execute($request->validated('selected_ids'));

        return response()->json([
            'success' => true,
            'message' => "Archived {$deleted} selected region(s).",
            'deleted' => $deleted,
        ]);
    }

    /**
     * Toggle region active status.
     */
    public function toggleStatus(Region $region, ToggleRegionStatusAction $action): JsonResponse
    {
        $this->authorize('regions.status');

        $updated = $action->execute($region);

        return response()->json([
            'success' => true,
            'message' => "Status changed to {$updated->status}.",
            'data' => new RegionResource($updated),
        ]);
    }
}
