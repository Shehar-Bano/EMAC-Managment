<?php

namespace App\Http\Controllers\Api\V1\Category;

use App\Actions\Subcategory\BulkDeleteSubcategoriesAction;
use App\Actions\Subcategory\CreateSubcategoryAction;
use App\Actions\Subcategory\DeleteSubcategoryAction;
use App\Actions\Subcategory\ToggleSubcategoryStatusAction;
use App\Actions\Subcategory\UpdateSubcategoryAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subcategory\BulkDeleteSubcategoryRequest;
use App\Http\Requests\Subcategory\StoreSubcategoryRequest;
use App\Http\Requests\Subcategory\UpdateSubcategoryRequest;
use App\Http\Resources\SubcategoryResource;
use App\Models\Region;
use App\Models\Subcategory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubcategoryController extends Controller
{
    /**
     * Resolve target Region model from request parameters (region_id, region, region_code, or region_slug).
     */
    protected function resolveRegion(Request $request): ?Region
    {
        $param = $request->get('region_id') ?? $request->get('region') ?? $request->get('region_code') ?? $request->get('region_slug');
        if (blank($param)) {
            return null;
        }

        if (is_numeric($param)) {
            return Region::whereNull('deleted_at')->where('id', (int) $param)->first();
        }

        return Region::whereNull('deleted_at')
            ->where(function ($q) use ($param) {
                $q->where('slug', $param)
                    ->orWhere('code', $param)
                    ->orWhere('name', $param);
            })
            ->first();
    }

    /**
     * Display a listing of subcategories with category details and regional pricing.
     */
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        $targetRegion = $this->resolveRegion($request);
        $targetRegionId = $targetRegion?->id;

        $query = Subcategory::query()
            ->whereNull('deleted_at')
            ->whereHas('category', fn ($cat) => $cat->whereNull('deleted_at'))
            ->with([
                'category' => fn ($cat) => $cat->whereNull('deleted_at'),
                'regionalServicePrices' => function ($priceQuery) use ($targetRegionId) {
                    $priceQuery->whereNull('deleted_at')
                        ->where('status', 'active')
                        ->when($targetRegionId, fn ($pq) => $pq->where('region_id', $targetRegionId))
                        ->whereHas('region', fn ($r) => $r->whereNull('deleted_at')->where('status', 'active'))
                        ->with(['region' => fn ($r) => $r->whereNull('deleted_at')]);
                },
            ])
            ->filter($request->only(['search', 'status', 'category_id', 'from_date', 'to_date']))
            ->when($targetRegionId && $request->boolean('only_with_prices'), function ($q) use ($targetRegionId) {
                $q->whereHas('regionalServicePrices', function ($pq) use ($targetRegionId) {
                    $pq->whereNull('deleted_at')
                        ->where('status', 'active')
                        ->where('region_id', $targetRegionId);
                });
            })
            ->orderBy('sort_order')
            ->latest('id');

        if ($request->get('per_page') === 'all' || $request->boolean('all')) {
            return response()->json([
                'success' => true,
                'data' => SubcategoryResource::collection($query->get()),
                'meta' => [
                    'region_id' => $targetRegionId,
                    'region_name' => $targetRegion?->name,
                    'region_code' => $targetRegion?->code,
                    'currency' => $targetRegion?->currency,
                ],
            ]);
        }

        $perPage = max(1, min(100, (int) $request->get('per_page', 15)));
        $subcategories = $query->paginate($perPage);

        return SubcategoryResource::collection($subcategories);
    }

    /**
     * Store a newly created subcategory.
     */
    public function store(StoreSubcategoryRequest $request, CreateSubcategoryAction $action): JsonResponse
    {
        $subcategory = $action->execute($request->validated());

        return (new SubcategoryResource($subcategory->load(['category'])))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified subcategory with its category and regional pricing.
     */
    public function show(Subcategory $subcategory, Request $request): SubcategoryResource
    {
        $targetRegion = $this->resolveRegion($request);
        $targetRegionId = $targetRegion?->id;

        $subcategory->load([
            'category' => fn ($cat) => $cat->whereNull('deleted_at'),
            'regionalServicePrices' => function ($priceQuery) use ($targetRegionId) {
                $priceQuery->whereNull('deleted_at')
                    ->where('status', 'active')
                    ->when($targetRegionId, fn ($pq) => $pq->where('region_id', $targetRegionId))
                    ->whereHas('region', fn ($r) => $r->whereNull('deleted_at')->where('status', 'active'))
                    ->with(['region' => fn ($r) => $r->whereNull('deleted_at')]);
            },
        ]);

        return new SubcategoryResource($subcategory);
    }

    /**
     * Update the specified subcategory.
     */
    public function update(UpdateSubcategoryRequest $request, Subcategory $subcategory, UpdateSubcategoryAction $action): SubcategoryResource
    {
        $updated = $action->execute($subcategory, $request->validated());

        return new SubcategoryResource($updated->load(['category']));
    }

    /**
     * Remove the specified subcategory.
     */
    public function destroy(Subcategory $subcategory, DeleteSubcategoryAction $action): JsonResponse
    {
        $this->authorize('subcategories.delete');

        try {
            $action->execute($subcategory);

            return response()->json([
                'success' => true,
                'message' => 'Subcategory deleted successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Bulk delete subcategories.
     */
    public function bulkDelete(BulkDeleteSubcategoryRequest $request, BulkDeleteSubcategoriesAction $action): JsonResponse
    {
        $result = $action->execute($request->validated('ids'));

        return response()->json([
            'success' => true,
            'message' => "Bulk delete operation completed. Deleted: {$result['deleted']}, Skipped: {$result['skipped']}.",
            'data' => $result,
        ]);
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(Subcategory $subcategory, ToggleSubcategoryStatusAction $action): JsonResponse
    {
        $this->authorize('subcategories.status');

        try {
            $updated = $action->execute($subcategory);

            return response()->json([
                'success' => true,
                'message' => "Subcategory status updated to {$updated->status}.",
                'data' => new SubcategoryResource($updated->load(['category'])),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
