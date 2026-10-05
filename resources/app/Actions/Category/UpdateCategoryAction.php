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
                'status' => $data['status'] ?? $category->status,
                'sort_order' => $data['sort_order'] ?? $category->sort_order,
            ];

            // 1. Handle Image Upload & Removal
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

            // 2. Handle Icon Upload, Update, & Removal
            if (isset($data['icon']) && $data['icon'] instanceof UploadedFile) {
                if ($category->icon && Storage::disk('public')->exists($category->icon)) {
                    Storage::disk('public')->delete($category->icon);
                }
                $updateData['icon'] = $data['icon']->store('categories/icons', 'public');
            } elseif (! empty($data['remove_icon'])) {
                if ($category->icon && Storage::disk('public')->exists($category->icon)) {
                    Storage::disk('public')->delete($category->icon);
                }
                $updateData['icon'] = null;
            } elseif (array_key_exists('icon', $data) && is_string($data['icon'])) {
                $updateData['icon'] = $data['icon'];
            }

            $category->update($updateData);

            return $category;
        });
    }
}
