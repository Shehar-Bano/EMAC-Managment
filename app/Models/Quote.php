<?php

namespace App\Models;

use App\Enums\QuoteStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quotes';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'quote_number',
        'service_request_id',
        'user_id',
        'sent_by',
        'service_description',
        'labor_cost',
        'materials_cost',
        'equipment_cost',
        'trip_charge',
        'additional_charges',
        'discount',
        'tax_rate',
        'tax_amount',
        'total_price',
        'terms_and_conditions',
        'expires_at',
        'status',
        'customer_notes',
        'admin_notes',
        'approved_at',
        'declined_at',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => QuoteStatus::PENDING->value,
        'labor_cost' => 0.00,
        'materials_cost' => 0.00,
        'equipment_cost' => 0.00,
        'trip_charge' => 0.00,
        'additional_charges' => 0.00,
        'discount' => 0.00,
        'tax_rate' => 0.00,
        'tax_amount' => 0.00,
        'total_price' => 0.00,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'date',
            'status' => QuoteStatus::class,
            'labor_cost' => 'decimal:2',
            'materials_cost' => 'decimal:2',
            'equipment_cost' => 'decimal:2',
            'trip_charge' => 'decimal:2',
            'additional_charges' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_price' => 'decimal:2',
            'approved_at' => 'datetime',
            'declined_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Quote $quote) {
            if (empty($quote->quote_number)) {
                $count = static::withTrashed()->count() + 1;
                $quote->quote_number = 'QUO-'.date('Y').'-'.str_pad((string) $count, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Associated customer service request.
     */
    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'service_request_id');
    }

    /**
     * Customer for whom the quote was created.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Admin/Staff who sent the quote.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    /**
     * Calculate subtotal (before discount and tax).
     */
    public function getSubtotalAttribute(): float
    {
        return (float) $this->labor_cost +
            (float) $this->materials_cost +
            (float) $this->equipment_cost +
            (float) $this->trip_charge +
            (float) $this->additional_charges;
    }

    /**
     * Scope a query to apply filters.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function (Builder $q, $search) {
                $q->where(function (Builder $sub) use ($search) {
                    $sub->where('quote_number', 'like', "%{$search}%")
                        ->orWhere('service_description', 'like', "%{$search}%")
                        ->orWhereHas('user', function (Builder $uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($filters['status'] ?? null, function (Builder $q, $status) {
                if ($status !== 'all') {
                    $q->where('status', $status);
                }
            });
    }
}
