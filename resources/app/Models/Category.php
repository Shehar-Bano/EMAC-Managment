<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'image',
        'status',
        'sort_order',
    ];

    /**
     * Subcategories under this category.
     */
    public function subcategories(): HasMany
    {
        return $this->hasMany(Subcategory::class, 'category_id')->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Regional service pricing configurations for this category.
     */
    public function regionalServicePrices(): HasMany
    {
        return $this->hasMany(RegionalServicePrice::class, 'category_id');
    }

    /**
     * Active regional service pricing configurations.
     */
    public function activeRegionalServicePrices(): HasMany
    {
        return $this->hasMany(RegionalServicePrice::class, 'category_id')
            ->where('status', 'active');
    }

    /**
     * Customer service requests categorized under this category.
     */
    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'category_id');
    }

    /**
     * Category image URL accessor with elegant fallback.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->image)) {
                    return null;
                }

                if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://') || str_starts_with($this->image, 'data:image/')) {
                    return $this->image;
                }

                return asset('storage/'.ltrim($this->image, '/'));
            }
        );
    }

    /**
     * Category icon URL accessor with elegant fallback.
     */
    protected function iconUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->icon)) {
                    return null;
                }

                if (str_starts_with($this->icon, 'http://') || str_starts_with($this->icon, 'https://') || str_starts_with($this->icon, 'data:image/')) {
                    return $this->icon;
                }

                if (str_contains($this->icon, '/') || str_contains($this->icon, '.')) {
                    return asset('storage/'.ltrim($this->icon, '/'));
                }

                return $this->icon;
            }
        );
    }

    /**
     * Active subcategories count accessor.
     */
    public function activeSubcategories(): HasMany
    {
        return $this->subcategories()->where('status', 'active');
    }

    /**
     * Scope for active categories.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to search by name or description.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (blank($search)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to apply standard index filters.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, fn (Builder $q, $search) => $q->search($search))
            ->when($filters['status'] ?? null, function (Builder $q, $status) {
                if ($status !== 'all') {
                    $q->where('status', $status);
                }
            })
            ->when($filters['from_date'] ?? null, fn (Builder $q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($filters['to_date'] ?? null, fn (Builder $q, $to) => $q->whereDate('created_at', '<=', $to));
    }
}
