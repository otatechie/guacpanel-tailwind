<?php

namespace App\Providers;

use App\Models\Personalisation;
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
                'name' => config('app.name'),
            ],
            'personalisation' => fn () => $personalisation
        ]);
    }
}
