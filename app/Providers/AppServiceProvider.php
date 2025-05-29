<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Personalisation;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $personalisation = Personalisation::first() ?? new Personalisation();

            if ($personalisation->favicon && !Storage::disk('public')->exists($personalisation->favicon)) {
                $personalisation->favicon = null;
            }

            $view->with('personalisation', $personalisation);
        });

        // Share app version with Inertia
        Inertia::share([
            'app' => [
                'version' => config('app.version'),
                'name' => config('app.name'),
            ]
        ]);
    }
}
