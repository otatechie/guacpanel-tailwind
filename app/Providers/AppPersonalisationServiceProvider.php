<?php

namespace App\Providers;

use App\Models\Personalisation;
use App\Traits\PersonalisationsHelper;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppPersonalisationServiceProvider extends ServiceProvider
{
    use PersonalisationsHelper;

    public function boot(): void
    {
        if (Schema::hasTable((new Personalisation())->getTable())) {
            $personalisation = $this->getPersonalisations();

            // Share with Blade views
            View::share('personalisation', $personalisation);

            // Share with Inertia
            Inertia::share([
                'app' => [
                    'version' => config('app.version'),
                    'name' => config('app.name'),
                ],
                'personalisation' => fn() => $personalisation,
            ]);
        }
    }
}
