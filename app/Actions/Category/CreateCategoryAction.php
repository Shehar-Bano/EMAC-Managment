<?php

namespace App\Actions\Category;

use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateCategoryAction
{
    /**
     * Create a new category with optional image upload and automatic slug generation.
     */
    public function execute(array $data): Category
    {
        return DB::transaction(function () use ($data) {
            $imagePath = null;
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $imagePath = $data['image']->store('categories', 'public');
            }

            $iconValue = null;
            if (isset($data['icon']) && $data['icon'] instanceof UploadedFile) {
                $iconValue = $data['icon']->store('categories/icons', 'public');
            } elseif (isset($data['icon']) && is_string($data['icon'])) {
                $iconValue = $data['icon'];
            }

            return Category::create([
                'name' => $data['name'],
                'slug' => ! empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']),
                'description' => $data['description'] ?? null,
                'icon' => $iconValue,
                'image' => $imagePath,
                'status' => $data['status'] ?? 'active',
                'sort_order' => $data['sort_order'] ?? 0,
            ]);
        });
    }
}
