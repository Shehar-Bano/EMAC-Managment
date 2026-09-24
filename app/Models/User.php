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
        return $this->hasMany(UserAddress::class, 'user_id');
    }

    /**
     * Service requests created by the user.
     */
    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'user_id');
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
                if ($this->avatar && file_exists(public_path('storage/'.$this->avatar))) {
                    return asset('storage/'.$this->avatar);
                }

                $name = urlencode($this->name);

                return "https://ui-avatars.com/api/?name={$name}&color=C5A059&background=111827&bold=true";
            }
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
