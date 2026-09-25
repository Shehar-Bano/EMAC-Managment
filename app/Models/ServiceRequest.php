<?php

namespace App\Models;

use App\Enums\ServiceRequestPriority;
use App\Enums\ServiceRequestStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequest extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'service_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'user_address_id',
        'description',
        'property_information',
        'preferred_service_date',
        'preferred_service_time',
        'priority',
        'additional_notes',
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
            'preferred_service_date' => 'date',
            'priority' => ServiceRequestPriority::class,
            'status' => ServiceRequestStatus::class,
        ];
    }

    /**
     * Customer who submitted this request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Selected address/location for this service request.
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class, 'user_address_id');
    }

    /**
     * Attached photographs for this request.
     */
    public function photographs(): HasMany
    {
        return $this->hasMany(ServiceRequestPhotograph::class, 'service_request_id');
    }

    /**
     * Attached videos for this request.
     */
    public function videos(): HasMany
    {
        return $this->hasMany(ServiceRequestVideo::class, 'service_request_id');
    }

    /**
     * All quotes generated for this request.
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'service_request_id');
    }

    /**
     * The latest quote generated for this request.
     */
    public function latestQuote(): HasOne
    {
        return $this->hasOne(Quote::class, 'service_request_id')->latestOfMany();
    }

    /**
     * Scope query to only pending requests.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ServiceRequestStatus::PENDING);
    }

    /**
     * Scope a query to apply filters.
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function (Builder $q, $search) {
                $q->where(function (Builder $sub) use ($search) {
                    $sub->where('description', 'like', "%{$search}%")
                        ->orWhere('property_information', 'like', "%{$search}%")
                        ->orWhere('additional_notes', 'like', "%{$search}%")
                        ->orWhereHas('user', function (Builder $uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                });
            })
            ->when($filters['status'] ?? null, function (Builder $q, $status) {
                if ($status !== 'all') {
                    $q->where('status', $status);
                }
            })
            ->when($filters['priority'] ?? null, function (Builder $q, $priority) {
                if ($priority !== 'all') {
                    $q->where('priority', $priority);
                }
            })
            ->when($filters['date_from'] ?? null, function (Builder $q, $dateFrom) {
                $q->whereDate('preferred_service_date', '>=', $dateFrom);
            })
            ->when($filters['date_to'] ?? null, function (Builder $q, $dateTo) {
                $q->whereDate('preferred_service_date', '<=', $dateTo);
            });
    }
}
