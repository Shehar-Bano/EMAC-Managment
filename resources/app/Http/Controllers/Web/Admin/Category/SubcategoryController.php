<?php

namespace App\Http\Controllers\Web\Admin\Category;

use App\Actions\Subcategory\BulkDeleteSubcategoriesAction;
use App\Actions\Subcategory\CreateSubcategoryAction;
use App\Actions\Subcategory\DeleteSubcategoryAction;
use App\Actions\Subcategory\ToggleSubcategoryStatusAction;
use App\Actions\Subcategory\UpdateSubcategoryAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subcategory\BulkDeleteSubcategoryRequest;
use App\Http\Requests\Subcategory\StoreSubcategoryRequest;
use App\Http\Requests\Subcategory\UpdateSubcategoryRequest;
use App\Models\Category;
use App\Models\Subcategory;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubcategoryController extends Controller
{
    /**
     * Display a listing of subcategories with category filter and pagination.
     */
    public function index(Request $request): View
    {
        $this->authorize('subcategories.view');

        $perPageParam = strtolower($request->get('per_page', '10'));
        $query = Subcategory::with(['category'])
            ->filter($request->only(['search', 'status', 'category_id', 'from_date', 'to_date']))
            ->orderBy('sort_order')
            ->latest('id');

        if ($perPageParam === 'all') {
            $totalCount = (clone $query)->count();
            $subcategories = $query->paginate(max($totalCount, 1));
        } else {
            $perPage = in_array((int) $perPageParam, [10, 20, 50, 100], true) ? (int) $perPageParam : 10;
            $subcategories = $query->paginate($perPage);
        }

        $categories = Category::orderBy('name')->get();

        return view('dashboard.modules.subcategories.index', compact('subcategories', 'categories'));
    }

    /**
     * Show the form for creating a new subcategory.
     */
    public function create(Request $request): View
    {
        $this->authorize('subcategories.create');

        $categories = Category::orderBy('name')->get();
        $selectedCategoryId = $request->get('category_id');

        return view('dashboard.modules.subcategories.create', compact('categories', 'selectedCategoryId'));
    }

    /**
     * Store a newly created subcategory in storage.
     */
    public function store(StoreSubcategoryRequest $request, CreateSubcategoryAction $action): RedirectResponse
    {
        $subcategory = $action->execute($request->validated());

        return redirect()
            ->route('dashboard.subcategories.index')
            ->with('success', "Subcategory '{$subcategory->name}' created successfully.");
    }

    /**
     * Display the specified subcategory.
     */
    public function show(Subcategory $subcategory): View
    {
        $this->authorize('subcategories.view');

        $subcategory->load(['category']);

        return view('dashboard.modules.subcategories.show', compact('subcategory'));
    }

    /**
     * Show the form for editing the specified subcategory.
     */
    public function edit(Subcategory $subcategory): View
    {
        $this->authorize('subcategories.edit');

        $categories = Category::orderBy('name')->get();

        return view('dashboard.modules.subcategories.edit', compact('subcategory', 'categories'));
    }

    /**
     * Update the specified subcategory in storage.
     */
    public function update(UpdateSubcategoryRequest $request, Subcategory $subcategory, UpdateSubcategoryAction $action): RedirectResponse
    {
        $action->execute($subcategory, $request->validated());

        return redirect()
            ->route('dashboard.subcategories.index')
            ->with('success', "Subcategory '{$subcategory->name}' updated successfully.");
    }

    /**
     * Remove the specified subcategory from storage.
     */
    public function destroy(Subcategory $subcategory, DeleteSubcategoryAction $action): RedirectResponse
    {
        $this->authorize('subcategories.delete');

        try {
            $subcategoryName = $subcategory->name;
            $action->execute($subcategory);

            return redirect()
                ->route('dashboard.subcategories.index')
                ->with('success', "Subcategory '{$subcategoryName}' has been deleted.");
        } catch (Exception $e) {
            return redirect()
                ->route('dashboard.subcategories.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk delete selected subcategories.
     */
    public function bulkDelete(BulkDeleteSubcategoryRequest $request, BulkDeleteSubcategoriesAction $action): JsonResponse|RedirectResponse
    {
        $result = $action->execute($request->validated('ids'));

        $message = "Deleted {$result['deleted']} selected subcategory(ies).";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $result,
            ]);
        }

        return redirect()->route('dashboard.subcategories.index')->with('success', $message);
    }

    /**
     * Toggle the active/inactive status of a subcategory.
     */
    public function toggleStatus(Subcategory $subcategory, ToggleSubcategoryStatusAction $action): RedirectResponse
    {
        $this->authorize('subcategories.status');

        try {
            $updated = $action->execute($subcategory);

            return back()->with('success', "Status for '{$updated->name}' changed to ".ucfirst($updated->status).'.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Export subcategories matching active filter criteria to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $this->authorize('subcategories.view');

        $subcategories = Subcategory::with(['category'])
            ->filter($request->only(['search', 'status', 'category_id', 'from_date', 'to_date']))
            ->orderBy('sort_order')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="subcategories_export_'.date('Y_m_d_His').'.csv"',
        ];

        return response()->stream(function () use ($subcategories) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Category', 'Name', 'Slug', 'Status', 'Sort Order', 'Created At']);

            foreach ($subcategories as $sub) {
                fputcsv($handle, [
                    $sub->id,
                    $sub->category?->name ?? 'N/A',
                    $sub->name,
                    $sub->slug,
                    ucfirst($sub->status),
                    $sub->sort_order,
                    $sub->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
