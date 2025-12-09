<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as BaseEventProvider;


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
        // Turn OFF automatic event discovery globally
        // EventServiceProvider causes conflict with L12 auto-discovery
        // BaseEventProvider::disableEventDiscovery();
    }
}
