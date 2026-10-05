<?php

namespace App\Models;

use App\Enums\AccountStatus;
use App\Enums\AuthSource;
use App\Enums\ProfileStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'password',
        'role',
        'source',
        'status',
        'account_status',
        'profile_status',
        'avatar',
        'email_verified_at',
        'phone_verified_at',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'account_status' => AccountStatus::class,
            'profile_status' => ProfileStatus::class,
            'source' => AuthSource::class,
        ];
    }

    /**
     * Roles assigned to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Multiple addresses for the user.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class, 'user_id')
            ->orderByDesc('is_primary')
            ->orderBy('id');
    }

    /**
     * Service requests created by the user.
     */
    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'user_id');
    }

    /**
     * Quotes issued for or received by the user.
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'user_id');
    }

    /**
     * Social accounts linked to the user.
     */
    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * OTP records for the user.
     */
    public function otps(): HasMany
    {
        return $this->hasMany(Otp::class);
    }

    /**
     * Password reset authorization records.
     */
    public function passwordResetAuthorizations(): HasMany
    {
        return $this->hasMany(PasswordResetAuthorization::class);
    }

    /**
     * Check if user is Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    /**
     * Check if user has a specific role (or one of several roles).
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        return $this->roles->pluck('slug')->intersect($roles)->isNotEmpty();
    }

    /**
     * Check if user has permission.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->roles->flatMap->permissions->pluck('name')->contains($permission);
    }

    /**
     * Avatar URL attribute with gold theme fallback initials.
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! empty($this->avatar)) {
                    if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://') || str_starts_with($this->avatar, 'data:image/')) {
                        return $this->avatar;
                    }

                    return asset('storage/'.ltrim($this->avatar, '/'));
                }

                $name = trim($this->name ?? 'User');
                $words = preg_split('/\s+/', $name) ?: [];
                $initials = '';
                if (! empty($words[0])) {
                    $initials .= mb_substr($words[0], 0, 1);
                }
                if (isset($words[1]) && ! empty($words[1])) {
                    $initials .= mb_substr($words[1], 0, 1);
                } elseif (mb_strlen($name) >= 2) {
                    $initials = mb_substr($name, 0, 2);
                }
                $initials = strtoupper($initials ?: 'U');

                $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100">'
                    .'<rect width="100" height="100" rx="24" fill="#111827"/>'
                    .'<text x="50" y="55" font-family="-apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica, Arial, sans-serif" font-size="36" font-weight="700" fill="#C5A059" text-anchor="middle" dominant-baseline="central">'
                    .htmlspecialchars($initials)
                    .'</text>'
                    .'</svg>';

                return 'data:image/svg+xml;utf8,'.rawurlencode($svg);
            }
        );
    }

    /**
     * Image URL attribute (alias for avatarUrl).
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->avatar_url
        );
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to search users by name, email, or phone.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (blank($search)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    /**
     * Scope a query to filter users.
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
            ->when($filters['role_id'] ?? null, function (Builder $q, $roleId) {
                if ($roleId !== 'all') {
                    $q->whereHas('roles', fn ($rq) => $rq->where('roles.id', $roleId));
                }
            })
            ->when($filters['from_date'] ?? null, fn (Builder $q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($filters['to_date'] ?? null, fn (Builder $q, $to) => $q->whereDate('created_at', '<=', $to));
    }
}
