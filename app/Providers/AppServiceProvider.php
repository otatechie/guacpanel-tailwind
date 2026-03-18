<?php

namespace App\Providers;

use App\Services\EventListenerRegistrar;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(EventListenerRegistrar $eventListenerRegistrar): void
    {
        $eventListenerRegistrar->register();
    }
}
