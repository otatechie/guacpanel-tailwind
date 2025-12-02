<?php

namespace App\Providers;

use App\Models\Personalisation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppPersonalisationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (!Schema::hasTable((new Personalisation())->getTable())) {
            abort(503);
        }

        $personalisation = $this->getPersonalisations();

        // Share with Blade views
        View::share('personalisation', $personalisation);

        // Share with Inertia
        Inertia::share([
            'app' => [
                'version' => config('app.version'),
                'name'    => config('app.name'),
            ],
            'personalisation' => fn () => $personalisation,
        ]);
    }

    public function getPersonalisations(bool $useCaching = true): Personalisation
    {
        $loader = fn () => tap(
            Personalisation::first() ?? new Personalisation(),
            function (Personalisation $personalisation) {
                if ($personalisation->favicon && !Storage::disk('public')->exists($personalisation->favicon)) {
                    $personalisation->favicon = null;
                }
            }
        );

        if (!$useCaching) {
            return $loader();
        }

        return Cache::rememberForever('system_settings', $loader);
    }
}
