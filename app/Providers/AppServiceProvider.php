<?php

namespace App\Providers;

use App\Models\Personalisation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Facades\Health;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Health::checks([
            UsedDiskSpaceCheck::new(),
            DatabaseCheck::new(),
            EnvironmentCheck::new(),
            DebugModeCheck::new(),
            CacheCheck::new(),
            QueueCheck::new(),
            OptimizedAppCheck::new(),
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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
