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
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    /**
     * Display a paginated listing of categories.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('categories.view');

        $perPage = (int) $request->get('per_page', 15);
        $categories = Category::withCount(['subcategories'])
            ->filter($request->only(['search', 'status', 'from_date', 'to_date']))
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate($perPage);

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
     * Display the specified category.
     */
    public function show(Category $category): CategoryResource
    {
        $this->authorize('categories.view');

        return new CategoryResource($category->load(['subcategories']));
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
