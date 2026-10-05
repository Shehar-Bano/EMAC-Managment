<?php

namespace App\Http\Controllers\Web\Admin\Category;

use App\Actions\Category\BulkDeleteCategoriesAction;
use App\Actions\Category\CreateCategoryAction;
use App\Actions\Category\DeleteCategoryAction;
use App\Actions\Category\ToggleCategoryStatusAction;
use App\Actions\Category\UpdateCategoryAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\BulkDeleteCategoryRequest;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories with filters and dynamic pagination.
     */
    public function index(Request $request): View
    {
        $this->authorize('categories.view');

        $perPageParam = strtolower($request->get('per_page', '10'));
        $query = Category::withCount(['subcategories'])
            ->filter($request->only(['search', 'status', 'from_date', 'to_date']))
            ->orderBy('sort_order')
            ->latest('id');

        if ($perPageParam === 'all') {
            $totalCount = (clone $query)->count();
            $categories = $query->paginate(max($totalCount, 1));
        } else {
            $perPage = in_array((int) $perPageParam, [10, 20, 50, 100], true) ? (int) $perPageParam : 10;
            $categories = $query->paginate($perPage);
        }

        return view('dashboard.modules.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        $this->authorize('categories.create');

        return view('dashboard.modules.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(StoreCategoryRequest $request, CreateCategoryAction $action): RedirectResponse
    {
        $category = $action->execute($request->validated());

        return redirect()
            ->route('dashboard.categories.index')
            ->with('success', "Category '{$category->name}' created successfully.");
    }

    /**
     * Display the specified category with subcategories.
     */
    public function show(Category $category): View
    {
        $this->authorize('categories.view');

        $category->load(['subcategories']);

        return view('dashboard.modules.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): View
    {
        $this->authorize('categories.edit');

        return view('dashboard.modules.categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category, UpdateCategoryAction $action): RedirectResponse
    {
        $action->execute($category, $request->validated());

        return redirect()
            ->route('dashboard.categories.index')
            ->with('success', "Category '{$category->name}' updated successfully.");
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category, DeleteCategoryAction $action): RedirectResponse
    {
        $this->authorize('categories.delete');

        try {
            $categoryName = $category->name;
            $action->execute($category);

            return redirect()
                ->route('dashboard.categories.index')
                ->with('success', "Category '{$categoryName}' and its subcategories have been deleted.");
        } catch (Exception $e) {
            return redirect()
                ->route('dashboard.categories.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk delete selected categories.
     */
    public function bulkDelete(BulkDeleteCategoryRequest $request, BulkDeleteCategoriesAction $action): JsonResponse|RedirectResponse
    {
        $result = $action->execute($request->validated('ids'));

        $message = "Deleted {$result['deleted']} selected category(ies).";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $result,
            ]);
        }

        return redirect()->route('dashboard.categories.index')->with('success', $message);
    }

    /**
     * Toggle the active/inactive status of a category.
     */
    public function toggleStatus(Category $category, ToggleCategoryStatusAction $action): RedirectResponse
    {
        $this->authorize('categories.status');

        try {
            $updatedCategory = $action->execute($category);

            return back()->with('success', "Status for '{$updatedCategory->name}' changed to ".ucfirst($updatedCategory->status).'.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Export categories matching active filter criteria to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $this->authorize('categories.view');

        $categories = Category::withCount(['subcategories'])
            ->filter($request->only(['search', 'status', 'from_date', 'to_date']))
            ->orderBy('sort_order')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="categories_export_'.date('Y_m_d_His').'.csv"',
        ];

        return response()->stream(function () use ($categories) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Slug', 'Subcategories Count', 'Status', 'Sort Order', 'Created At']);

            foreach ($categories as $category) {
                fputcsv($handle, [
                    $category->id,
                    $category->name,
                    $category->slug,
                    $category->subcategories_count,
                    ucfirst($category->status),
                    $category->sort_order,
                    $category->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
