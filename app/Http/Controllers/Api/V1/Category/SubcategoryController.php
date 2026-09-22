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
use App\Models\Subcategory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubcategoryController extends Controller
{
    /**
     * Display a paginated listing of subcategories.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('subcategories.view');

        $perPage = (int) $request->get('per_page', 15);
        $subcategories = Subcategory::with(['category'])
            ->filter($request->only(['search', 'status', 'category_id', 'from_date', 'to_date']))
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate($perPage);

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
     * Display the specified subcategory.
     */
    public function show(Subcategory $subcategory): SubcategoryResource
    {
        $this->authorize('subcategories.view');

        return new SubcategoryResource($subcategory->load(['category']));
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
