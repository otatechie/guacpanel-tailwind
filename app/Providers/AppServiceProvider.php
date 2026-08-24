<?php

namespace App\Providers;

use App\Services\EventListenerRegistrar;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(EventListenerRegistrar $eventListenerRegistrar): void
    {
        $eventListenerRegistrar->register();

        /* Password::default() was in use but defaults() was never configured,
           which left every password at Laravel's bare minimum of 8 characters.
           uncompromised() checks Have I Been Pwned by k-anonymity: only the
           first five characters of the hash are sent, so no password and no
           full hash leaves this server. Relaxed in tests, where factories use
           'password' and the lookup would be a network call per test. */
        Password::defaults(function () {
            $rule = Password::min(12);

            return $this->app->isProduction() || $this->app->environment('local') ? $rule->uncompromised() : $rule;
        });
    }
}
