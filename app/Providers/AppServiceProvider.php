<?php

namespace App\Providers;

use App\Models\Personalisation;
use App\Providers\CacheEloquentUserServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

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
        auth()->provider('cachedEloquent', function (Application $application, array $config) {
            return new CacheEloquentUserServiceProvider(
                $application['hash'],
                $config['model']
            );
        });

        if (Schema::hasTable((new Personalisation())->getTable())) {
            // Get personalization data
            $personalisation = Personalisation::first() ?? new Personalisation();

            if ($personalisation->favicon && !Storage::disk('public')->exists($personalisation->favicon)) {
                $personalisation->favicon = null;
            }

            // Share with Laravel views
            View::composer('*', function ($view) use ($personalisation) {
                $view->with('personalisation', $personalisation);
            });

            // Share with Inertia
            Inertia::share([
                'app' => [
                    'version' => config('app.version'),
                    'name'    => config('app.name'),
                ],
                'personalisation' => fn () => $personalisation,
            ]);
        }
    }
}
