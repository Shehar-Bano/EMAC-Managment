<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegionalServicePrice extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'regional_service_prices';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'region_id',
        'category_id',
        'subcategory_id',
        'price',
        'currency',
        'notes',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    /**
     * Region relationship.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    /**
     * Category relationship.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Subcategory relationship.
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }

    /**
     * Scope for active pricing entries.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope ensuring related entities are not soft-deleted.
     */
    public function scopeValidRelations(Builder $query): Builder
    {
        return $query->whereHas('region')
            ->whereHas('category')
            ->whereHas('subcategory');
    }

    /**
     * Scope to search by notes or relation names.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (blank($search)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($search) {
            $q->where('notes', 'like', "%{$search}%")
                ->orWhere('price', 'like', "%{$search}%")
                ->orWhereHas('region', fn (Builder $sub) => $sub->where('name', 'like', "%{$search}%"))
                ->orWhereHas('category', fn (Builder $sub) => $sub->where('name', 'like', "%{$search}%"))
                ->orWhereHas('subcategory', fn (Builder $sub) => $sub->where('name', 'like', "%{$search}%"));
        });
    }

    /**
     * Scope to apply index filters.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, fn (Builder $q, $search) => $q->search($search))
            ->when($filters['region_id'] ?? null, function (Builder $q, $regionId) {
                if ($regionId !== 'all') {
                    $q->where('region_id', $regionId);
                }
            })
            ->when($filters['category_id'] ?? null, function (Builder $q, $categoryId) {
                if ($categoryId !== 'all') {
                    $q->where('category_id', $categoryId);
                }
            })
            ->when($filters['subcategory_id'] ?? null, function (Builder $q, $subcategoryId) {
                if ($subcategoryId !== 'all') {
                    $q->where('subcategory_id', $subcategoryId);
                }
            })
            ->when($filters['status'] ?? null, function (Builder $q, $status) {
                if ($status !== 'all') {
                    $q->where('status', $status);
                }
            })
            ->when($filters['from_date'] ?? null, fn (Builder $q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($filters['to_date'] ?? null, fn (Builder $q, $to) => $q->whereDate('created_at', '<=', $to));
    }
}
