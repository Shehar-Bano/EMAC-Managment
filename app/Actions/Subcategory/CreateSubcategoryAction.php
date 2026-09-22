<?php

namespace App\Actions\Subcategory;

use App\Models\Subcategory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateSubcategoryAction
{
    /**
     * Create a new subcategory with optional image upload and automatic slug generation.
     */
    public function execute(array $data): Subcategory
    {
        return DB::transaction(function () use ($data) {
            $imagePath = null;
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $imagePath = $data['image']->store('subcategories', 'public');
            }

            return Subcategory::create([
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'slug' => ! empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']),
                'description' => $data['description'] ?? null,
                'icon' => $data['icon'] ?? null,
                'image' => $imagePath,
                'status' => $data['status'] ?? 'active',
                'sort_order' => $data['sort_order'] ?? 0,
            ]);
        });
    }
}
