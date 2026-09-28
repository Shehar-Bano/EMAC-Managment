<?php

namespace App\Actions\Category;

use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UpdateCategoryAction
{
    /**
     * Update category attributes and handle image updates/removal.
     */
    public function execute(Category $category, array $data): Category
    {
        return DB::transaction(function () use ($category, $data) {
            $updateData = [
                'name' => $data['name'],
                'slug' => ! empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']),
                'description' => $data['description'] ?? null,
                'icon' => $data['icon'] ?? null,
                'status' => $data['status'] ?? $category->status,
                'sort_order' => $data['sort_order'] ?? $category->sort_order,
            ];

            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                if ($category->image && Storage::disk('public')->exists($category->image)) {
                    Storage::disk('public')->delete($category->image);
                }
                $updateData['image'] = $data['image']->store('categories', 'public');
            } elseif (! empty($data['remove_image'])) {
                if ($category->image && Storage::disk('public')->exists($category->image)) {
                    Storage::disk('public')->delete($category->image);
                }
                $updateData['image'] = null;
            }

            $category->update($updateData);

            return $category;
        });
    }
}
