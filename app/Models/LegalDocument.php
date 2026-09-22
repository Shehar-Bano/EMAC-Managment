<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LegalDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'slug',
        'title',
        'content',
        'version',
        'effective_date',
        'status',
        'sort_order',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
            'sort_order' => 'integer',
        ];
    }

    /**
     * User who updated the document.
     */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope active documents.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope Terms and Conditions.
     */
    public function scopeTerms(Builder $query): Builder
    {
        return $query->where('type', 'terms');
    }

    /**
     * Scope Privacy Policy.
     */
    public function scopePrivacy(Builder $query): Builder
    {
        return $query->where('type', 'privacy');
    }
}
