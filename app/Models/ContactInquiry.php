<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactInquiry extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'market',
        'region_id',
        'category_id',
        'subcategory_id',
        'estimated_price',
        'currency',
        'message',
        'photographs',
        'video',
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
            'photographs' => 'array',
            'estimated_price' => 'decimal:2',
        ];
    }

    /**
     * Category related to this inquiry.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Subcategory related to this inquiry.
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }

    /**
     * Region related to this inquiry.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    /**
     * Scope for filter.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function (Builder $q, $search) {
                $q->where(function (Builder $subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, function (Builder $q, $status) {
                if ($status !== 'all') {
                    $q->where('status', $status);
                }
            })
            ->when($filters['market'] ?? null, function (Builder $q, $market) {
                if ($market !== 'all') {
                    $q->where('market', $market);
                }
            })
            ->when($filters['from_date'] ?? null, fn (Builder $q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($filters['to_date'] ?? null, fn (Builder $q, $to) => $q->whereDate('created_at', '<=', $to));
    }
}
