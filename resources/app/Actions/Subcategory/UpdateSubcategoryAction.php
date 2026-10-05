<?php

namespace App\Actions\Subcategory;

use App\Models\Subcategory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UpdateSubcategoryAction
{
    /**
     * Update subcategory details and handle image updates/removal.
     */
    public function execute(Subcategory $subcategory, array $data): Subcategory
    {
        return DB::transaction(function () use ($subcategory, $data) {
            $updateData = [
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'slug' => ! empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']),
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? $subcategory->status,
                'sort_order' => $data['sort_order'] ?? $subcategory->sort_order,
            ];

            // 1. Handle Image Upload & Removal
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                if ($subcategory->image && Storage::disk('public')->exists($subcategory->image)) {
                    Storage::disk('public')->delete($subcategory->image);
                }
                $updateData['image'] = $data['image']->store('subcategories', 'public');
            } elseif (! empty($data['remove_image'])) {
                if ($subcategory->image && Storage::disk('public')->exists($subcategory->image)) {
                    Storage::disk('public')->delete($subcategory->image);
                }
                $updateData['image'] = null;
            }

            // 2. Handle Icon Upload, Update, & Removal
            if (isset($data['icon']) && $data['icon'] instanceof UploadedFile) {
                if ($subcategory->icon && Storage::disk('public')->exists($subcategory->icon)) {
                    Storage::disk('public')->delete($subcategory->icon);
                }
                $updateData['icon'] = $data['icon']->store('subcategories/icons', 'public');
            } elseif (! empty($data['remove_icon'])) {
                if ($subcategory->icon && Storage::disk('public')->exists($subcategory->icon)) {
                    Storage::disk('public')->delete($subcategory->icon);
                }
                $updateData['icon'] = null;
            } elseif (array_key_exists('icon', $data) && is_string($data['icon'])) {
                $updateData['icon'] = $data['icon'];
            }

            $subcategory->update($updateData);

            return $subcategory;
        });
    }
}
