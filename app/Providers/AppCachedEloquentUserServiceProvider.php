<?php

namespace App\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppCachedEloquentUserServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        auth()->provider('cachedEloquentUser', function (Application $application, array $config) {
            return new CachedEloquentUserProvider(
                $application['hash'],
                $config['model']
            );
        });
    }
}
