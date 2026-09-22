<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(! $this->app->isProduction());
        Paginator::useTailwind();

        // Super Admin bypass: automatically authorize all abilities
        Gate::before(function (User $user, string $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }

            return null;
        });

        // Dynamic Gate check for defined permissions
        Gate::after(function (User $user, string $ability, ?bool $result, array $arguments = []) {
            if ($result !== null) {
                return $result;
            }

            return $user->hasPermission($ability);
        });
    }
}
