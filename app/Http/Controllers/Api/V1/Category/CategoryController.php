<?php

namespace App\Http\Controllers\Api\V1\Category;

use App\Actions\Category\BulkDeleteCategoriesAction;
use App\Actions\Category\CreateCategoryAction;
use App\Actions\Category\DeleteCategoryAction;
use App\Actions\Category\ToggleCategoryStatusAction;
use App\Actions\Category\UpdateCategoryAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\BulkDeleteCategoryRequest;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Region;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
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
     * Display a comprehensive catalog of active categories, subcategories, and regional pricing.
     */
    public function catalog(Request $request): JsonResponse
    {
        $targetRegion = $this->resolveRegion($request);
        $targetRegionId = $targetRegion?->id;

        $categories = Category::query()
            ->whereNull('deleted_at')
            ->where('status', 'active')
            ->when($request->filled('search'), fn ($q) => $q->search($request->get('search')))
            ->when($targetRegionId && $request->boolean('only_with_prices'), function ($q) use ($targetRegionId) {
                $q->whereHas('subcategories.regionalServicePrices', function ($pq) use ($targetRegionId) {
                    $pq->whereNull('deleted_at')
                        ->where('status', 'active')
                        ->where('region_id', $targetRegionId);
                });
            })
            ->with([
                'subcategories' => function ($subQuery) use ($targetRegionId, $request) {
                    $subQuery->whereNull('deleted_at')
                        ->where('status', 'active')
                        ->when($targetRegionId && $request->boolean('only_with_prices'), function ($sq) use ($targetRegionId) {
                            $sq->whereHas('regionalServicePrices', fn ($pq) => $pq->whereNull('deleted_at')->where('status', 'active')->where('region_id', $targetRegionId));
                        })
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->with([
                            'regionalServicePrices' => function ($priceQuery) use ($targetRegionId) {
                                $priceQuery->whereNull('deleted_at')
                                    ->where('status', 'active')
                                    ->when($targetRegionId, fn ($pq) => $pq->where('region_id', $targetRegionId))
                                    ->whereHas('region', fn ($r) => $r->whereNull('deleted_at')->where('status', 'active'))
                                    ->with(['region' => fn ($r) => $r->whereNull('deleted_at')]);
                            },
                        ]);
                },
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Categories with subcategories and pricing retrieved successfully.',
            'data' => CategoryResource::collection($categories),
            'meta' => [
                'total_categories' => $categories->count(),
                'region_id' => $targetRegionId,
                'region_name' => $targetRegion?->name,
                'region_code' => $targetRegion?->code,
                'currency' => $targetRegion?->currency,
            ],
        ]);
    }

    /**
     * Display a listing of categories (with optional subcategories and regional pricing).
     */
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        $targetRegion = $this->resolveRegion($request);
        $targetRegionId = $targetRegion?->id;
        $includeSubcategories = $request->boolean('with_subcategories') || $request->boolean('with_prices') || in_array('subcategories', explode(',', (string) $request->get('include', '')));

        $query = Category::query()
            ->whereNull('deleted_at')
            ->withCount(['subcategories' => fn ($q) => $q->whereNull('deleted_at')])
            ->filter($request->only(['search', 'status', 'from_date', 'to_date']))
            ->when($targetRegionId && $request->boolean('only_with_prices'), function ($q) use ($targetRegionId) {
                $q->whereHas('subcategories.regionalServicePrices', function ($pq) use ($targetRegionId) {
                    $pq->whereNull('deleted_at')
                        ->where('status', 'active')
                        ->where('region_id', $targetRegionId);
                });
            })
            ->orderBy('sort_order')
            ->latest('id');

        if ($includeSubcategories) {
            $query->with([
                'subcategories' => function ($subQuery) use ($targetRegionId, $request) {
                    $subQuery->whereNull('deleted_at')
                        ->where('status', 'active')
                        ->when($targetRegionId && $request->boolean('only_with_prices'), function ($sq) use ($targetRegionId) {
                            $sq->whereHas('regionalServicePrices', fn ($pq) => $pq->whereNull('deleted_at')->where('status', 'active')->where('region_id', $targetRegionId));
                        })
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->with([
                            'regionalServicePrices' => function ($priceQuery) use ($targetRegionId) {
                                $priceQuery->whereNull('deleted_at')
                                    ->where('status', 'active')
                                    ->when($targetRegionId, fn ($pq) => $pq->where('region_id', $targetRegionId))
                                    ->whereHas('region', fn ($r) => $r->whereNull('deleted_at')->where('status', 'active'))
                                    ->with(['region' => fn ($r) => $r->whereNull('deleted_at')]);
                            },
                        ]);
                },
            ]);
        }

        if ($request->get('per_page') === 'all' || $request->boolean('all')) {
            return response()->json([
                'success' => true,
                'data' => CategoryResource::collection($query->get()),
                'meta' => [
                    'region_id' => $targetRegionId,
                    'region_name' => $targetRegion?->name,
                    'region_code' => $targetRegion?->code,
                    'currency' => $targetRegion?->currency,
                ],
            ]);
        }

        $perPage = max(1, min(100, (int) $request->get('per_page', 15)));
        $categories = $query->paginate($perPage);

        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreCategoryRequest $request, CreateCategoryAction $action): JsonResponse
    {
        $category = $action->execute($request->validated());

        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified category with subcategories and pricing.
     */
    public function show(Category $category, Request $request): CategoryResource
    {
        $targetRegion = $this->resolveRegion($request);
        $targetRegionId = $targetRegion?->id;

        $category->load([
            'subcategories' => function ($subQuery) use ($targetRegionId) {
                $subQuery->whereNull('deleted_at')
                    ->where('status', 'active')
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->with([
                        'regionalServicePrices' => function ($priceQuery) use ($targetRegionId) {
                            $priceQuery->whereNull('deleted_at')
                                ->where('status', 'active')
                                ->when($targetRegionId, fn ($pq) => $pq->where('region_id', $targetRegionId))
                                ->whereHas('region', fn ($r) => $r->whereNull('deleted_at')->where('status', 'active'))
                                ->with(['region' => fn ($r) => $r->whereNull('deleted_at')]);
                        },
                    ]);
            },
        ]);

        return new CategoryResource($category);
    }

    /**
     * Update the specified category.
     */
    public function update(UpdateCategoryRequest $request, Category $category, UpdateCategoryAction $action): CategoryResource
    {
        $updated = $action->execute($category, $request->validated());

        return new CategoryResource($updated);
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category, DeleteCategoryAction $action): JsonResponse
    {
        $this->authorize('categories.delete');

        try {
            $action->execute($category);

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Bulk delete categories.
     */
    public function bulkDelete(BulkDeleteCategoryRequest $request, BulkDeleteCategoriesAction $action): JsonResponse
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
    public function toggleStatus(Category $category, ToggleCategoryStatusAction $action): JsonResponse
    {
        $this->authorize('categories.status');

        try {
            $updated = $action->execute($category);

            return response()->json([
                'success' => true,
                'message' => "Category status updated to {$updated->status}.",
                'data' => new CategoryResource($updated),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
