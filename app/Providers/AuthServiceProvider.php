<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        /**
         * This overrides all permissions checks and auth middleware.
         * Best to check on permissions in most cases.
         * Use with extreme caution and intention.
         **/
        if (config('guacpanel.allow_superuser_override')) {
            Gate::before(function ($user, $ability) {
                return $user->hasRole('superuser') ? true : null;
            });
        }
    }
}
